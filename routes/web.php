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
use App\Http\Controllers\Admin\AnalyticsController;
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
use App\Http\Controllers\ChatbotController;

use App\Http\Controllers\PublicBloodRequestController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogSubmissionController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\SupportMessageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\SpamRadarController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\PwaController;
use App\Http\Controllers\HospitalController;
use App\Models\Division;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SmartCardImageController;

// ─────────────────────────────────────────────────────────────────────────
// 🔐 Dynamic QR Smart Card — Public Verification (NO auth middleware)
// ─────────────────────────────────────────────────────────────────────────
Route::get('/verify/{token}', [PublicVerificationController::class, 'show'])
    ->middleware('throttle:60,1')
    ->name('public.verify');
Route::get('/verify/{token}/download-pass', [SmartCardImageController::class, 'downloadPass'])
    ->name('public.verify.download');
Route::get('/verify/{token}/og-image.png', [SmartCardImageController::class, 'socialOg'])
    ->name('public.verify.og');

// ─────────────────────────────────────────────────────────────────────────
// 🤖 Telegram Bot Webhook (NO auth — Telegram সার্ভার থেকে আসে)
// ─────────────────────────────────────────────────────────────────────────
Route::post('/telegram/webhook', [TelegramController::class, 'webhook'])
    ->name('telegram.webhook')
    ->withoutMiddleware(['web']);



// --- ১. পাবলিক রাউটস (No Login Required) ---

// 📱 PWA Offline Fallback
Route::get('/offline', [PwaController::class, 'offline'])->name('pwa.offline');

Route::get('/', function () {
    $divisions  = \App\Models\Division::all();
    $homeRequests = \App\Models\BloodRequest::active()
        ->with(['district:id,name', 'upazila:id,name'])
        ->withCount([
            'responses as accepted_responses_count' => fn($q) => $q->where('status', 'accepted'),
            'responses as claimed_verifications_count' => fn($q) => $q->where('verification_status', 'claimed'),
            'responses as verified_verifications_count' => fn($q) => $q->where('verification_status', 'verified'),
        ])
        ->orderByRaw("FIELD(urgency, 'emergency', 'urgent', 'normal')")
        ->orderBy('needed_at', 'asc')
        ->limit(3)
        ->get();

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

    // Social proof stats (cached for 5 minutes; DB is used when cache expires)
    $impactMetrics = Cache::remember('home:impact_metrics', now()->addMinutes(5), function () {
        $verifiedDonors = \App\Models\User::where('role', 'donor')
            ->where(function ($q) {
                $q->where('nid_status', 'approved')->orWhere('verified_badge', 1);
            })
            ->count();

        return [
            'verified_donors' => $verifiedDonors,
            'total_donations' => (int) \App\Models\User::sum('total_verified_donations'),
            'total_donors' => \App\Models\User::where('role', 'donor')->count(),
        ];
    });

    $verifiedDonors = $impactMetrics['verified_donors'];
    $totalDonations = $impactMetrics['total_donations'];
    $totalDonors    = $impactMetrics['total_donors'];
    $recentPosts    = \App\Models\Post::where('status', 'published')
        ->with([
            'author:id,name,profile_image',
            'storyMeta:id,post_id,anonymize_level,is_verified_story',
            'categories:id,name',
        ])
        ->orderByDesc('published_at')
        ->limit(3)
        ->get();

    return view('home', compact('divisions', 'topDonors', 'verifiedDonors', 'totalDonations', 'totalDonors', 'homeRequests', 'recentPosts'));
})->name('home');

Route::post('/chatbot/ask', [ChatbotController::class, 'ask'])
    ->middleware('throttle:30,1')
    ->name('chatbot.ask');

Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::post('/donors/{donor}/reveal/start', [DonorRevealController::class, 'start'])->name('donors.reveal.start');
Route::post('/donors/{donor}/reveal/verify', [DonorRevealController::class, 'verify'])->name('donors.reveal.verify');

Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');

