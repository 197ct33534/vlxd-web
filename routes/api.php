<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/upload/image', [\App\Http\Controllers\Api\ImageUploadController::class, 'store']);
Route::post('/upload/image-url', [\App\Http\Controllers\Api\ImageUploadController::class, 'storeFromUrl']);
Route::get('/images', [\App\Http\Controllers\Api\ImageUploadController::class, 'index']);
Route::delete('/images/{id}', [\App\Http\Controllers\Api\ImageUploadController::class, 'destroy']);

