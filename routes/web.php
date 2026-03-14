<?php

use App\Http\Controllers\Auth\OnboardingController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\DonorRevealController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

require __DIR__ . '/auth.php';

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Blood Requests (UI expects these)
    Route::resource('requests', BloodRequestController::class)->only(['index', 'create', 'store', 'show']);

    Route::get('/search', [SearchController::class, 'index'])->name('search');

    Route::post('/donors/{donor}/reveal/start', [DonorRevealController::class, 'start'])->name('donors.reveal.start');
    Route::post('/donors/{donor}/reveal/verify', [DonorRevealController::class, 'verify'])->name('donors.reveal.verify');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});