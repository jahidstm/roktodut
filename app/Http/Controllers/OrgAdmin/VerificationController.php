<?php

namespace App\Http\Controllers\OrgAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    /**
     * ডোনারের বিস্তারিত তথ্য দেখা
     */
    public function show($id)
    {
        $donor = User::findOrFail($id);
        $this->authorizeOrganizationAccess($donor);

        return view('org.donor.verify', compact('donor'));
    }

    /**
     * ডোনারকে অ্যাপ্রুভ করা
     */
    public function approve($id)
    {
        $donor = User::findOrFail($id);
        $this->authorizeOrganizationAccess($donor);

        $donor->update([
            'nid_status' => 'approved',
            'verified_badge' => true // 🎯 এই ব্যাজটিই আমরা পরে সার্চ রেজাল্টে দেখাব
        ]);

        return redirect()->route('org.dashboard')->with('success', "{$donor->name}-কে সফলভাবে ভেরিফাই করা হয়েছে।");
    }

    /**
     * ডোনারকে রিজেক্ট করা
     */
    public function reject($id)
    {
        $donor = User::findOrFail($id);
        $this->authorizeOrganizationAccess($donor);

        $donor->update([
            'nid_status' => 'rejected',
            'verified_badge' => false
        ]);

        return redirect()->route('org.dashboard')->with('error', "{$donor->name}-এর ভেরিফিকেশন বাতিল করা হয়েছে।");
    }

    /**
     * 🛡️ সিকিউরিটি লেয়ার: অন্য ক্লাবের অ্যাডমিন যেন এই মেম্বারকে এক্সেস করতে না পারে
     */
    private function authorizeOrganizationAccess(User $donor)
    {
        if (Auth::user()->organization_id !== $donor->organization_id) {
            abort(403, 'Unauthorized access. This member belongs to another organization.');
        }
    }
}