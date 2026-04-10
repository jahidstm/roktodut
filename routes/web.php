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
use App\Http\Controllers\Admin\GamificationGovernanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationRecordController;
use App\Http\Controllers\OrgAdmin\DashboardController as OrgDashboardController;
use App\Http\Controllers\OrgAdmin\VerificationController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrgRegistrationController;
use App\Http\Controllers\DonationClaimController; // 🚀 কন্ট্রোলার ইম্পোর্ট
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\PublicVerificationController; // 🔐 QR Smart Card
use App\Http\Controllers\PageController;
use App\Http\Controllers\PublicBloodRequestController;
use App\Http\Controllers\BlogController;
use App\Models\Division;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────────
// 🔐 Dynamic QR Smart Card — Public Verification (NO auth middleware)
// ─────────────────────────────────────────────────────────────────────────
// throttle:60,1 → প্রতি IP থেকে প্রতি ১ মিনিটে সর্বোচ্চ ৬০ রিকোয়েস্ট।
// এর বেশি হলে Laravel স্বয়ংক্রিয়ভাবে HTTP 429 Too Many Requests ফেরত দেবে।
// Security: auth middleware ইচ্ছাকৃতভাবে নেই — QR scanner-কে login করতে হবেঠি না।
// ─────────────────────────────────────────────────────────────────────────
Route::get('/verify/{token}', [PublicVerificationController::class, 'show'])
    ->middleware('throttle:60,1')
    ->name('public.verify');



// --- ১. পাবলিক রাউটস (No Login Required) ---

// 🎯 ফিক্স: হোমপেজের ড্রপডাউনের জন্য বিভাগগুলো ডেটাবেস থেকে পাঠানো হচ্ছে
Route::get('/', function () {
    $divisions  = \App\Models\Division::all();
    $topDonors  = \App\Models\User::where('role', 'donor')
        ->notShadowbanned()
        ->where(function ($q) {
            $q->where('total_verified_donations', '>', 0)
              ->orWhere('points', '>', 0);
        })
        ->with(['badges', 'district'])
        ->orderByDesc('total_verified_donations')
        ->orderByDesc('points')
        ->limit(3)
        ->get();
    return view('home', compact('divisions', 'topDonors'));
})->name('home');

// 🛡️ ইমার্জেন্সি সার্চ এবং প্রাইভেসি শিল্ড রাউটস
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::post('/donors/{donor}/reveal/start', [DonorRevealController::class, 'start'])->name('donors.reveal.start');
Route::post('/donors/{donor}/reveal/verify', [DonorRevealController::class, 'verify'])->name('donors.reveal.verify');

// 🏆 লিডারবোর্ড (পাবলিক)
Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');

// 🪙 গ্যামিফিকেশন গাইড (পাবলিক)
Route::get('/gamification-guide', function () {
    return view('pages.gamification-guide');
})->name('gamification.guide');

// 🩸 রক্ত দিন - ল্যান্ডিং পেজ
Route::get('/donate-blood', [PageController::class, 'donateBloodInfo'])->name('pages.donate');

// 🚨 জরুরি রক্তের অনুরোধ - পাবলিক ফিড
Route::get('/urgent-requests', [PublicBloodRequestController::class, 'index'])->name('public.requests.index');

// 📰 ব্লগ রাউটস
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// --- ২. সোশ্যাল লগইন ---
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

// --- ৩. অনবোর্ডিং (শুধুমাত্র অথেনটিকেটেড) ---
Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
});

