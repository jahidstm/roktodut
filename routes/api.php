<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\FcmTokenController;

// এখন থেকে এই রাউটটি হবে: /api/locations
Route::get('/locations', [LocationController::class, 'getLocations'])->name('api.locations');
Route::middleware('auth:sanctum')
    ->post('/user/fcm-token', FcmTokenController::class)
    ->name('api.user.fcm-token.update');
