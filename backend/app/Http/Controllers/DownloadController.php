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
     * Clean up old temporary files
     */
    private function cleanupTempFiles()
    {
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            return;
        }
        
        $files = glob($tempDir . '/*');
        $now = time();
        
        foreach ($files as $file) {
            if (is_file($file)) {
                // Delete files older than 1 hour
                if ($now - filemtime($file) >= 3600) {
                    unlink($file);
                }
            }
        }
    }
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
        // Clean up old temporary files first
        $this->cleanupTempFiles();
        
        // Increase execution time and memory for large files
        set_time_limit(600); // 10 minutes for very large files
        ini_set('memory_limit', '1G'); // Increase memory limit
        ini_set('max_input_time', '600');
        ini_set('default_socket_timeout', '600');
        
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
        
        // Check total file size to determine strategy
        $totalSize = 0;
        $validFiles = [];
        
        foreach ($software as $item) {
            $filePath = Storage::disk('public')->path('downloads/' . $item->file_name);
            if (file_exists($filePath)) {
                $fileSize = filesize($filePath);
                $totalSize += $fileSize;
                $validFiles[] = [
                    'path' => $filePath,
                    'name' => $item->file_name,
                    'size' => $fileSize
                ];
            }
        }
        
        if (empty($validFiles)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid files found'
            ], 404);
        }
        
        // Create temporary zip file with unique name
        $zipFileName = 'sdm_' . time() . '_' . uniqid() . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $zip = new ZipArchive;
        
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            // Set compression level for better performance vs size trade-off
            foreach ($validFiles as $file) {
                // For large files, use store method (no compression) for speed
                if ($file['size'] > 100 * 1024 * 1024) { // Files larger than 100MB
                    $zip->addFile($file['path'], $file['name']);
                    $zip->setCompressionName($file['name'], ZipArchive::CM_STORE);
                } else {
                    $zip->addFile($file['path'], $file['name']);
                    $zip->setCompressionName($file['name'], ZipArchive::CM_DEFAULT);
                }
                
                // Add file info as comment
                $zip->setCommentName($file['name'], "Size: " . number_format($file['size']) . " bytes");
            }
            
            // Add total info comment to ZIP
            $zip->setArchiveComment("Software Download Manager Bundle\nFiles: " . count($validFiles) . "\nTotal Size: " . number_format($totalSize) . " bytes\nCreated: " . date('Y-m-d H:i:s'));
            
            $zip->close();
            
            // Check if zip was created successfully
            if (!file_exists($zipPath) || filesize($zipPath) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create zip file'
                ], 500);
            }
            
            $zipSize = filesize($zipPath);
            
            // Return zip file for download with optimized headers
            return Response::download($zipPath, 'software_bundle.zip', [
                'Content-Type' => 'application/zip',
                'Content-Length' => $zipSize,
                'Content-Disposition' => 'attachment; filename="software_bundle.zip"',
                'Cache-Control' => 'no-cache, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'Accept-Ranges' => 'bytes'
            ])->deleteFileAfterSend(true);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to create zip file - could not open zip archive'
        ], 500);
    }
}