Route::get('/gamification-guide', function () {
    return view('pages.gamification-guide');
})->name('gamification.guide');

Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/privacy-policy', function () {
    return view('pages.privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('pages.terms');
})->name('terms');



Route::get('/urgent-requests', [PublicBloodRequestController::class, 'index'])->name('public.requests.index');

Route::post('/reports', [ReportController::class, 'store'])
    ->middleware('throttle:reports-submit')
    ->name('reports.store');

Route::get('/requests/create', [BloodRequestController::class, 'create'])
    ->name('requests.create');
Route::post('/requests', [BloodRequestController::class, 'store'])
    ->middleware('throttle:requests-store')
    ->name('requests.store');
Route::get('/my-requests', [BloodRequestController::class, 'myRequests'])
    ->name('requests.my-requests');
Route::post('/requests/{bloodRequest}/renew', [BloodRequestController::class, 'renew'])
    ->name('requests.renew');
Route::get('/requests/{bloodRequest}', [BloodRequestController::class, 'show'])
    ->name('requests.show');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/create', [BlogSubmissionController::class, 'create'])
    ->middleware('auth')
    ->name('blog.create');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// ─────────────────────────────────────────────────────────────────────────
// 📬 যোগাযোগ করুন — Contact Form
// POST রুটে ভিন্ন throttle: Guest (2/min), Auth (5/min)
// ─────────────────────────────────────────────────────────────────────────
Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');

// Guest: প্রতি মিনিটে সর্বোচ্চ ২ বার
Route::middleware(['guest', 'throttle:contact-guest'])
    ->post('/contact', [ContactController::class, 'store'])
    ->name('contact.store.guest');

// Auth: প্রতি মিনিটে সর্বোচ্চ ৫ বার
Route::middleware(['auth', 'throttle:contact-auth'])
    ->post('/contact', [ContactController::class, 'store'])
    ->name('contact.store');

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
        ->only(['index']);

    Route::post('/requests/{bloodRequest}/respond', [BloodRequestResponseController::class, 'store'])->name('requests.respond');
    Route::post('/responses/{response}/claim', [DonationClaimController::class, 'store'])->name('donations.claim');
    Route::post('/responses/{response}/recipient-verify', [DonationClaimController::class, 'verifyByRecipient'])->name('donations.recipient_verify');
    Route::post('/requests/{bloodRequest}/fulfill', [BloodRequestController::class, 'fulfill'])->name('requests.fulfill');
    Route::post('/requests/{bloodRequest}/report', [\App\Http\Controllers\BloodRequestReportController::class, 'store'])->name('requests.report');

    Route::post('/requests/{bloodRequest}/donors/{donor}/reveal-phone', [DonorRevealController::class, 'revealPhone'])
        ->middleware('throttle:phone-reveal')
        ->name('requests.donors.reveal_phone');

    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::get('/notifications/recent', [NotificationController::class, 'getRecent'])->name('notifications.recent');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/upgrade-to-donor', [ProfileController::class, 'upgradeToDonor'])->name('profile.upgrade_to_donor');
    Route::post('/profile/availability', [ProfileController::class, 'toggleEmergencyMode'])->name('donor_profile.is_available_now');
    Route::post('/profile/toggle-hide-phone', [ProfileController::class, 'toggleHidePhone'])->name('profile.toggle.hide_phone');

    Route::post('/welcome-back/update', [ProfileController::class, 'welcomeBackUpdate'])->name('welcome_back.update');
    Route::post('/donor/upload-nid', [ProfileController::class, 'uploadNid'])->name('donor.upload_nid');
    Route::get('/donor/{id}/nid-document', [ProfileController::class, 'viewNid'])->name('donor.view_nid');
    Route::post('/blog', [BlogSubmissionController::class, 'store'])->name('blog.store');
    Route::post('/donation-record', [DonationRecordController::class, 'update'])->name('donation.record.update');
    Route::get('/donations/{response}/proof', [DonationClaimController::class, 'viewProof'])->name('donations.proof');

    // 📍 Geospatial: ডোনারের GPS লোকেশন সেভ করা
    Route::post('/profile/location', [ProfileController::class, 'updateLocation'])->name('profile.location.update');

    // 🤖 Telegram Bot: কানেক্ট ও ডিসকানেক্ট
    Route::get('/telegram/connect', [TelegramController::class, 'generateConnectLink'])->name('telegram.connect');
    Route::post('/telegram/disconnect', [TelegramController::class, 'disconnect'])->name('telegram.disconnect');
});


