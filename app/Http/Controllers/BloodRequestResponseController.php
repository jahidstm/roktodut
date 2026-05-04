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
            'status' => 'required|in:pending,declined,withdrawn',
        ]);

        // ২. ইউজারকে ভেরিয়েবলে নেওয়া
        $user = $request->user();

        // 🚨 ৩. মেডিকেল কমপ্লায়েন্স চেক (৯০-দিনের রুল)
        if ($request->status === 'pending' && !$user->canDonate()) {
            $remainingDays = $user->daysUntilNextDonation();
            return back()->with('error', "মেডিকেল গাইডলাইন অনুযায়ী আপনি আগামী {$remainingDays} দিন রক্ত দিতে পারবেন না।");
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

        // ৬. নোটিফিকেশন পাঠানো (অপশনাল, শুধু pending হলে পাঠাই)
        $owner = $bloodRequest->requester; 
        if ($owner && $request->status === 'pending') {
            $owner->notify(new BloodResponseNotification($bloodRequest, $user, $request->status));
        }

        // ৭. ইউজার ফিডব্যাক
        $message = $request->status === 'pending' 
            ? '✅ আপনার আগ্রহ জানানো হয়েছে। রোগীর অনুমোদনের জন্য অপেক্ষা করুন।' 
            : 'আপনি রিকোয়েস্টটি ডিক্লাইন/ক্যান্সেল করেছেন।';

        return back()->with('success', $message);
    }

    /**
     * রোগীর ড্যাশবোর্ড থেকে ডোনারকে Accept বা Decline করা
     */
    public function updateStatus(Request $request, BloodRequestResponse $response)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $bloodRequest = $response->bloodRequest;

        // শুধু রিকোয়েস্টের মালিক (রোগী) এটি করতে পারবে
        abort_if(
            $bloodRequest->requested_by !== $request->user()->id, 
            403, 
            'আপনি এই অ্যাকশনটি করতে পারবেন না।'
        );

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $response, $bloodRequest) {
            // ১. Update the donor's response status
            $response->update([
                'status' => $request->status,
            ]);

            // ২. Sync parent request status to avoid data inconsistency
            // Shift it out of 'pending' so it drops from the public feed
            if ($request->status === 'accepted') {
                $bloodRequest->update([
                    'status' => 'in_progress',
                ]);
            }
        });

        $msg = $request->status === 'accepted' 
            ? 'ডোনারকে সফলভাবে অ্যাকসেপ্ট করা হয়েছে। এখন আপনি তার ফোন নম্বর দেখতে পারবেন।'
            : 'ডোনারকে ডিক্লাইন করা হয়েছে।';

        return back()->with('success', $msg);
    }
}