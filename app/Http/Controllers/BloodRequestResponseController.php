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
        $request->validate([
            'status' => 'required|in:accepted,declined',
        ]);

        // ১. পলিসি এনফোর্সমেন্ট: রিকোয়েস্টের মালিক নিজের পোস্টে রেসপন্স করতে পারবে না
        abort_if($bloodRequest->requested_by === $request->user()->id, 403, 'আপনি নিজের রিকোয়েস্টে রেসপন্স করতে পারবেন না।');

        // ২. রেসপন্স সেভ বা আপডেট করা
        $response = BloodRequestResponse::updateOrCreate(
            [
                'blood_request_id' => $bloodRequest->id,
                'user_id' => $request->user()->id,
            ],
            [
                'status' => $request->status,
            ]
        );

        // 🎯 ৩. নোটিফিকেশন পাঠানো (The Magic!)
        $owner = $bloodRequest->requester; // রিকোয়েস্টের মালিক
        if ($owner) {
            $owner->notify(new BloodResponseNotification($bloodRequest, $request->user(), $request->status));
        }

        // ৪. ইউজার ফিডব্যাক
        $message = $request->status === 'accepted' ? 'আপনি রিকোয়েস্টটি এক্সেপ্ট করেছেন।' : 'আপনি রিকোয়েস্টটি ডিক্লাইন করেছেন।';
        return back()->with('success', $message);
    }
}