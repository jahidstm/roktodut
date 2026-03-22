<?php

use App\Http\Controllers\Auth\OnboardingController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\BloodRequestResponseController;
use App\Http\Controllers\DonorRevealController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationRecordController;
use Illuminate\Support\Facades\Route;

// --- পাবলিক রাউটস ---
Route::get('/', fn () => view('home'))->name('home');

require __DIR__ . '/auth.php';

// --- সোশ্যাল লগইন ---
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

// --- অনবোর্ডিং (শুধুমাত্র অথেনটিকেটেড) ---
Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
});

// --- ভেরিফাইড ইউজার কোর ফিচারস ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    // ১. ব্লাড রিকোয়েস্ট ম্যানেজমেন্ট
    Route::resource('requests', BloodRequestController::class)
        ->parameters(['requests' => 'bloodRequest'])
        ->only(['index', 'create', 'store', 'show']);

    // ২. রিকোয়েস্ট রেসপন্স (Accept/Decline)
    Route::post('/requests/{bloodRequest}/respond', [BloodRequestResponseController::class, 'store'])
        ->name('requests.respond');

    // ৩. রিকোয়েস্ট ফুলফিলমেন্ট
    Route::post('/requests/{bloodRequest}/fulfill', [BloodRequestController::class, 'fulfill'])
        ->name('requests.fulfill');

    // ৪. সার্চ ফিচার
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // ৫. ডোনার রিভিল ফ্লো (Anti-Scraping Protection Applied 🛡️)
    Route::post('/donors/{donor}/reveal/start', [DonorRevealController::class, 'start'])->name('donors.reveal.start');
    Route::post('/donors/{donor}/reveal/verify', [DonorRevealController::class, 'verify'])->name('donors.reveal.verify');
    
    // ফোন নম্বর দেখার রাউটটিতে রেট লিমিট বসানো হয়েছে
    Route::post('/requests/{bloodRequest}/donors/{donor}/reveal-phone', [DonorRevealController::class, 'revealPhone'])
        ->middleware('throttle:phone-reveal') 
        ->name('requests.donors.reveal_phone');

    // ৬. নোটিফিকেশন ম্যানেজমেন্ট
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    // ৭. প্রোফাইল ম্যানেজমেন্ট
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // রক্তদানের রেকর্ড আপডেট
    Route::post('/donation-record', [DonationRecordController::class, 'update'])->name('donation.record.update');
});

// --- ড্যাশবোর্ড রাউটস (রোল ভিত্তিক) ---

// ডোনার/রিসিপিয়েন্ট ড্যাশবোর্ড
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:donor,recipient'])
    ->name('dashboard');

// সিস্টেম অ্যাডমিন ড্যাশবোর্ড (ক্লিনড আপ)
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:admin']) 
    ->name('admin.dashboard');

// অর্গানাইজেশন অ্যাডমিন ড্যাশবোর্ড
Route::get('/org/dashboard', fn () => view('org.dashboard'))
    ->middleware(['auth', 'verified', 'role:org_admin'])
    ->name('org.dashboard');