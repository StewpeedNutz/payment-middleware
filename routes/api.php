<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/checkout', [CheckoutController::class, 'store']);