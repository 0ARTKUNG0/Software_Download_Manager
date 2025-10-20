<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SoftwareController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\BundleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    // User info
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Software
    Route::get('/software', [SoftwareController::class, 'index']);
    Route::post('/download', [SoftwareController::class, 'download']);
    
    // Bundles CRUD
    Route::get('/bundles', [BundleController::class, 'index']);
    Route::post('/bundles', [BundleController::class, 'store']);
    Route::get('/bundles/{bundle}', [BundleController::class, 'show']);
    Route::put('/bundles/{bundle}', [BundleController::class, 'update']);
    Route::delete('/bundles/{bundle}', [BundleController::class, 'destroy']);
    
    // File downloads
    Route::get('/download-file/{id}', [DownloadController::class, 'single']);
    Route::post('/download-multiple', [DownloadController::class, 'zip']);
    
    // Bundle downloads
    Route::post('/bundles/{bundle}/download', [DownloadController::class, 'zipBundle']);
    Route::post('/bundles/{bundle}/export-script', [DownloadController::class, 'exportScript']);
});

