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
use App\Http\Controllers\Admin\BlogModerationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationRecordController;
use App\Http\Controllers\OrgAdmin\DashboardController as OrgDashboardController;
use App\Http\Controllers\OrgAdmin\VerificationController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrgRegistrationController;
use App\Http\Controllers\DonationClaimController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\PublicVerificationController;

use App\Http\Controllers\PublicBloodRequestController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogSubmissionController;
use App\Models\Division;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────────
// 🔐 Dynamic QR Smart Card — Public Verification (NO auth middleware)
// ─────────────────────────────────────────────────────────────────────────
Route::get('/verify/{token}', [PublicVerificationController::class, 'show'])
    ->middleware('throttle:60,1')
    ->name('public.verify');



// --- ১. পাবলিক রাউটস (No Login Required) ---

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

    // Social proof stats (migrated from donate page)
    $verifiedDonors = \App\Models\User::where('role', 'donor')
        ->where(function ($q) {
            $q->where('nid_status', 'approved')->orWhere('verified_badge', 1);
        })->count();
    $totalDonations = \App\Models\User::sum('total_verified_donations');
    $livesSaved     = ($totalDonations * 3) + 120;

    return view('home', compact('divisions', 'topDonors', 'verifiedDonors', 'livesSaved'));
})->name('home');

Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::post('/donors/{donor}/reveal/start', [DonorRevealController::class, 'start'])->name('donors.reveal.start');
Route::post('/donors/{donor}/reveal/verify', [DonorRevealController::class, 'verify'])->name('donors.reveal.verify');

Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');

Route::get('/gamification-guide', function () {
    return view('pages.gamification-guide');
})->name('gamification.guide');



Route::get('/urgent-requests', [PublicBloodRequestController::class, 'index'])->name('public.requests.index');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/create', [BlogSubmissionController::class, 'create'])
    ->middleware('auth')
    ->name('blog.create');
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
    Route::resource('requests', BloodRequestController::class)
        ->parameters(['requests' => 'bloodRequest'])
        ->only(['index', 'create', 'store', 'show']);

    Route::post('/requests/{bloodRequest}/respond', [BloodRequestResponseController::class, 'store'])->name('requests.respond');
    Route::post('/responses/{response}/claim', [DonationClaimController::class, 'store'])->name('donations.claim');
    Route::post('/responses/{response}/recipient-verify', [DonationClaimController::class, 'verifyByRecipient'])->name('donations.recipient_verify');
    Route::post('/requests/{bloodRequest}/fulfill', [BloodRequestController::class, 'fulfill'])->name('requests.fulfill');

    Route::post('/requests/{bloodRequest}/donors/{donor}/reveal-phone', [DonorRevealController::class, 'revealPhone'])
        ->middleware('throttle:phone-reveal')
        ->name('requests.donors.reveal_phone');

    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/emergency-mode', [ProfileController::class, 'toggleEmergencyMode'])->name('profile.emergency.toggle');

    Route::post('/welcome-back/update', [ProfileController::class, 'welcomeBackUpdate'])->name('welcome_back.update');
    Route::post('/donor/upload-nid', [ProfileController::class, 'uploadNid'])->name('donor.upload_nid');
    Route::get('/donor/{id}/nid-document', [ProfileController::class, 'viewNid'])->name('donor.view_nid');
    Route::post('/blog', [BlogSubmissionController::class, 'store'])->name('blog.store');
    Route::post('/donation-record', [DonationRecordController::class, 'update'])->name('donation.record.update');
});

// --- ৫. ড্যাশবোর্ড রাউটস (রোল ভিত্তিক) ---
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:donor,recipient'])
    ->name('dashboard');

// 🛡️ সিস্টেম অ্যাডমিন রাউটস (ইন্টিগ্রেটেড গ্রুপ)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/donations/{response}/verify', [DonationClaimController::class, 'adminVerify'])->name('admin.donations.verify');
    Route::post('/admin/users/{user}/verify-nid', [AdminDashboardController::class, 'verifyNid'])->name('admin.nid.verify');

    Route::prefix('admin/gamification')->name('admin.gamification.')->group(function () {
        Route::get('/',                          [GamificationGovernanceController::class, 'index'])->name('index');
        Route::get('/users/{user}',              [GamificationGovernanceController::class, 'show'])->name('show');
        Route::post('/users/{user}/shadowban',   [GamificationGovernanceController::class, 'toggleShadowban'])->name('shadowban');
        Route::post('/users/{user}/points',      [GamificationGovernanceController::class, 'adjustPoints'])->name('points.adjust');
        Route::post('/users/{user}/badges',      [GamificationGovernanceController::class, 'assignBadge'])->name('badges.assign');
    });

    // 📝 ব্লগ মডারেশন (ফিক্সড 라우্ট বাইন্ডিং)
    Route::prefix('admin/blog/moderation')->name('admin.blog.moderation.')->group(function () {
        Route::get('/',                  [BlogModerationController::class, 'index'])->name('index');
        Route::patch('/{post:id}/approve',  [BlogModerationController::class, 'approve'])->name('approve'); // ✅ FIX
        Route::patch('/{post:id}/reject',   [BlogModerationController::class, 'reject'])->name('reject'); // ✅ FIX
    });
});

// --- ৬. অর্গানাইজেশন অ্যাডমিন রাউটস (ইন্টিগ্রেটেড গ্রুপ) ---
Route::middleware(['auth', 'role:org_admin'])->group(function () {
    Route::get('/org/dashboard', [OrgDashboardController::class, 'index'])->name('org.dashboard');
    Route::patch('/org/members/{donor}/verify', [OrgDashboardController::class, 'updateVerificationStatus'])->name('org.members.verify');
    Route::get('/org/donor/{id}/verify', [VerificationController::class, 'show'])->name('org.donor.verify');
    Route::post('/org/donor/{id}/approve', [VerificationController::class, 'approve'])->name('org.donor.approve');
    Route::post('/org/donor/{id}/reject', [VerificationController::class, 'reject'])->name('org.donor.reject');
});

// --- ৭. AJAX লোকেশন রাউটস ---
Route::get('/ajax/divisions', [LocationController::class, 'getDivisions']);
Route::get('/ajax/districts/{division_id}', [LocationController::class, 'getDistricts']);
Route::get('/ajax/upazilas/{district_id}', [LocationController::class, 'getUpazilas']);

require __DIR__ . '/auth.php';

// Organization Registration Routes
Route::middleware('guest')->group(function () {
    Route::get('/org/register', [OrgRegistrationController::class, 'create'])->name('org.register');
    Route::post('/org/register', [OrgRegistrationController::class, 'store'])->name('org.store');
});