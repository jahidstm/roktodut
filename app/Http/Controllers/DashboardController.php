<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\BloodRequestResponse;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ১. অনবোর্ডিং চেক
        if ($request->user() && !$request->user()->is_onboarded) {
            return redirect()->route('onboarding.show');
        }

        $user = Auth::user()->load('badges');
        $isDonor = $user->is_donor; // ✅ Use new boolean flag instead of role

        // ২. স্ট্যাটিস্টিকস ক্যালকুলেশন
        // "মোট রিকোয়েস্ট": Donor হলে responded requests, Recipient হলে created requests
        $totalRequestsMade = $isDonor
            ? BloodRequestResponse::where('user_id', $user->id)
                ->distinct('blood_request_id')
                ->count('blood_request_id')
            : BloodRequest::where('requested_by', $user->id)->count();

        // "আপনার অবদান": verified/fulfilled responses
        $totalContributions = BloodRequestResponse::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('verification_status', 'verified')
                    ->orWhereNotNull('fulfilled_at');
            })
            ->count();

        // "সফল রিকোয়েস্ট": donor এর verified/fulfilled responses
        $fulfilledRequests = BloodRequestResponse::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('verification_status', 'verified')
                    ->orWhereNotNull('fulfilled_at');
            })
            ->count();

        // "সফলতার হার": (Fulfilled Donations / Total Responses) * 100. If total responses is 0, show "N/A" (not 0%).
        $totalUserResponses = BloodRequestResponse::where('user_id', $user->id)->count();
        $successRate = $totalUserResponses > 0
            ? round(($totalContributions / $totalUserResponses) * 100, 1)
            : 'N/A';

        // ৩. সাম্প্রতিক ৫টি রিকোয়েস্টের হিস্ট্রি (Eager Loading সহ)
        $recentRequestsQuery = BloodRequest::query()
            ->withCount([
                'responses as total_responses',
                'responses as accepted_responses' => fn($q) => $q->where('status', 'accepted'),
            ]);

        if ($isDonor) {
            $recentRequestsQuery
                ->whereHas('responses', fn($q) => $q->where('user_id', $user->id)->where('status', 'accepted'))
                ->withMax([
                    'responses as my_latest_response_at' => fn($q) => $q->where('user_id', $user->id)->where('status', 'accepted'),
                ], 'created_at')
                ->orderByDesc('my_latest_response_at');
        } else {
            $recentRequestsQuery
                ->where('requested_by', $user->id)
                ->orderByDesc('created_at');
        }

        $recentRequests = $recentRequestsQuery->limit(5)->get();

        // 🎯 ৪. ডোনার হিসেবে আপনার চলমান কমিটমেন্ট (Top 3)
        $ongoingCommitments = BloodRequestResponse::where('user_id', $user->id)
            ->whereIn('verification_status', ['pending', 'claimed'])
            ->with(['bloodRequest.district', 'bloodRequest.upazila'])
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        // 🎯 ৫. ডোনেশন হিস্ট্রি (History Table)
        $donationHistory = BloodRequestResponse::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('verification_status', 'verified')
                    ->orWhereNotNull('fulfilled_at');
            })
            ->with(['bloodRequest.district', 'bloodRequest.upazila'])
            ->orderByDesc('fulfilled_at')
            ->orderByDesc('updated_at')
            ->get();

        $successfulDonationsCount = BloodRequestResponse::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('verification_status', 'verified')
                    ->orWhereNotNull('fulfilled_at');
            })
            ->count();

        $livesSaved = $totalContributions * 3;

        // ৬. গ্রহীতার রিকোয়েস্টে কোনো ডোনার 'claimed' অবস্থায় আছে কি না (পপ-আপ লজিক)
        $pendingClaim = BloodRequestResponse::whereHas('bloodRequest', function ($query) use ($user) {
            $query->where('requested_by', $user->id);
        })
            ->where('verification_status', 'claimed')
            ->with(['user', 'bloodRequest'])
            ->first();

        // 🔴 ৬. LOCAL EMERGENCY RADAR ─────────────────────────────────────────
        $radarRequests = collect();

        if ($isDonor && $user->district_id) { // ✅ Only for donors
            $userBloodGroup = $user->blood_group?->value ?? $user->blood_group;

            $urgencyOrder = ['emergency' => 0, 'urgent' => 1, 'normal' => 2];

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
                ->values();

            $radarRequests = $radarRequests->map(function ($req) {
                $phone = $req->contact_number ?? '';
                $req->masked_phone = strlen($phone) >= 6
                    ? substr($phone, 0, 3) . '****' . substr($phone, -2)
                    : '***';
                return $req;
            });
        }

        // 🏆 ৭. গ্যামিফিকেশন স্ট্যাটস
        $gamificationStats = null;
        if ($isDonor) { // ✅ Only for donors
            $currentPoints     = $user->points ?? 0;
            $totalDonations    = $user->total_verified_donations ?? 0;

            $milestones = [
                ['label' => 'Bronze Bloodline', 'bn' => 'ব্রোঞ্জ ব্লাডলাইন', 'emoji' => '🥉', 'color' => 'amber',   'donations' => 1,  'points' => 50],
                ['label' => 'Silver Savior',    'bn' => 'সিলভার সেভিয়ার',   'emoji' => '🥈', 'color' => 'slate',   'donations' => 5,  'points' => 300],
                ['label' => 'Golden Guardian',  'bn' => 'গোল্ডেন গার্ডিয়ান', 'emoji' => '🏅', 'color' => 'yellow',  'donations' => 10, 'points' => 600],
                ['label' => 'Platinum Hero',    'bn' => 'প্লাটিনাম হিরো',   'emoji' => '🏆', 'color' => 'purple',  'donations' => 20, 'points' => 1500],
            ];

            $nextMilestone = null;
            $progressPercent = 0;
            foreach ($milestones as $m) {
                if ($totalDonations < $m['donations']) {
                    $nextMilestone = $m;
                    $prevDonations = 0;
                    foreach ($milestones as $prev) {
                        if ($prev['donations'] < $m['donations']) $prevDonations = $prev['donations'];
                    }
                    $progressPercent = $prevDonations < $m['donations']
                        ? min(99, round(($totalDonations - $prevDonations) / ($m['donations'] - $prevDonations) * 100))
                        : 100;
                    break;
                }
            }

            $myRank = User::where('is_donor', true)
                ->where(fn($q) => $q->where('total_verified_donations', '>', 0)->orWhere('points', '>', 0))
                ->where(function ($q) use ($totalDonations, $currentPoints) {
                    $q->where('total_verified_donations', '>', $totalDonations)
                      ->orWhere(fn($q2) => $q2->where('total_verified_donations', $totalDonations)->where('points', '>', $currentPoints));
                })
                ->count() + 1;

            $gamificationStats = compact(
                'currentPoints', 'totalDonations', 'milestones',
                'nextMilestone', 'progressPercent', 'myRank'
            );
        }

        // ৮. Inactive Donor Popup Logic (অনেক দিন পর দেখা!)
        $showInactivePopup = false;

        if ($isDonor && $user->is_onboarded && !$user->welcome_back_checked) {
            // Rule 2 & 3: New User Check & Inactivity Check
            // অ্যাকাউন্ট অন্তত ৩০ দিনের পুরোনো হতে হবে। LogSuccessfulLogin ইভেন্ট ৩০ দিন পর welcome_back_checked অটোমেটিক false করে।
            $accountAgeDays = $user->created_at ? $user->created_at->diffInDays(now()) : 0;
            
            if ($accountAgeDays >= 30) {
                $showInactivePopup = true;
            }
        }

        return view('dashboard', compact(
            'totalRequestsMade',
            'fulfilledRequests',
            'totalContributions',
            'successRate',
            'recentRequests',
            'pendingClaim',
            'ongoingCommitments',
            'donationHistory',
            'successfulDonationsCount',
            'livesSaved',
            'gamificationStats',
            'radarRequests',
            'showInactivePopup',
            'isDonor'
        ));
    }
}