// --- ৫. ড্যাশবোর্ড রাউটস (রোল ভিত্তিক) ---
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:donor,recipient'])
    ->name('dashboard');

// 🛡️ সিস্টেম অ্যাডমিন রাউটস (ইন্টিগ্রেটেড গ্রুপ)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/donations/proof-reviews', [AdminDashboardController::class, 'proofReviews'])->name('admin.donations.proof_reviews');
    Route::get('/admin/verification/nid-reviews', [AdminDashboardController::class, 'nidReviews'])->name('admin.nid.reviews');
    Route::get('/admin/verification/organization-reviews', [AdminDashboardController::class, 'organizationReviews'])->name('admin.org.reviews');
    Route::get('/admin/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics.index');
    Route::get('/admin/analytics/export', [AnalyticsController::class, 'export'])->name('admin.analytics.export');
    Route::get('/admin/audit-logs', [AuditLogController::class, 'index'])->name('admin.audit-logs.index');
    Route::get('/admin/nid/{user}/image', [ProfileController::class, 'viewNidForAdmin'])
        ->middleware('signed')
        ->name('admin.nid.image');
    Route::post('/admin/donations/{response}/verify', [DonationClaimController::class, 'adminVerify'])->name('admin.donations.verify');
    Route::post('/admin/users/{user}/verify-nid', [AdminDashboardController::class, 'verifyNid'])->name('admin.nid.verify');
    Route::post('/admin/orgs/{organization}/verify', [AdminDashboardController::class, 'verifyOrg'])->name('admin.org.verify');
    Route::get('/admin/orgs/{organization}/document', [AdminDashboardController::class, 'viewOrgDocument'])->name('admin.org.document');

    // 🛑 Spam Radar Routes
    Route::get('/admin/spam-radar', [SpamRadarController::class, 'index'])->name('admin.spam-radar.index');
    Route::post('/admin/spam-radar/{bloodRequest}/approve', [SpamRadarController::class, 'approveStrike'])->name('admin.spam-radar.approve');
    Route::post('/admin/spam-radar/{bloodRequest}/reject', [SpamRadarController::class, 'rejectReports'])->name('admin.spam-radar.reject');

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

    // 📬 সাপোর্ট / যোগাযোগ বার্তা — Admin Inbox
    Route::prefix('admin/support/messages')->name('admin.support.messages.')->group(function () {
        Route::get('/',          [SupportMessageController::class, 'index'])->name('index');
        Route::get('/{message}', [SupportMessageController::class, 'show'])->name('show');
        Route::post('/{message}/status', [SupportMessageController::class, 'updateStatus'])->name('status');
    });

    Route::prefix('admin/reports')->name('admin.reports.')->group(function () {
        Route::get('/', [AdminReportController::class, 'index'])->name('index');
        Route::get('/{report}', [AdminReportController::class, 'show'])->name('show');
        Route::post('/{report}/status', [AdminReportController::class, 'updateStatus'])->name('status');
    });
});

