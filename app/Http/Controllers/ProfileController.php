<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * ডিসপ্লে ইউজার প্রোফাইল এডিট ফর্ম
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * আপডেট ইউজার প্রোফাইল ইনফরমেশন (Location ID ভিত্তিক)
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // ১. ভ্যালিডেশন ডেটা নেওয়া (অনুরোধে পাঠানো আইডিগুলো সহ)
        $validatedData = $request->validated();

        // ২. সরাসরি আইডিগুলো অ্যাসাইন করা
        $user->fill($validatedData);

        // ৩. ইমেইল পরিবর্তন হলে ভেরিফিকেশন রিসেট করা
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * ইউজার অ্যাকাউন্ট ডিলিট করা
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}