<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Carbon\Carbon;
use App\Models\User; // 🚀 মডেলটি ইমপোর্ট করো

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        /** @var User $user */ // 🎯 THE FIX: ইনটেলফেন্সকে বলা হচ্ছে এটি ইউজার মডেল
        $user = $event->user;

        // যদি ইউজারের আগের লগইন ৩০ দিনের বেশি পুরনো হয়, তবে ফ্ল্যাগটি false করে দাও
        if ($user->last_login_at && $user->last_login_at->diffInDays(now()) >= 30) {
            $user->welcome_back_checked = false;
        }

        // বর্তমান লগইন টাইম আপডেট
        $user->last_login_at = now();
        $user->save(); // ✅ এখন আর এরর দেখাবে না
        
        // 🚀 প্রতিবার লগইনে গ্যামিফিকেশন ব্যাজ (যেমন Campus Hero) চেক করে দেওয়া হবে
        app(\App\Services\GamificationService::class)->checkAndAwardBadges($user);
    }
}
