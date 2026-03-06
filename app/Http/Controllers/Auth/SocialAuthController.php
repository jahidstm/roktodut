<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'সোশ্যাল লগইন ব্যর্থ হয়েছে। আবার চেষ্টা করুন।');
        }

        // ইউজার আগে থেকেই আছে কি না চেক করো
        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            // নতুন ইউজার তৈরি করো (অসম্পূর্ণ প্রোফাইল)
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'is_onboarded' => false, 
                'role' => null, 
                'email_verified_at' => now(), // গুগল থেকে আসলে ইমেইল ভেরিফাইড ধরাই যায়
            ]);
        }

        Auth::login($user);

        // অনবোর্ডিং শেষ না হলে অনবোর্ডিং পেজে পাঠাও
        if (!$user->is_onboarded) {
            return redirect()->route('onboarding.show');
        }

        return redirect()->route('dashboard');
    }
}