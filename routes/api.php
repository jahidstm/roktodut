<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationController;

// এখন থেকে এই রাউটটি হবে: /api/locations
Route::get('/locations', [LocationController::class, 'getLocations'])->name('api.locations');