<?php

namespace App\Http\Controllers\OrgAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\DonorVerificationStatusNotification; // 👈 নতুন নোটিফিকেশন ক্লাস
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * ডোনারের এনআইডি এবং ইনফরমেশন প্রিভিউ করা
     */
    public function show($id)
    {
        $donor = User::findOrFail($id);
        
        // সিকিউরিটি চেক
        abort_if($donor->nid_status !== 'pending', 404, 'এই ডোনারের ভেরিফিকেশন স্ট্যাটাস রিভিউ করার যোগ্য নয়।');
        
        return view('org.verify', compact('donor'));
    }

    /**
     * ভেরিফিকেশন অ্যাপ্রুভ করা (ব্লু-ব্যাজ প্রদান)
     */
    public function approve($id)
    {
        $donor = User::findOrFail($id);
        
        $donor->update([
            'nid_status' => 'approved',
            'verified_badge' => true,
        ]);

        // 🔔 ডোনারকে নোটিফিকেশন পাঠানো হচ্ছে
        $donor->notify(new DonorVerificationStatusNotification('approved'));

        return redirect()->route('org.dashboard')->with('success', "{$donor->name}-এর একাউন্ট সফলভাবে ভেরিফাই করা হয়েছে এবং ব্লু-ব্যাজ যুক্ত করা হয়েছে!");
    }

    /**
     * ভেরিফিকেশন রিজেক্ট বা বাতিল করা
     */
    public function reject($id)
    {
        $donor = User::findOrFail($id);
        
        $donor->update([
            'nid_status' => 'rejected',
            'verified_badge' => false,
        ]);

        // 🔔 ডোনারকে নোটিফিকেশন পাঠানো হচ্ছে
        $donor->notify(new DonorVerificationStatusNotification('rejected'));

        return redirect()->route('org.dashboard')->with('error', "{$donor->name}-এর ভেরিফিকেশন রিকোয়েস্ট বাতিল করা হয়েছে।");
    }
}