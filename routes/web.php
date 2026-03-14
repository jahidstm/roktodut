<?php

use App\Http\Controllers\Auth\OnboardingController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\DonorRevealController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

// --- 1. Landing Page (Always Home) ---
Route::get('/', function () {
    return view('home');
})->name('home');

// --- 2. Built-in auth routes ---
require __DIR__ . '/auth.php';

// --- 3. Social login routes ---
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

// --- 4. Onboarding routes ---
Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
});

// --- 5. Verified app routes ---
Route::middleware(['auth', 'verified'])->group(function () {
    // Blood Requests (UI expects these resource routes)
    Route::resource('requests', BloodRequestController::class)->only(['index', 'create', 'store', 'show']);

    Route::get('/search', [SearchController::class, 'index'])->name('search');

    Route::post('/donors/{donor}/reveal/start', [DonorRevealController::class, 'start'])->name('donors.reveal.start');
    Route::post('/donors/{donor}/reveal/verify', [DonorRevealController::class, 'verify'])->name('donors.reveal.verify');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- 6. Dashboards (role-based) [RESTORED] ---
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'role:donor,recipient'])->name('dashboard');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified', 'role:admin'])->name('admin.dashboard');

Route::get('/org/dashboard', function () {
    return view('org.dashboard');
})->middleware(['auth', 'verified', 'role:org_admin'])->name('org.dashboard');