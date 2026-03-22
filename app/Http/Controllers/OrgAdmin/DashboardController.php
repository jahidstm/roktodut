<?php

namespace App\Http\Controllers\OrgAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // 🎯 এটি যুক্ত করা হয়েছে

class DashboardController extends Controller
{
    public function index()
    {
        // Auth Facade ব্যবহার করলে ইনটেলফেন্স আর এরর দেখাবে না
        $admin = Auth::user(); 

        // 🚨 Data Leak Prevention: অ্যাডমিনের নিজ অর্গানাইজেশনের আইডি
        $orgId = $admin->organization_id;

        // স্ট্যাটিস্টিক্স
        $totalMembers = User::where('organization_id', $orgId)->count();
        $verifiedMembers = User::where('organization_id', $orgId)
                                ->where('is_verified', true)
                                ->count();
        
        // পেন্ডিং ভেরিফিকেশন লিস্ট
        $pendingVerifications = User::where('organization_id', $orgId)
                                    ->where('is_verified', false)
                                    ->latest()
                                    ->take(10)
                                    ->get();

        return view('org.dashboard', compact(
            'totalMembers', 
            'verifiedMembers', 
            'pendingVerifications'
        ));
    }
}