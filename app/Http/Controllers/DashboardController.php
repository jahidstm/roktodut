<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\BloodRequestResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user() && !$request->user()->is_onboarded) {
            return redirect()->route('onboarding.show');
        }

        $user = $request->user();

        // ১. স্ট্যাটিস্টিকস ক্যালকুলেশন
        $totalRequestsMade = BloodRequest::where('requested_by', $user->id)->count();

        $fulfilledRequests = BloodRequest::where('requested_by', $user->id)
            ->where('status', 'fulfilled')
            ->count();

        $totalContributions = BloodRequestResponse::where('user_id', $user->id)
            ->where('status', 'accepted')
            ->count();

        $successRate = $totalRequestsMade > 0
            ? round(($fulfilledRequests / $totalRequestsMade) * 100, 1)
            : 0;

        // ২. সাম্প্রতিক ৫টি রিকোয়েস্টের হিস্ট্রি
        $recentRequests = BloodRequest::where('requested_by', $user->id)
            ->withCount([
                'responses as total_responses',
                'responses as accepted_responses' => fn($q) => $q->where('status', 'accepted')
            ])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // 🎯 ৩. গ্রহীতার রিকোয়েস্টে কোনো ডোনার 'claimed' (unverified) অবস্থায় আছে কি না চেক করা
        // এখানে 'requested_by' ব্যবহার করা হয়েছে যাতে আপনার টেবিল স্কিমার সাথে ম্যাচ করে
        $pendingClaim = BloodRequestResponse::whereHas('bloodRequest', function ($query) use ($user) {
            $query->where('requested_by', $user->id);
        })
            ->where('verification_status', 'claimed')
            ->with(['user', 'bloodRequest']) // ডোনারের নাম ও রিকোয়েস্ট ডেটা লোড করা
            ->first();

        return view('dashboard', compact(
            'totalRequestsMade',
            'fulfilledRequests',
            'totalContributions',
            'successRate',
            'recentRequests',
            'pendingClaim' // 👈 নতুন ডেটা ভিউতে পাঠানো হলো
        ));
    }
}
