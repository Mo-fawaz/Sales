<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->middleware('auth:sanctum');
Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/reset-password', [AuthController::class, 'verifyResetOtp'])->middleware('auth:sanctum');
Route::post('/login', [AuthController::class, 'login']);
