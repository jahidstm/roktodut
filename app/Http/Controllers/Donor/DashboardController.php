<?php

namespace App\Http\Controllers\Donor;

use App\Enums\BloodComponentType;
use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\BloodRequestResponse;
use App\Models\District;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function recentRequests(Request $request)
    {
        $user = Auth::user();
        if (!$user->is_donor) return redirect()->route('dashboard');

        $recentRequests = BloodRequest::query()
            ->withCount([
                'responses as total_responses',
                'responses as accepted_responses' => fn($q) => $q->where('status', 'accepted'),
            ])
            ->whereHas('responses', fn($q) => $q->where('user_id', $user->id)->where('status', 'accepted'))
            ->withMax([
                'responses as my_latest_response_at' => fn($q) => $q->where('user_id', $user->id)->where('status', 'accepted'),
            ], 'created_at')
            ->orderByDesc('my_latest_response_at')
            ->get();

        return view('donor.recent-requests', compact('recentRequests'));
    }

    public function bloodHistory(Request $request)
    {
        $user = Auth::user();
        if (!$user->is_donor) return redirect()->route('dashboard');

        $donationHistory = BloodRequestResponse::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('verification_status', 'verified')
                  ->orWhereNotNull('fulfilled_at');
            })
            ->with(['bloodRequest.district', 'bloodRequest.upazila', 'bloodRequest.hospital'])
            ->orderByDesc('fulfilled_at')
            ->orderByDesc('updated_at')
            ->get();

        return view('donor.blood-history', compact('donationHistory'));
    }

    public function index(Request $request)
    {
        $user = Auth::user()->load('badges');

        // ── Guard: শুধুমাত্র Donor দের জন্য ──────────────────────────────────
        if (!$user->is_donor) {
            return redirect()->route('dashboard');
        }

        // ── Onboarding check ──────────────────────────────────────────────────
        if (!$user->is_onboarded) {
            return redirect()->route('onboarding.show');
        }

        // ── ১. Gamification Stats ─────────────────────────────────────────────
        $currentPoints  = $user->points ?? 0;
        $totalDonations = $user->total_verified_donations ?? 0;

        $milestones = [
            ['label' => 'Bronze Bloodline', 'bn' => 'ব্রোঞ্জ ব্লাডলাইন', 'emoji' => '🥉', 'color' => 'amber',  'donations' => 1,  'points' => 50],
            ['label' => 'Silver Savior',    'bn' => 'সিলভার সেভিয়ার',   'emoji' => '🥈', 'color' => 'slate',  'donations' => 5,  'points' => 300],
            ['label' => 'Golden Guardian',  'bn' => 'গোল্ডেন গার্ডিয়ান', 'emoji' => '🏅', 'color' => 'yellow', 'donations' => 10, 'points' => 600],
            ['label' => 'Platinum Hero',    'bn' => 'প্লাটিনাম হিরো',   'emoji' => '🏆', 'color' => 'purple', 'donations' => 20, 'points' => 1500],
        ];

        $nextMilestone   = null;
        $progressPercent = 0;
        foreach ($milestones as $m) {
            if ($totalDonations < $m['donations']) {
                $nextMilestone   = $m;
                $progressPercent = max(0, min(99, round(($totalDonations / $m['donations']) * 100)));
                break;
            }
        }

        $myRank = \App\Models\User::where('is_donor', true)
            ->where(fn($q) => $q->where('total_verified_donations', '>', 0)->orWhere('points', '>', 0))
            ->where(function ($q) use ($totalDonations, $currentPoints) {
                $q->where('total_verified_donations', '>', $totalDonations)
                  ->orWhere(fn($q2) => $q2->where('total_verified_donations', $totalDonations)->where('points', '>', $currentPoints));
            })
            ->count() + 1;

        $gamificationStats = compact(
            'currentPoints',
            'totalDonations',
            'milestones',
            'nextMilestone',
            'progressPercent',
            'myRank'
        );

        // ── ২. Impact Stats ───────────────────────────────────────────────────
        $totalContributions = BloodRequestResponse::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('verification_status', 'verified')
                  ->orWhereNotNull('fulfilled_at');
            })
            ->count();

        $totalRequestsMade = BloodRequestResponse::where('user_id', $user->id)
            ->distinct('blood_request_id')
            ->count('blood_request_id');

        $fulfilledRequests = $totalContributions;

        $totalUserResponses = BloodRequestResponse::where('user_id', $user->id)->count();
        $successRate = $totalUserResponses > 0
            ? round(($totalContributions / $totalUserResponses) * 100, 1)
            : 'তথ্য নেই';

        // ── ৩. Donation Recovery Cards ────────────────────────────────────────
        $donationRecoveryCards = collect([
            ['component_key' => BloodComponentType::WHOLE_BLOOD->value, 'title' => 'পূর্ণ রক্ত / PRBC', 'max_cooldown_days' => 120],
            ['component_key' => BloodComponentType::PLASMA->value,      'title' => 'প্লাজমা',            'max_cooldown_days' => 28],
            ['component_key' => BloodComponentType::PLATELETS->value,   'title' => 'প্লাটিলেট',          'max_cooldown_days' => 14],
        ])->map(function (array $item) use ($user) {
            $remainingDays = $user->daysUntilNextDonation($item['component_key']);
            $progress      = (int) round((($item['max_cooldown_days'] - $remainingDays) / $item['max_cooldown_days']) * 100);
            $progress      = max(0, min(100, $progress));
            $eligibleOn    = now()->copy()->addDays($remainingDays)->startOfDay();
            $isReady       = $remainingDays === 0;

            return [
                ...$item,
                'remaining_days'        => $remainingDays,
                'eligible_on'           => $eligibleOn,
                'eligible_on_formatted' => $eligibleOn->format('d M, Y'),
                'progress_percent'      => $progress,
                'is_ready'              => $isReady,
                'state_text'            => $isReady ? 'রক্তদানের জন্য প্রস্তুত' : "{$remainingDays} দিনের মধ্যে উপলব্ধ",
                'bar_class'             => $isReady ? 'bg-emerald-500' : 'bg-amber-500',
                'text_class'            => $isReady ? 'text-emerald-600' : 'text-amber-600',
            ];
        })->values();

        // ── ৪. Local Emergency Radar ──────────────────────────────────────────
        $radarRequests = collect();
        if ($user->district_id) {
            $userBloodGroup = $user->blood_group?->value ?? $user->blood_group;
            $urgencyOrder   = ['emergency' => 0, 'urgent' => 1, 'normal' => 2];

            $radarRequests = BloodRequest::active()
                ->where('district_id', $user->district_id)
                ->where('requested_by', '!=', $user->id)
                ->with(['district:id,name', 'upazila:id,name'])
                ->get()
                ->sortBy(function ($req) use ($userBloodGroup, $urgencyOrder) {
                    $reqGroup = $req->blood_group?->value ?? $req->blood_group;
                    $isMatch  = ($reqGroup === $userBloodGroup) ? 0 : 1;
                    $urgency  = $urgencyOrder[$req->urgency?->value ?? $req->urgency ?? 'normal'] ?? 2;
                    $timeVal  = $req->needed_at ? $req->needed_at->timestamp : PHP_INT_MAX;
                    return [$isMatch, $urgency, $timeVal];
                })
                ->take(6)
                ->values()
                ->map(function ($req) {
                    $phone = $req->contact_number ?? '';
                    $req->masked_phone = strlen($phone) >= 6
                        ? substr($phone, 0, 3) . '****' . substr($phone, -2)
                        : '***';
                    return $req;
                });
        }

        // ── ৫. My Ongoing Commitments ─────────────────────────────────────────
        $ongoingCommitments = BloodRequestResponse::where('user_id', $user->id)
            ->whereIn('verification_status', ['pending', 'claimed'])
            ->with(['bloodRequest.district', 'bloodRequest.upazila', 'bloodRequest.hospital'])
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        // ── ৬. Donation History ───────────────────────────────────────────────
        $donationHistory = BloodRequestResponse::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('verification_status', 'verified')
                  ->orWhereNotNull('fulfilled_at');
            })
            ->with(['bloodRequest.district', 'bloodRequest.upazila', 'bloodRequest.hospital'])
            ->orderByDesc('fulfilled_at')
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        // ── ৭. Recent Requests (Responded by this donor, accepted status) ─────
        $recentRequests = BloodRequest::query()
            ->withCount([
                'responses as total_responses',
                'responses as accepted_responses' => fn($q) => $q->where('status', 'accepted'),
            ])
            ->whereHas('responses', fn($q) => $q->where('user_id', $user->id)->where('status', 'accepted'))
            ->withMax([
                'responses as my_latest_response_at' => fn($q) => $q->where('user_id', $user->id)->where('status', 'accepted'),
            ], 'created_at')
            ->orderByDesc('my_latest_response_at')
            ->limit(5)
            ->get();

        // ── ৮. Pending Claim Popup (recipient confirmed?) ─────────────────────
        $pendingClaim = BloodRequestResponse::whereHas('bloodRequest', function ($q) use ($user) {
                $q->where('requested_by', $user->id);
            })
            ->where('verification_status', 'claimed')
            ->with(['user', 'bloodRequest'])
            ->first();

        // ── ৯. Referral Code ─────────────────────────────────────────────────
        $gamification = app(GamificationService::class);
        $myCode       = $gamification->generateReferralCode($user);
        $referralLink = url('/register?ref=' . $myCode);

        // ── ১১. Welcome-back popup flag ───────────────────────────────────────
        $showInactivePopup = false;
        if ($user->is_onboarded && !$user->welcome_back_checked) {
            $accountAgeDays = $user->created_at ? $user->created_at->diffInDays(now()) : 0;
            if ($accountAgeDays >= 30) {
                $showInactivePopup = true;
            }
        }

        return view('donor.dashboard', compact(
            'user',
            'gamificationStats',
            'totalRequestsMade',
            'totalContributions',
            'fulfilledRequests',
            'successRate',
            'donationRecoveryCards',
            'radarRequests',
            'ongoingCommitments',
            'donationHistory',
            'recentRequests',
            'pendingClaim',
            'myCode',
            'referralLink',
            'showInactivePopup'
        ));
    }

    public function offlineClaim(Request $request)
    {
        $user = Auth::user();
        if (!$user->is_donor) return redirect()->route('dashboard');
        if (!$user->is_onboarded) return redirect()->route('onboarding.show');

        $districts = District::orderBy('name')->get(['id', 'name']);

        return view('donor.offline-claim', compact('districts'));
    }
}
