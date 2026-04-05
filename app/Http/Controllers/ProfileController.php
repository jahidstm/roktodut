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
            // 🚀 ড্রপডাউনের জন্য অর্গানাইজেশন পাঠানো হচ্ছে
            'organizations' => \App\Models\Organization::orderBy('name', 'asc')->get(),
        ]);
    }

    /**
     * আপডেট ইউজার প্রোফাইল ইনফরমেশন
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $validatedData = $request->validated();
        $user->fill($validatedData);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // 🚀 প্রোফাইল পিকচার আপলোড লজিক
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        // যদি ইউজার নতুন অর্গানাইজেশন সিলেক্ট করে, তবে তার NID স্ট্যাটাস আবার pending হবে
        if ($user->isDirty('organization_id') && $user->organization_id != null) {
            $user->nid_status = 'pending';
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * 🚀 Welcome Back স্ট্যাটাস আপডেট
     */
    public function welcomeBackUpdate(Request $request): RedirectResponse
    {
        $user = $request->user();

        // ইউজার যদি 'is_available' চেকবক্স মার্ক করে থাকে
        $user->is_available = $request->has('is_available');
        $user->welcome_back_checked = true; // চেক করা হয়ে গেছে
        $user->save();

        return back()->with('success', 'আপনার স্ট্যাটাস সফলভাবে আপডেট করা হয়েছে। রক্তদূতে আবার স্বাগতম!');
    }

    /**
     * NID বা ডকুমেন্ট আপলোড লজিক
     */
    public function uploadNid(Request $request): RedirectResponse
    {
        $request->validate([
            'nid_document' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048']
        ]);

        $user = $request->user();

        if ($request->hasFile('nid_document')) {
            $path = $request->file('nid_document')->store('donor_nids', 'public');
            $user->nid_path = $path;
            $user->save();
        }

        return Redirect::route('dashboard')->with('success', 'ডকুমেন্ট সফলভাবে আপলোড হয়েছে! অর্গানাইজেশন যাচাই করার পর আপনার ব্যাজ যুক্ত হবে।');
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
