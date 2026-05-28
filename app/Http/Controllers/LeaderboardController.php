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
        // ─── প্যারামিটার রিড (নতুন + পুরনো backward-compat) ───────────────
        // নতুন: time=all|month | পুরনো: period=all_time|monthly
        $rawTime = $request->get('time', $request->get('period', 'all'));
        $time = match ($rawTime) {
            'monthly', 'month' => 'month',
            default            => 'all',
        };

        // নতুন: scope=bd|district | পুরনো: scope=national
        $rawScope = $request->get('scope', 'bd');
        $scope = match ($rawScope) {
            'district'         => 'district',
            default            => 'bd',
        };

        $districtId = $request->get('district_id') ? (int) $request->get('district_id') : null;
        $limit      = 50; // সর্বদা Top 50

        // ─── লোকেশন ডেটা ──────────────────────────────────────────────────
        $districts        = District::orderBy('name')->get();
        $selectedDistrict = ($scope === 'district' && $districtId)
            ? $districts->firstWhere('id', $districtId)
            : null;

        // ─── লিডারবোর্ড কোয়েরি (Cached for 10 minutes) ────────────────────
        $cacheKey = "leaderboard_{$scope}_{$districtId}_{$time}";

        [$top3, $donors] = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(10), function () use ($scope, $districtId, $time, $limit) {
            $top3 = $this->gamification->getTop3($scope, $districtId, $time);
            $donors = $this->gamification->getLeaderboard($scope, $districtId, $time, $limit);
            return [$top3, $donors];
        });

        // ─── লগইন ইউজারের র‌্যাঙ্ক ও পয়েন্ট ─────────────────────────────
        $myRank   = null;
        $myPoints = null;

        if (Auth::check()) {
            $authUser = Auth::user();
            $currentYm = now()->format('Y-m');

            $cacheKey = 'leaderboard_my_rank_' . $authUser->id . '_' . $scope . '_' . ($districtId ?? 'all') . '_' . $time . '_' . $currentYm;

            [$myRank, $myPoints] = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(3), function () use ($authUser, $scope, $districtId, $time, $currentYm) {
                $baseQuery = User::where('role', 'donor')
                    ->notShadowbanned()
                    ->where(function ($q) {
                        $q->where('total_verified_donations', '>', 0)
                          ->orWhere('points', '>', 0);
                    });

                if ($scope === 'district' && $districtId) {
                    $baseQuery->where('district_id', $districtId);
                }

                if ($time === 'month') {
                    $myPoints = $authUser->monthly_points_month === $currentYm
                        ? ($authUser->monthly_points ?? 0)
                        : 0;

                    $myRank = $baseQuery
                        ->where('monthly_points_month', $currentYm)
                        ->where('monthly_points', '>', $myPoints)
                        ->count() + 1;
                } else {
                    $myPoints = $authUser->points ?? 0;

                    $myRank = $baseQuery->where(function ($q) use ($authUser) {
                        $q->where('total_verified_donations', '>', $authUser->total_verified_donations ?? 0)
                          ->orWhere(function ($q2) use ($authUser) {
                              $q2->where('total_verified_donations', $authUser->total_verified_donations ?? 0)
                                 ->where('points', '>', $authUser->points ?? 0);
                          });
                    })->count() + 1;
                }

                return [$myRank, $myPoints];
            });
        }

        // ─── বাংলা UI লেবেল ────────────────────────────────────────────────
        $monthNames = [
            1 => 'জানুয়ারি', 2 => 'ফেব্রুয়ারি', 3 => 'মার্চ',     4 => 'এপ্রিল',
            5 => 'মে',        6 => 'জুন',          7 => 'জুলাই',     8 => 'আগস্ট',
            9 => 'সেপ্টেম্বর', 10 => 'অক্টোবর',   11 => 'নভেম্বর', 12 => 'ডিসেম্বর',
        ];
        $currentMonth = $monthNames[(int) now()->format('n')] . ' ' . now()->format('Y');

        // পয়েন্ট লেবেল (view-এ ব্যবহার হবে)
        $pointsLabel = $time === 'month' ? 'মাসিক পয়েন্ট' : 'পয়েন্ট';

        $view = view('leaderboard', compact(
            'donors',
            'top3',
            'districts',
            'scope',
            'districtId',
            'selectedDistrict',
            'time',
            'currentMonth',
            'pointsLabel',
            'myRank',
            'myPoints',
        ));

        if ($request->ajax()) {
            $sections = $view->renderSections();
            return response($sections['content'] ?? $view->render());
        }

        return $view;
    }
}
