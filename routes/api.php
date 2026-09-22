<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Endpoint ini dilindungi oleh Sanctum (Wajib ada Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
Route::post('/checkout', [CheckoutController::class, 'store']);
});

// Endpoint Webhook biasanya dibiarkan public sebab bank yang akan call terus
Route::post('/webhook/payment', [WebhookController::class, 'handle']);