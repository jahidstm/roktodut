<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Exception;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            // পুরো প্রসেসটিকে ট্রাই-ক্যাচের ভেতরে আনা হলো
            $socialUser = Socialite::driver($provider)->user();

            // ১. ইউজার আগে থেকেই সিস্টেমে আছে কি না চেক করা
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                // ২. নতুন ইউজার তৈরি (পাসওয়ার্ড এবং রোল হ্যান্ডলিং সহ)
                $user = User::create([
                    'name'              => $socialUser->getName(),
                    'email'             => $socialUser->getEmail(),
                    'password'          => Hash::make(Str::random(24)), // ডামি সিকিউর পাসওয়ার্ড
                    'provider'          => $provider,
                    'provider_id'       => $socialUser->getId(),
                    'is_onboarded'      => false, 
                    'role'              => null, // অনবোর্ডিংয়ে ইউজার তার রোল সিলেক্ট করবে
                    'email_verified_at' => now(), 
                ]);
            } else {
                // ৩. এক্সিস্টিং ইউজার যদি প্রথমবারের মতো গুগল দিয়ে লগইন করে
                if (!$user->provider_id) {
                    $user->update([
                        'provider'      => $provider,
                        'provider_id'   => $socialUser->getId(),
                    ]);
                }
            }

            // ৪. ইউজারকে লগইন করানো
            Auth::login($user);

            // ৫. রাউটিং লজিক (মিডলওয়্যার ট্র্যাপ এড়ানো)
            if (!$user->is_onboarded) {
                return redirect()->route('onboarding.show');
            }

            // অনবোর্ডিং শেষ হয়ে থাকলে তাকে মূল ফিডে পাঠানো হবে
            return redirect()->intended(route('requests.index'));

        } catch (Exception $e) {
            // প্রোডাকশনে এরর ট্র্যাক করার জন্য লগিং
            Log::error("Social Login Failed: " . $e->getMessage());
            return redirect()->route('login')->with('error', 'সোশ্যাল লগইন ব্যর্থ হয়েছে। দয়া করে আবার চেষ্টা করুন।');
        }
    }
}