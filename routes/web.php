<?php

use App\Http\Controllers\Auth\OnboardingController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\BloodRequestResponseController;
use App\Http\Controllers\DonorRevealController;
use App\Http\Controllers\NotificationController; // 👈 নতুন কন্ট্রোলার ইমপোর্ট
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Admin\AdminDashboardController;
use Illuminate\Support\Facades\Route;

// পাবলিক রাউটস
Route::get('/', fn () => view('home'))->name('home');

require __DIR__ . '/auth.php';

// সোশ্যাল লগইন রাউটস
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

// শুধুমাত্র অথেনটিকেটেড ইউজারদের জন্য (Onboarding)
Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
});

// ভেরিফাইড ইউজারদের জন্য কোর ফিচারস
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

    // ৫. ডোনার রিভিল ফ্লো (ইন্টিগ্রেটেড রাউটস)
    Route::post('/donors/{donor}/reveal/start', [DonorRevealController::class, 'start'])->name('donors.reveal.start');
    Route::post('/donors/{donor}/reveal/verify', [DonorRevealController::class, 'verify'])->name('donors.reveal.verify');
    
    Route::post('/requests/{bloodRequest}/donors/{donor}/reveal-phone', [DonorRevealController::class, 'revealPhone'])
        ->name('requests.donors.reveal_phone');

    // 🔔 ৬. নোটিফিকেশন ম্যানেজমেন্ট (নতুন অ্যাড করা হয়েছে)
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    // ৭. প্রোফাইল ম্যানেজমেন্ট
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // রক্তদানের রেকর্ড আপডেট
    Route::post('/donation-record', [\App\Http\Controllers\DonationRecordController::class, 'update'])
        ->name('donation.record.update');
});

// ড্যাশবোর্ড রাউটস (রোল ভিত্তিক)
Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:donor,recipient'])
    ->name('dashboard');

Route::get('/admin/dashboard', fn () => view('admin.dashboard'))
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.dashboard');

Route::get('/org/dashboard', fn () => view('org.dashboard'))
    ->middleware(['auth', 'verified', 'role:org_admin'])
    ->name('org.dashboard');

Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:admin']) 
    ->name('admin.dashboard');