<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DonationRecordController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();
        
        // Anti-cheat: Check if user is currently in a cooldown period
        if ($user->last_donated_at) {
            $lastDonation = \Carbon\Carbon::parse($user->last_donated_at)->startOfDay();
            $cooldownEnds = $lastDonation->copy()->addDays(120); // 120 days cooldown
            
            if (now()->startOfDay()->lt($cooldownEnds)) {
                return back()->with('error', 'আপনি বর্তমানে রক্তদানের কুলডাউন পিরিয়ডে আছেন, তাই এই তারিখ পরিবর্তন করা যাবে না।');
            }
        }

        $request->validate([
            'last_donated_at' => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        $user->update([
            'last_donated_at' => $request->last_donated_at,
        ]);

        return back()->with('success', 'আপনার সর্বশেষ রক্তদানের তথ্য সফলভাবে আপডেট হয়েছে।');
    }
}