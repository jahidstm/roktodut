<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Services\GamificationService;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function __construct(private GamificationService $gamification)
    {
    }

    public function index(Request $request)
    {
        $scope      = $request->get('scope', 'national'); // national | district
        $districtId = $request->get('district_id');
        $period     = $request->get('period', 'all_time'); // all_time | monthly

        $donors    = $this->gamification->getLeaderboard($scope, $districtId, $period, 50);
        $districts = District::orderBy('name')->get();

        // বর্তমান মাস বাংলায়
        $monthNames = [
            1 => 'জানুয়ারি', 2 => 'ফেব্রুয়ারি', 3 => 'মার্চ', 4 => 'এপ্রিল',
            5 => 'মে', 6 => 'জুন', 7 => 'জুলাই', 8 => 'আগস্ট',
            9 => 'সেপ্টেম্বর', 10 => 'অক্টোবর', 11 => 'নভেম্বর', 12 => 'ডিসেম্বর',
        ];
        $currentMonth = $monthNames[(int) now()->format('n')] . ' ' . now()->format('Y');

        return view('leaderboard', compact('donors', 'districts', 'scope', 'districtId', 'period', 'currentMonth'));
    }
}
