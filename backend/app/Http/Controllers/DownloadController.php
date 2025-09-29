<?php

namespace App\Http\Controllers;

use App\Models\Software;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use ZipArchive;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class DownloadController extends Controller
{
    /**
     * Download single file
     */
    public function downloadSingle(Request $request, $id)
    {
        // Handle token from query parameter for file downloads
        if ($request->has('token')) {
            try {
                JWTAuth::setToken($request->token);
                JWTAuth::checkOrFail();
            } catch (\Exception $e) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }
        
        $software = Software::findOrFail($id);
        
        // Check if file exists in storage
        $filePath = 'downloads/' . $software->file_name;
        
        if (!Storage::disk('public')->exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }
        
        $fullPath = Storage::disk('public')->path($filePath);
        
        return Response::download($fullPath, $software->file_name, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }
    
    /**
     * Download multiple files as ZIP
     */
    public function downloadMultiple(Request $request)
    {
        $request->validate([
            'software_ids' => 'required|array',
            'software_ids.*' => 'exists:software,id'
        ]);
        
        $software = Software::whereIn('id', $request->software_ids)->get();
        
        if ($software->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No software found'
            ], 404);
        }
        
        // Create temporary zip file
        $zipFileName = 'software_bundle_' . time() . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $zip = new ZipArchive;
        
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($software as $item) {
                $filePath = Storage::disk('public')->path('downloads/' . $item->file_name);
                
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $item->file_name);
                }
            }
            $zip->close();
            
            // Return zip file for download
            return Response::download($zipPath, $zipFileName, [
                'Content-Type' => 'application/zip',
            ])->deleteFileAfterSend(true);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to create zip file'
        ], 500);
    }
}