// --- ৪. ভেরিফাইড ইউজার কোর ফিচারস ---
Route::middleware(['auth'])->group(function () {

    // ব্লাড রিকোয়েস্ট ম্যানেজমেন্ট
    Route::resource('requests', BloodRequestController::class)
        ->parameters(['requests' => 'bloodRequest'])
        ->only(['index', 'create', 'store', 'show']);

    Route::post('/requests/{bloodRequest}/respond', [BloodRequestResponseController::class, 'store'])->name('requests.respond');

    // 🎯 ডোনেশন ক্লেইম রাউট (PIN or Image)
    Route::post('/responses/{response}/claim', [DonationClaimController::class, 'store'])->name('donations.claim');

    // 🎯 গ্রহীতার ভেরিফিকেশন রাউট (Approve/Dispute)
    Route::post('/responses/{response}/recipient-verify', [DonationClaimController::class, 'verifyByRecipient'])->name('donations.recipient_verify');

    Route::post('/requests/{bloodRequest}/fulfill', [BloodRequestController::class, 'fulfill'])->name('requests.fulfill');

    // ডোনার রিভিল ফ্লো (For specific blood requests only)
    Route::post('/requests/{bloodRequest}/donors/{donor}/reveal-phone', [DonorRevealController::class, 'revealPhone'])
        ->middleware('throttle:phone-reveal')
        ->name('requests.donors.reveal_phone');

    // নোটিফিকেশন ম্যানেজমেন্ট
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // প্রোফাইল ম্যানেজমেন্ট
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/emergency-mode', [ProfileController::class, 'toggleEmergencyMode'])->name('profile.emergency.toggle');

    // 🚀 Welcome Back আপডেট রাউট
    Route::post('/welcome-back/update', [ProfileController::class, 'welcomeBackUpdate'])->name('welcome_back.update');

    // 🚀 NID ডকুমেন্ট আপলোড রাউট
    Route::post('/donor/upload-nid', [ProfileController::class, 'uploadNid'])->name('donor.upload_nid');

    // রক্তদানের রেকর্ড আপডেট
    Route::post('/donation-record', [DonationRecordController::class, 'update'])->name('donation.record.update');
});

// --- ৫. ড্যাশবোর্ড রাউটস (রোল ভিত্তিক) ---

// সাধারণ ইউজার/ডোনার ড্যাশবোর্ড
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:donor,recipient'])
    ->name('dashboard');

// 🛡️ সিস্টেম অ্যাডমিন রাউটস (ইন্টিগ্রেটেড গ্রুপ)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // 🎯 অ্যাডমিন ডোনেশন ভেরিফিকেশন রাউট
    Route::post('/admin/donations/{response}/verify', [DonationClaimController::class, 'adminVerify'])->name('admin.donations.verify');

    // 🪪 সিস্টেম অ্যাডমিন NID ভেরিফিকেশন রাউট
    Route::post('/admin/users/{user}/verify-nid', [AdminDashboardController::class, 'verifyNid'])->name('admin.nid.verify');

    // 🎮 Gamification Governance মডিউল
    Route::prefix('admin/gamification')->name('admin.gamification.')->group(function () {
        Route::get('/',                          [GamificationGovernanceController::class, 'index'])->name('index');
        Route::get('/users/{user}',              [GamificationGovernanceController::class, 'show'])->name('show');
        Route::post('/users/{user}/shadowban',   [GamificationGovernanceController::class, 'toggleShadowban'])->name('shadowban');
        Route::post('/users/{user}/points',      [GamificationGovernanceController::class, 'adjustPoints'])->name('points.adjust');
        Route::post('/users/{user}/badges',      [GamificationGovernanceController::class, 'assignBadge'])->name('badges.assign');
    });
});

// --- ৬. অর্গানাইজেশন অ্যাডমিন রাউটস (ইন্টিগ্রেটেড গ্রুপ) ---
Route::middleware(['auth', 'role:org_admin'])->group(function () {

    // ড্যাশবোর্ড
    Route::get('/org/dashboard', [OrgDashboardController::class, 'index'])
        ->name('org.dashboard');

    // মেম্বার ভেরিফিকেশন স্ট্যাটাস আপডেট (Patch Route)
    Route::patch('/org/members/{donor}/verify', [OrgDashboardController::class, 'updateVerificationStatus'])->name('org.members.verify');

    // ভেরিফিকেশন ফ্লো (Review, Approve, Reject)
    Route::get('/org/donor/{id}/verify', [VerificationController::class, 'show'])->name('org.donor.verify');
    Route::post('/org/donor/{id}/approve', [VerificationController::class, 'approve'])->name('org.donor.approve');
    Route::post('/org/donor/{id}/reject', [VerificationController::class, 'reject'])->name('org.donor.reject');
});

// --- ৭. AJAX লোকেশন রাউটস ---
Route::get('/ajax/divisions', [LocationController::class, 'getDivisions']);
Route::get('/ajax/districts/{division_id}', [LocationController::class, 'getDistricts']);
Route::get('/ajax/upazilas/{district_id}', [LocationController::class, 'getUpazilas']);

// 🎯 লারাভেলের ডিফল্ট অথেনটিকেশন রাউটগুলো সবসময় ফাইলের নিচে রাখা নিরাপদ
require __DIR__ . '/auth.php';

// Organization Registration Routes
Route::middleware('guest')->group(function () {
    Route::get('/org/register', [OrgRegistrationController::class, 'create'])->name('org.register');
    Route::post('/org/register', [OrgRegistrationController::class, 'store'])->name('org.store');
});
