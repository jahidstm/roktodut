<?php

namespace App\Events;

use App\Models\BloodRequest;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DonationCompleted
{
    use Dispatchable, SerializesModels;

    /**
     * ইভেন্টটি ফায়ার হলে Listener-রা এই প্রপার্টিগুলো পাবে।
     *
     * @param  User         $donor        — যিনি রক্ত দিয়েছেন
     * @param  BloodRequest $bloodRequest — সম্পন্ন হওয়া রিকোয়েস্ট
     * @param  bool         $isEmergency  — ইমার্জেন্সি ছিল কি না (First Responder বোনাসের জন্য)
     * @param  bool         $isFirstResponder — ৩ ঘণ্টার মধ্যে রেসপন্ড করেছিলেন কি না
     */
    public function __construct(
        public readonly User         $donor,
        public readonly BloodRequest $bloodRequest,
        public readonly bool         $isEmergency      = false,
        public readonly bool         $isFirstResponder = false,
    ) {}
}
