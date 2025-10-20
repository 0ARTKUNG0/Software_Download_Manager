<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use App\Models\BundleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BundleController extends Controller
{
    /**
     * List user's bundles
     */
    public function index()
    {
        $bundles = Bundle::where('user_id', auth()->id())
            ->with('items')
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($bundle) {
                return [
                    'id' => $bundle->id,
                    'name' => $bundle->name,
                    'is_default' => $bundle->is_default,
                    'software_ids' => $bundle->items->pluck('software_id')->toArray(),
                    'item_count' => $bundle->items->count(),
                    'created_at' => $bundle->created_at,
                    'updated_at' => $bundle->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'bundles' => $bundles
        ]);
    }

    /**
     * Create new bundle
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'software_ids' => 'required|array',
            'software_ids.*' => 'exists:software,id',
            'is_default' => 'boolean'
        ]);

        DB::beginTransaction();

        try {
            // If setting as default, unset other defaults
            if ($request->input('is_default', false)) {
                Bundle::where('user_id', auth()->id())
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            $bundle = Bundle::create([
                'user_id' => auth()->id(),
                'name' => $request->name,
                'is_default' => $request->input('is_default', false),
            ]);

            // Add items with sort order
            foreach ($request->software_ids as $index => $softwareId) {
                BundleItem::create([
                    'bundle_id' => $bundle->id,
                    'software_id' => $softwareId,
                    'sort_order' => $index + 1,
                ]);
            }

            DB::commit();

            $bundle->load('items');

            return response()->json([
                'success' => true,
                'message' => 'Bundle created successfully',
                'bundle' => [
                    'id' => $bundle->id,
                    'name' => $bundle->name,
                    'is_default' => $bundle->is_default,
                    'software_ids' => $bundle->items->pluck('software_id')->toArray(),
                    'item_count' => $bundle->items->count(),
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create bundle: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show bundle details
     */
    public function show($id)
    {
        $bundle = Bundle::with('software')->findOrFail($id);

        // Check ownership
        if ($bundle->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'bundle' => [
                'id' => $bundle->id,
                'name' => $bundle->name,
                'is_default' => $bundle->is_default,
                'software_ids' => $bundle->items->pluck('software_id')->toArray(),
                'software' => $bundle->software,
                'item_count' => $bundle->items->count(),
                'created_at' => $bundle->created_at,
                'updated_at' => $bundle->updated_at,
            ]
        ]);
    }

    /**
     * Update bundle
     */
    public function update(Request $request, $id)
    {
        $bundle = Bundle::findOrFail($id);

        // Check ownership
        if ($bundle->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'software_ids' => 'sometimes|array',
            'software_ids.*' => 'exists:software,id',
            'is_default' => 'sometimes|boolean'
        ]);

        DB::beginTransaction();

        try {
            // If setting as default, unset other defaults
            if ($request->has('is_default') && $request->is_default) {
                Bundle::where('user_id', auth()->id())
                    ->where('id', '!=', $bundle->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            // Update bundle
            $bundle->update($request->only(['name', 'is_default']));

            // Update items if provided
            if ($request->has('software_ids')) {
                // Delete old items
                BundleItem::where('bundle_id', $bundle->id)->delete();

                // Add new items
                foreach ($request->software_ids as $index => $softwareId) {
                    BundleItem::create([
                        'bundle_id' => $bundle->id,
                        'software_id' => $softwareId,
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            DB::commit();

            $bundle->load('items');

            return response()->json([
                'success' => true,
                'message' => 'Bundle updated successfully',
                'bundle' => [
                    'id' => $bundle->id,
                    'name' => $bundle->name,
                    'is_default' => $bundle->is_default,
                    'software_ids' => $bundle->items->pluck('software_id')->toArray(),
                    'item_count' => $bundle->items->count(),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update bundle: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete bundle
     */
    public function destroy($id)
    {
        $bundle = Bundle::findOrFail($id);

        // Check ownership
        if ($bundle->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $bundle->delete(); // Cascade delete will remove items

        return response()->json([
            'success' => true,
            'message' => 'Bundle deleted successfully'
        ]);
    }
}
