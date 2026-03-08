<?php

use App\Http\Controllers\DonorRevealController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\OnboardingController;

Route::get('/', function () {
    return view('welcome');
});

// Donor/Recipient dashboard only
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'role:donor,recipient'])->name('dashboard');

// Admin dashboard
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified', 'role:admin'])->name('admin.dashboard');

// Org Admin dashboard
Route::get('/org/dashboard', function () {
    return view('org.dashboard');
})->middleware(['auth', 'verified', 'role:org_admin'])->name('org.dashboard');

// Search (AUTH_REQUIRED)
Route::get('/search', [SearchController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('search');

// Reveal endpoints (AUTH_REQUIRED)
Route::post('/donors/{donor}/reveal/start', [DonorRevealController::class, 'start'])
    ->middleware(['auth', 'verified'])
    ->name('donors.reveal.start');

Route::post('/donors/{donor}/reveal/verify', [DonorRevealController::class, 'verify'])
    ->middleware(['auth', 'verified'])
    ->name('donors.reveal.verify');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require DIR.'/auth.php';

// সোশ্যাল লগইন রাউট
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

// অনবোর্ডিং রাউট (লগইন করা ইউজারদের জন্য)
Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
});