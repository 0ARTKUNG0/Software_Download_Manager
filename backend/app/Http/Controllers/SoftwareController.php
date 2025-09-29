<?php

namespace App\Http\Controllers;

use App\Models\Software;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SoftwareController extends Controller
{
    /**
     * Get all software
     */
    public function index(Request $request)
    {
        $query = Software::query();

        // Filter by category if provided
        if ($request->has('category') && $request->category !== 'All') {
            $query->where('category', $request->category);
        }

        // Search by name if provided
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $software = $query->get();

        return response()->json([
            'success' => true,
            'software' => $software
        ]);
    }

    /**
     * Generate download links for selected software
     */
    public function download(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'software_ids' => 'required|array',
            'software_ids.*' => 'exists:software,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $software = Software::whereIn('id', $request->software_ids)->get();
        
        $downloadLinks = $software->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'download_url' => $item->download_url,
                'size' => $item->size
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Download links generated successfully',
            'downloads' => $downloadLinks
        ]);
    }
}
