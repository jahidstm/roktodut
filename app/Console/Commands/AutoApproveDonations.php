<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BloodRequestResponse; // তোমার আসল মডেলের নাম অনুযায়ী দেবে
use Illuminate\Support\Facades\Log;

class AutoApproveDonations extends Command
{
    protected $signature = 'donations:auto-approve';
    protected $description = '২৪ ঘণ্টা পার হয়ে যাওয়া পেন্ডিং ডোনেশন অটো-অ্যাপ্রুভ করা';

    public function handle()
    {
        // যেসব ডোনেশন 'donated' স্ট্যাটাসে আছে এবং ২৪ ঘণ্টা পার হয়ে গেছে
        $responses = BloodRequestResponse::where('status', 'donated') // তোমার সিস্টেমে যে স্ট্যাটাস ব্যবহৃত হয়
            ->where('updated_at', '<=', now()->subHours(24))
            ->get();

        $count = 0;
        foreach ($responses as $response) {
            // ১. স্ট্যাটাস অ্যাপ্রুভড করা
            $response->status = 'approved';
            $response->save();

            // ২. ডোনারের প্রোফাইলে রেকর্ড আপডেট করা
            $donor = $response->donor;
            if ($donor) {
                $donor->total_donations += 1;
                $donor->last_donated_at = now();
                $donor->save();
            }
            $count++;
        }

        $this->info("{$count} donations auto-approved successfully.");
        Log::info("Silent Approval Engine: {$count} donations auto-approved.");
    }
}
