<?php

namespace App\Http\Controllers\OrgAdmin;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\BroadcastLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BloodRequestController extends Controller
{
    public function index()
    {
        $admin = Auth::user();
        $org = $admin->organization;

        if (!$org) {
            abort(403, 'আপনার কোনো অর্গানাইজেশন অ্যাসাইন করা নেই।');
        }

        // Only requests in the Org's division/district/upazila that are pending
        $query = BloodRequest::with(['requester', 'district', 'upazila'])
            ->where('status', 'pending');

        if ($org->upazila) {
            $query->where('upazila_id', $org->upazila);
        } elseif ($org->district) {
            $query->where('district_id', $org->district);
        }

        $requests = $query->latest()->paginate(15);

        return view('org.blood-requests.index', compact('requests', 'org'));
    }

    public function broadcast(Request $request, BloodRequest $bloodRequest)
    {
        $admin = Auth::user();
        $org = $admin->organization;

        if (!$org) {
            abort(403, 'আনঅথোরাইজড অ্যাক্সেস!');
        }

        // Rate limit / check duplicate broadcast for this request by this org
        $alreadyBroadcasted = BroadcastLog::where('organization_id', $org->id)
            ->where('blood_request_id', $bloodRequest->id)
            ->exists();

        if ($alreadyBroadcasted) {
            return back()->with('error', 'এই রিকোয়েস্টটি ইতিমধ্যে ব্রডকাস্ট করা হয়েছে।');
        }

        // Find verified members of this org who haven't opted out
        $members = $org->approvedMembers()
            ->where('opt_out_org_broadcast', false)
            ->get();

        // In a real app we would send DB notifications or push notifications here.
        // For MVP, we just create a broadcast log.
        BroadcastLog::create([
            'organization_id' => $org->id,
            'blood_request_id' => $bloodRequest->id,
            'broadcasted_by' => $admin->id,
        ]);

        return back()->with('success', "{$members->count()} জন ডোনারের কাছে ব্রডকাস্ট মেসেজ পাঠানো হয়েছে। (MVP)");
    }
}
