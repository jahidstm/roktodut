<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\BloodRequestResponse;
use App\Notifications\BloodResponseNotification;
use Illuminate\Http\Request;

class BloodRequestResponseController extends Controller
{
    /**
     * রিকোয়েস্টে ডোনারের রেসপন্স সেভ করা এবং নোটিফিকেশন পাঠানো
     */
    public function store(Request $request, BloodRequest $bloodRequest)
    {
        // ১. ভ্যালিডেশন
        $request->validate([
            'status' => 'required|in:accepted,declined',
        ]);

        // ২. ইউজারকে ভেরিয়েবলে নেওয়া (এডিটর এরর এড়াতে এবং কোড ক্লিন রাখতে)
        $user = $request->user();

        // 🚨 ৩. মেডিকেল কমপ্লায়েন্স চেক (৯০-দিনের রুল) 🚨
        // ইউজার যদি এক্সেপ্ট করতে চায়, তবেই এই চেকটি কাজ করবে
        if ($request->status === 'accepted' && !$user->canDonate()) {
            $remainingDays = $user->daysUntilNextDonation();
            
            return back()->with('error', "মেডিকেল গাইডলাইন অনুযায়ী আপনি আগামী {$remainingDays} দিন রক্ত দিতে পারবেন না। আপনার শরীরকে সুস্থ হওয়ার সময় দিন।");
        }

        // ৪. বেসিক পলিসি: নিজের রিকোয়েস্টে রেসপন্স করা যাবে না
        abort_if(
            $bloodRequest->requested_by === $user->id, 
            403, 
            'আপনি নিজের রিকোয়েস্টে রেসপন্স করতে পারবেন না।'
        );

        // ৫. রেসপন্স সেভ বা আপডেট করা
        $response = BloodRequestResponse::updateOrCreate(
            [
                'blood_request_id' => $bloodRequest->id,
                'user_id' => $user->id,
            ],
            [
                'status' => $request->status,
            ]
        );

        // ৬. নোটিফিকেশন পাঠানো
        $owner = $bloodRequest->requester; 
        if ($owner) {
            $owner->notify(new BloodResponseNotification($bloodRequest, $user, $request->status));
        }

        // ৭. ইউজার ফিডব্যাক
        $message = $request->status === 'accepted' 
            ? 'আপনি রিকোয়েস্টটি এক্সেপ্ট করেছেন।' 
            : 'আপনি রিকোয়েস্টটি ডিক্লাইন করেছেন।';

        return back()->with('success', $message);
    }
}