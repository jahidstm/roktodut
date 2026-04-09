<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display the blood donation informational landing page.
     */
    public function donateBloodInfo()
    {
        $verifiedDonors = \App\Models\User::where('role', 'donor')
            ->where(function($q) {
                $q->where('nid_status', 'approved')->orWhere('verified_badge', 1);
            })->count();

        // Calculate lives saved roughly as total bags given across platforms or total fulfilled * 3.
        // We'll use total verified donations from system + base number
        $totalDonations = \App\Models\User::sum('total_verified_donations');
        $livesSaved = ($totalDonations * 3) + 120; // 120 is base offset for social proof demo

        return view('pages.donate', compact('verifiedDonors', 'livesSaved'));
    }
}
