<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\DashboardApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Payments
    Route::apiResource('payments', PaymentApiController::class);

    // Dashboard Metrics
    Route::get('dashboard/metrics', [DashboardApiController::class, 'index']);
});
