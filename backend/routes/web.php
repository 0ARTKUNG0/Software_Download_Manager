<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'Software Download Manager API',
        'version' => '1.0.0',
        'endpoints' => [
            'POST /api/register' => 'Register new user',
            'POST /api/login' => 'Login user',
            'GET /api/software' => 'Get software list (protected)',
            'POST /api/download' => 'Generate download links (protected)',
        ]
    ]);
});
