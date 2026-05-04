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
            $socialUser = Socialite::driver($provider)->user();

            // ১. ইউজার আগে থেকেই সিস্টেমে আছে কি না চেক করা
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                // ২. নতুন ইউজার তৈরি
                // ✅ Google Bypass Fix: ইউজার ফর্ম দেখেননি, তাই is_donor=false ডিফল্ট।
                // অনবোর্ডিং ফ্লোতে ইউজার নিজে ডোনার হওয়ার সিদ্ধান্ত নেবেন।
                $user = User::create([
                    'name'              => $socialUser->getName(),
                    'email'             => $socialUser->getEmail(),
                    'password'          => Hash::make(Str::random(24)),
                    'provider'          => $provider,
                    'provider_id'       => $socialUser->getId(),
                    'is_onboarded'      => false,
                    'role'              => null,
                    'is_donor'          => false,
                    'email_verified_at' => now(),
                ]);
            } else {
                // ৩. এক্সিস্টিং ইউজার যদি প্রথমবারের মতো গুগল দিয়ে লগইন করে
                if (!$user->provider_id) {
                    $user->update([
                        'provider'    => $provider,
                        'provider_id' => $socialUser->getId(),
                    ]);
                }
            }

            // ৪. ইউজারকে লগইন করানো
            Auth::login($user);

            // ৫. রাউটিং লজিক (মিডলওয়্যার ট্র্যাপ এড়ানো)
            if (!$user->is_onboarded) {
                return redirect()->route('onboarding.show');
            }

            return redirect()->intended(route('requests.index'));

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}