// --- ৬. অর্গানাইজেশন অ্যাডমিন রাউটস (ইন্টিগ্রেটেড গ্রুপ) ---
Route::middleware(['auth', 'role:org_admin'])->group(function () {
    Route::get('/org/dashboard', [OrgDashboardController::class, 'index'])->name('org.dashboard');
    Route::patch('/org/members/{donor}/verify', [OrgDashboardController::class, 'updateVerificationStatus'])->name('org.members.verify');
    Route::get('/org/donor/{id}/verify', [VerificationController::class, 'show'])->name('org.donor.verify');
    Route::get('/org/nid/{user}/image', [ProfileController::class, 'viewNidForOrg'])
        ->middleware('signed')
        ->name('org.nid.image');
    Route::post('/org/donor/{id}/approve', [VerificationController::class, 'approve'])->name('org.donor.approve');
    Route::post('/org/donor/{id}/reject', [VerificationController::class, 'reject'])->name('org.donor.reject');

    // Blood Requests & Broadcast
    Route::get('/org/requests', [\App\Http\Controllers\OrgAdmin\BloodRequestController::class, 'index'])->name('org.requests.index');
    Route::post('/org/requests/{bloodRequest}/broadcast', [\App\Http\Controllers\OrgAdmin\BloodRequestController::class, 'broadcast'])->name('org.requests.broadcast');

    // Campaigns/Camps
    Route::get('/org/camps', [\App\Http\Controllers\OrgAdmin\BloodCampController::class, 'index'])->name('org.camps.index');
    Route::get('/org/camps/create', [\App\Http\Controllers\OrgAdmin\BloodCampController::class, 'create'])->name('org.camps.create');
    Route::post('/org/camps', [\App\Http\Controllers\OrgAdmin\BloodCampController::class, 'store'])->name('org.camps.store');
    Route::get('/org/camps/{camp}/edit', [\App\Http\Controllers\OrgAdmin\BloodCampController::class, 'edit'])->name('org.camps.edit');
    Route::put('/org/camps/{camp}', [\App\Http\Controllers\OrgAdmin\BloodCampController::class, 'update'])->name('org.camps.update');
    Route::post('/org/camps/{camp}/publish', [\App\Http\Controllers\OrgAdmin\BloodCampController::class, 'publish'])->name('org.camps.publish');
    Route::post('/org/camps/{camp}/cancel', [\App\Http\Controllers\OrgAdmin\BloodCampController::class, 'cancel'])->name('org.camps.cancel');
    Route::get('/org/camps/{camp}', [\App\Http\Controllers\OrgAdmin\BloodCampController::class, 'show'])->name('org.camps.show');
    Route::post('/org/camps/{camp}/attendance', [\App\Http\Controllers\OrgAdmin\BloodCampController::class, 'logAttendance'])->name('org.camps.attendance');
});

// --- ৭. AJAX লোকেশন রাউটস ---
Route::get('/ajax/divisions', [LocationController::class, 'getDivisions']);
Route::get('/ajax/districts/{division_id}', [LocationController::class, 'getDistricts']);
Route::get('/ajax/upazilas/{district_id}', [LocationController::class, 'getUpazilas']);

// --- ৮. হসপিটাল অটোকমপ্লিট API ---
Route::get('/api/hospitals/search', [HospitalController::class, 'search'])
    ->name('hospitals.search')
    ->middleware('throttle:60,1');
Route::post('/api/hospitals', [HospitalController::class, 'store'])
    ->name('hospitals.store')
    ->middleware('throttle:3,1');

// --- অ্যাডমিন হসপিটাল ম্যানেজমেন্ট ---
Route::middleware(['auth', 'role:admin'])->prefix('admin/hospitals')->name('admin.hospitals.')->group(function () {
    Route::get('/unverified', [HospitalController::class, 'unverified'])->name('unverified');
    Route::patch('/{hospital}/merge', [HospitalController::class, 'merge'])->name('merge');
    Route::patch('/{hospital}/verify', [HospitalController::class, 'verify'])->name('verify');
    Route::delete('/{hospital}', [HospitalController::class, 'destroy'])->name('destroy');
});

require __DIR__ . '/auth.php';

// Organization Registration Routes
Route::middleware('guest')->group(function () {
    Route::get('/org/register', [OrgRegistrationController::class, 'create'])->name('org.register');
    Route::post('/org/register', [OrgRegistrationController::class, 'store'])->name('org.store');
});
