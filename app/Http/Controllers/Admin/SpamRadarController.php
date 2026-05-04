<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BloodRequest;

class SpamRadarController extends Controller
{
    /**
     * Display the Spam Radar dashboard.
     */
    public function index()
    {
        // Get blood requests that have pending spam reports.
        // We will group them by blood_request to easily review them.
        $suspectedRequests = BloodRequest::whereHas('spamReports', function ($query) {
                $query->where('status', 'pending')
                      ->whereIn('reason', ['fake_number', 'spam', 'abusive']);
            })
            ->with(['requester:id,name,phone,spam_strikes,is_shadowbanned', 'spamReports' => function($q) {
                $q->where('status', 'pending')
                  ->whereIn('reason', ['fake_number', 'spam', 'abusive'])
                  ->with('reporter:id,name,phone');
            }])
            ->withCount(['spamReports as pending_reports_count' => function ($query) {
                $query->where('status', 'pending')
                      ->whereIn('reason', ['fake_number', 'spam', 'abusive']);
            }])
            ->orderByDesc('pending_reports_count')
            ->paginate(15);

        return view('admin.spam_radar.index', compact('suspectedRequests'));
    }

    /**
     * Approve the reports, give a strike to the user, and potentially shadowban.
     */
    public function approveStrike(BloodRequest $bloodRequest)
    {
        $requester = $bloodRequest->requester;

        // Give a strike to the requester
        $requester->increment('spam_strikes');

        // Check if shadowban threshold is met
        if ($requester->spam_strikes >= 2) {
            $requester->update(['is_shadowbanned' => true]);
        }

        // Mark all pending spam reports for this request as approved
        $bloodRequest->spamReports()
            ->where('status', 'pending')
            ->whereIn('reason', ['fake_number', 'spam', 'abusive'])
            ->update(['status' => 'strike_approved']);

        // The request remains hidden (is_hidden = true) because it's confirmed spam
        $bloodRequest->update(['is_hidden' => true]);

        return back()->with('success', "স্ট্রাইক অ্যাপ্রুভ করা হয়েছে। ইউজারের বর্তমান স্ট্রাইক: {$requester->spam_strikes}");
    }

    /**
     * Reject the reports (false positive), restore the request.
     */
    public function rejectReports(BloodRequest $bloodRequest)
    {
        // Mark all pending reports as rejected
        $bloodRequest->spamReports()
            ->where('status', 'pending')
            ->whereIn('reason', ['fake_number', 'spam', 'abusive'])
            ->update(['status' => 'rejected']);

        // Restore the request visibility and reset its spam count
        $bloodRequest->update([
            'is_hidden' => false,
            'spam_report_count' => 0
        ]);

        return back()->with('success', 'রিপোর্টগুলো বাতিল করা হয়েছে এবং রিকোয়েস্টটি ফিডে রিস্টোর করা হয়েছে।');
    }
}
