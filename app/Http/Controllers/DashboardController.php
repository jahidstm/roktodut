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
        // ১. অনবোর্ডিং চেক
        if ($request->user() && !$request->user()->is_onboarded) {
            return redirect()->route('onboarding.show');
        }

        $user = Auth::user();

        // ২. স্ট্যাটিস্টিকস ক্যালকুলেশন
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

        // ৩. সাম্প্রতিক ৫টি রিকোয়েস্টের হিস্ট্রি (Eager Loading সহ)
        $recentRequests = BloodRequest::where('requested_by', $user->id)
            ->withCount([
                'responses as total_responses',
                'responses as accepted_responses' => fn($q) => $q->where('status', 'accepted')
            ])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // 🎯 ৪. ডোনার হিসেবে আপনার একসেপ্ট করা সাম্প্রতিক ৫টি ডোনেশন
        // ফিক্স: 'donor_id' এর বদলে 'user_id' ব্যবহার করা হয়েছে
        $acceptedDonations = BloodRequestResponse::where('user_id', $user->id)
            ->with(['bloodRequest'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ৫. গ্রহীতার রিকোয়েস্টে কোনো ডোনার 'claimed' অবস্থায় আছে কি না (পপ-আপ লজিক)
        $pendingClaim = BloodRequestResponse::whereHas('bloodRequest', function ($query) use ($user) {
            $query->where('requested_by', $user->id);
        })
            ->where('verification_status', 'claimed')
            ->with(['user', 'bloodRequest'])
            ->first();

        return view('dashboard', compact(
            'totalRequestsMade',
            'fulfilledRequests',
            'totalContributions',
            'successRate',
            'recentRequests',
            'pendingClaim',
            'acceptedDonations' // 👈 কম্প্যাক্টে যোগ করা হয়েছে
        ));
    }
}
