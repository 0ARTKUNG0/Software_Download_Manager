<?php

namespace App\Http\Controllers;

use App\Models\Software;
use App\Models\Bundle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use STS\ZipStream\Facades\Zip;

class DownloadController extends Controller
{
    /**
     * Download single file with proper headers
     */
    public function single($id)
    {
        $software = Software::findOrFail($id);
        $path = "public/downloads/{$software->file_name}";

        abort_unless(Storage::exists($path), 404, 'File not found');

        $stream = Storage::readStream($path);
        $filename = $software->file_name;
        $mimeType = Storage::mimeType($path) ?? 'application/octet-stream';

        return response()->streamDownload(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, $filename, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Access-Control-Expose-Headers' => 'Content-Disposition',
        ]);
    }

    /**
     * Download multiple files as ZIP (streaming, no temp file)
     */
    public function zip(Request $request)
    {
        $request->validate([
            'software_ids' => 'required|array',
            'software_ids.*' => 'exists:software,id'
        ]);

        $files = Software::whereIn('id', $request->software_ids)->get();

        if ($files->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No software found'
            ], 404);
        }

        // Simple filename: SDM.zip
        $filename = 'SDM.zip';
        return $this->createZipStream($files, $filename);
    }

    /**
     * Download bundle as ZIP
     */
    public function zipBundle($bundleId)
    {
        $bundle = Bundle::with('software')->findOrFail($bundleId);

        // Check if user owns the bundle
        if ($bundle->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $files = $bundle->software;

        if ($files->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No software in bundle'
            ], 404);
        }

        // Format: SDM-BundleName-2025-10-20.zip
        $bundleName = \Illuminate\Support\Str::slug($bundle->name);
        $filename = "SDM-{$bundleName}-" . now()->format('Y-m-d') . '.zip';
        return $this->createZipStream($files, $filename);
    }

    /**
     * Export PowerShell script for offline install
     */
    public function exportScript($bundleId)
    {
        $bundle = Bundle::with('software')->findOrFail($bundleId);

        // Check if user owns the bundle
        if ($bundle->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $files = $bundle->software;

        if ($files->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No software in bundle'
            ], 404);
        }

        // Generate PowerShell script
        $ps1 = "# Software Download Manager - Bundle: {$bundle->name}\r\n";
        $ps1 .= "# Generated: " . now()->toDateTimeString() . "\r\n\r\n";
        $ps1 .= "\$downloadPath = \"\$env:USERPROFILE\\Downloads\\SDM_Installers\"\r\n";
        $ps1 .= "New-Item -ItemType Directory -Force -Path \$downloadPath | Out-Null\r\n";
        $ps1 .= "Write-Host \"Downloading to: \$downloadPath\" -ForegroundColor Green\r\n\r\n";

        foreach ($files as $file) {
            $url = url("/api/download-file/{$file->id}");
            $ps1 .= "# {$file->name}\r\n";
            $ps1 .= "Write-Host \"Downloading: {$file->name}...\"\r\n";
            $ps1 .= "Invoke-WebRequest -Uri \"{$url}\" -OutFile \"\$downloadPath\\{$file->file_name}\" -Headers @{\"Authorization\"=\"Bearer \$env:SDM_TOKEN\"}\r\n";
            $ps1 .= "Write-Host \"  ✓ Downloaded {$file->file_name}\" -ForegroundColor Cyan\r\n\r\n";
        }

        $ps1 .= "Write-Host \"All downloads complete!\" -ForegroundColor Green\r\n";

        $filename = \Illuminate\Support\Str::slug($bundle->name) . '-install.ps1';

        return response($ps1, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Access-Control-Expose-Headers' => 'Content-Disposition',
        ]);
    }

    /**
     * Helper: Create ZIP stream from software collection
     */
    private function createZipStream($files, $filename)
    {
        // Start with empty ZIP
        $zip = Zip::create($filename);
        
        $hasFiles = false;
        foreach ($files as $file) {
            $path = "public/downloads/{$file->file_name}";
            
            if (Storage::exists($path)) {
                // Add each file with clean name (not full path)
                $zip->add(Storage::path($path), $file->file_name);
                $hasFiles = true;
            }
        }

        if (!$hasFiles) {
            return response()->json([
                'success' => false,
                'message' => 'No files available for download'
            ], 404);
        }

        // Get the StreamedResponse and modify headers
        $response = $zip->response();
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Access-Control-Expose-Headers', 'Content-Disposition');
        
        return $response;
    }
}
