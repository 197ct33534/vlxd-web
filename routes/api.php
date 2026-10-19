<?php

use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/v1/auth/token', [AuthTokenController::class, 'store']);

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::delete('/auth/token', [AuthTokenController::class, 'destroy']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/delivery-notes/{deliveryNote}/start', [OrderController::class, 'start']);
    Route::post('/delivery-notes/{deliveryNote}/complete', [OrderController::class, 'complete']);
});
