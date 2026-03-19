<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // ১. গ্লোবাল কাউন্টস (Platform Health)
        $totalUsers = User::count();
        $totalDonors = User::where('role', 'donor')->count(); // ডাটাবেসে role কলাম 'donor' ধরে
        $totalRequests = BloodRequest::count();
        $fulfilledRequests = BloodRequest::where('status', 'fulfilled')->count();

        // ২. সাকসেস রেট অ্যালগরিদম (Division by zero এড়ানোর জন্য সেফটি চেক)
        $successRate = $totalRequests > 0 
            ? round(($fulfilledRequests / $totalRequests) * 100, 1) 
            : 0;

        // ৩. ব্লাড গ্রুপ ডিমান্ড অ্যানালাইসিস (পাই-চার্টের জন্য)
        // ডাটাবেস লেভেলেই গ্রুপ করে কাউন্ট করা হচ্ছে, যা মেমোরি বাঁচাবে
        $bloodGroupDemand = BloodRequest::select('blood_group', DB::raw('count(*) as total'))
            ->groupBy('blood_group')
            ->pluck('total', 'blood_group')
            ->toArray();

        // ৪. লোকেশন-বেজড ইমার্জেন্সি ট্রেন্ড (বার-চার্টের জন্য টপ ৫ জেলা)
        $districtDemand = BloodRequest::select('district', DB::raw('count(*) as total'))
            ->groupBy('district')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'district')
            ->toArray();

        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalDonors', 
            'totalRequests', 
            'fulfilledRequests',
            'successRate',
            'bloodGroupDemand',
            'districtDemand'
        ));
    }
}