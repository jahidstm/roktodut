<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaderboardController extends Controller
{
    public function __construct(private GamificationService $gamification) {}

    public function index(Request $request)
    {
        $scope      = $request->get('scope', 'national');
        $districtId = $request->get('district_id');
        $period     = $request->get('period', 'all_time');
        $limit      = min((int) $request->get('limit', 10), 50); // টপ ১০ ডিফল্ট, সর্বোচ্চ ৫০

        $districts = District::orderBy('name')->get();
        $donors    = $this->gamification->getLeaderboard($scope, $districtId, $period, $limit);

        // ─── বর্তমান লগইন ইউজারের র‌্যাঙ্ক ও পয়েন্ট ──────────────────
        $myRank   = null;
        $myPoints = null;

        if (Auth::check()) {
            $authUser = Auth::user();

            // পুরো লিডারবোর্ডে ইউজার কতজনের পরে আছে তা গণনা করি
            $baseQuery = User::where('role', 'donor')
                ->notShadowbanned()
                ->where(function ($q) {
                    $q->where('total_verified_donations', '>', 0)
                      ->orWhere('points', '>', 0);
                });

            // Scope ফিল্টার
            if ($scope === 'district' && $districtId) {
                $baseQuery->where('district_id', $districtId);
            }

            if ($period === 'monthly') {
                $currentMonth = now()->format('Y-m');
                $myPoints     = $authUser->monthly_points_month === $currentMonth
                    ? ($authUser->monthly_points ?? 0)
                    : 0;

                // monthly_points এর চেয়ে বেশি যারা আছে তাদের সংখ্যা + 1
                $myRank = $baseQuery
                    ->where('monthly_points_month', $currentMonth)
                    ->where('monthly_points', '>', $myPoints)
                    ->count() + 1;
            } else {
                $myPoints = $authUser->points ?? 0;

                // all-time: total_verified_donations > আমার, অথবা donations same কিন্তু points বেশি
                $myRank = $baseQuery->where(function ($q) use ($authUser) {
                    $q->where('total_verified_donations', '>', $authUser->total_verified_donations ?? 0)
                      ->orWhere(function ($q2) use ($authUser) {
                          $q2->where('total_verified_donations', $authUser->total_verified_donations ?? 0)
                             ->where('points', '>', $authUser->points ?? 0);
                      });
                })->count() + 1;
            }
        }

        // ─── বর্তমান মাস বাংলায় ─────────────────────────────────────
        $monthNames = [
            1 => 'জানুয়ারি', 2 => 'ফেব্রুয়ারি', 3 => 'মার্চ',    4 => 'এপ্রিল',
            5 => 'মে',        6 => 'জুন',         7 => 'জুলাই',    8 => 'আগস্ট',
            9 => 'সেপ্টেম্বর', 10 => 'অক্টোবর',  11 => 'নভেম্বর', 12 => 'ডিসেম্বর',
        ];
        $currentMonth = $monthNames[(int) now()->format('n')] . ' ' . now()->format('Y');

        return view('leaderboard', compact(
            'donors',
            'districts',
            'scope',
            'districtId',
            'period',
            'currentMonth',
            'myRank',
            'myPoints',
        ));
    }
}
