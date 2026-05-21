<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InternalNlpRequestController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\FcmTokenController;

use App\Http\Controllers\Api\SpatialHeatmapController;

// এখন থেকে এই রাউটটি হবে: /api/locations
Route::get('/locations', [LocationController::class, 'getLocations'])->name('api.locations');

// Geo-Spatial Demand Heatmap API (Public — no auth required for live map data)
Route::get('/analytics/spatial-heatmap', [SpatialHeatmapController::class, 'data'])
    ->name('api.analytics.spatial-heatmap');

Route::middleware('auth:sanctum')
    ->post('/user/fcm-token', FcmTokenController::class)
    ->name('api.user.fcm-token.update');

Route::middleware('internal.secret')
    ->post('/internal/requests/nlp', [InternalNlpRequestController::class, 'store'])
    ->name('api.internal.requests.nlp.store');
