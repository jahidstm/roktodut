<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\BloodRequestResponse; // 🚀 নতুন মডেল ইম্পোর্ট
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // ১. গ্লোবাল কাউন্টস (Platform Health)
        $totalUsers = User::count();
        $totalDonors = User::where('role', 'donor')->count();
        $totalRequests = BloodRequest::count();
        $fulfilledRequests = BloodRequest::where('status', 'fulfilled')->count();

        // ২. সাকসেস রেট অ্যালগরিদম (Division by zero এড়ানোর জন্য সেফটি চেক)
        $successRate = $totalRequests > 0
            ? round(($fulfilledRequests / $totalRequests) * 100, 1)
            : 0;

        // ৩. ব্লাড গ্রুপ ডিমান্ড অ্যানালাইসিস (পাই-চার্টের জন্য)
        $bloodGroupDemand = BloodRequest::select('blood_group', DB::raw('count(*) as total'))
            ->groupBy('blood_group')
            ->pluck('total', 'blood_group')
            ->toArray();

        // ৪. লোকেশন-বেজড ইমার্জেন্সি ট্রেন্ড (বার-চার্টের জন্য টপ ৫ জেলা)
        // নোট: যদি তোমার ডিস্ট্রিক্ট আইডি হয়, তাহলে with('district') দিয়ে লোড করতে হতে পারে।
        $districtDemand = BloodRequest::select('district_id', DB::raw('count(*) as total'))
            ->groupBy('district_id')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'district_id')
            ->toArray();

        // 🎯 ৫. যেসব ক্লেইম ভেরিফাই করার জন্য পেন্ডিং আছে বা ডিসপুট করা হয়েছে
        $pendingClaims = BloodRequestResponse::with(['user', 'bloodRequest']) // 'user' হলো ডোনার
            ->whereIn('verification_status', ['claimed', 'disputed'])
            ->orderBy('donor_claimed_at', 'desc')
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalDonors',
            'totalRequests',
            'fulfilledRequests',
            'successRate',
            'bloodGroupDemand',
            'districtDemand',
            'pendingClaims' // 👈 নতুন ডেটা ভিউতে পাঠানো হলো
        ));
    }
}
