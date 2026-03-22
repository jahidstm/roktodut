<?php

namespace App\Http\Controllers\OrgAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ১. যে ডোনাররা NID বা ডকুমেন্ট সাবমিট করেছে কিন্তু এখনো ভেরিফাই হয়নি (Pending)
        $pendingVerifications = User::where('nid_status', 'pending')
                                    ->where('role', 'donor')
                                    ->latest()
                                    ->get();

        // ২. ড্যাশবোর্ডের কিছু বেসিক স্ট্যাটিস্টিক্স
        $totalVerified = User::where('verified_badge', true)->count();
        $totalPending = $pendingVerifications->count();

        return view('org.dashboard', compact('pendingVerifications', 'totalVerified', 'totalPending'));
    }
}