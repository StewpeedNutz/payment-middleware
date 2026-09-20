<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/checkout', [CheckoutController::class, 'store']);
Route::post('/webhook/payment', [WebhookController::class, 'handle']);