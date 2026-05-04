<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BloodRequestReportController extends Controller
{
    /**
     * Store a newly created report in storage.
     */
    public function store(Request $request, BloodRequest $bloodRequest)
    {
        $request->validate([
            'reason' => 'required|in:fake_number,already_managed,spam,abusive',
        ]);

        $user = auth()->user();

        // 1. Advanced Guard: Must be eligible to report (e.g. has NID or is an active donor)
        // For now, we will just check if they are logged in (handled by auth middleware in route)
        // Let's add a basic check: They cannot report their own request.
        if ($user->id === $bloodRequest->requested_by) {
            return back()->with('error', 'আপনি নিজের রিকোয়েস্টে রিপোর্ট করতে পারবেন না।');
        }

        // Check if already reported
        $existingReport = BloodRequestReport::where('user_id', $user->id)
                                            ->where('blood_request_id', $bloodRequest->id)
                                            ->first();

        if ($existingReport) {
            return back()->with('error', 'আপনি ইতোমধ্যেই এই রিকোয়েস্টে রিপোর্ট করেছেন।');
        }

        // Create the report
        $report = BloodRequestReport::create([
            'user_id'          => $user->id,
            'blood_request_id' => $bloodRequest->id,
            'reason'           => $request->reason,
            'status'           => 'pending',
        ]);

        // Route the logic based on the reason
        if ($request->reason === 'already_managed') {
            $bloodRequest->increment('managed_report_count');

            // 3-Strike rule for managed
            if ($bloodRequest->managed_report_count >= 3) {
                $bloodRequest->update(['status' => 'completed']);
            }
        } else {
            // fake_number, spam, abusive
            $bloodRequest->increment('spam_report_count');

            // 3-Strike rule for spam (Auto-Hide Trigger)
            if ($bloodRequest->spam_report_count >= 3) {
                $bloodRequest->update(['is_hidden' => true]);
            }
        }

        return back()->with('success', 'আপনার রিপোর্টটি সফলভাবে গ্রহণ করা হয়েছে। ধন্যবাদ!');
    }
}
