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

        // ১. বেসিক পলিসি: নিজের রিকোয়েস্টে রেসপন্স করা যাবে না
        abort_if($bloodRequest->requested_by === $request->user()->id, 403, 'আপনি নিজের রিকোয়েস্টে রেসপন্স করতে পারবেন না।');

        // 🚨 ২. মেডিকেল পলিসি এনফোর্সমেন্ট (The core fix) 🚨
        if ($request->status === 'accepted') {
            abort_unless(
                $request->user()->is_eligible_to_donate, 
                403, 
                'মেডিকেল গাইডলাইন অনুযায়ী আপনি আপাতত রক্তদানের জন্য যোগ্য নন। বিস্তারিত জানতে ড্যাশবোর্ড চেক করুন।'
            );
        }

        // ৩. রেসপন্স সেভ বা আপডেট করা
        $response = BloodRequestResponse::updateOrCreate(
            [
                'blood_request_id' => $bloodRequest->id,
                'user_id' => $request->user()->id,
            ],
            [
                'status' => $request->status,
            ]
        );

        // ৪. নোটিফিকেশন পাঠানো
        $owner = $bloodRequest->requester; 
        if ($owner) {
            $owner->notify(new BloodResponseNotification($bloodRequest, $request->user(), $request->status));
        }

        // ৫. ইউজার ফিডব্যাক
        $message = $request->status === 'accepted' ? 'আপনি রিকোয়েস্টটি এক্সেপ্ট করেছেন।' : 'আপনি রিকোয়েস্টটি ডিক্লাইন করেছেন।';
        return back()->with('success', $message);
    }
}