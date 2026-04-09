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
     * প্রোফাইল কমপ্লিশন পার্সেন্টেজ হিসাব করা
     */
    private function calcCompletion($user): array
    {
        $steps = [
            ['key' => 'name',          'label' => 'পূর্ণ নাম',              'weight' => 10, 'done' => !empty($user->name)],
            ['key' => 'email',         'label' => 'ইমেইল ভেরিফাই',         'weight' => 10, 'done' => (bool) $user->email_verified_at],
            ['key' => 'phone',         'label' => 'মোবাইল নাম্বার',         'weight' => 10, 'done' => !empty($user->phone)],
            ['key' => 'blood_group',   'label' => 'রক্তের গ্রুপ',           'weight' => 15, 'done' => !empty($user->blood_group)],
            ['key' => 'profile_image', 'label' => 'প্রোফাইল ছবি',           'weight' => 10, 'done' => !empty($user->profile_image)],
            ['key' => 'date_of_birth', 'label' => 'জন্ম তারিখ',             'weight' => 5,  'done' => !empty($user->date_of_birth)],
            ['key' => 'gender',        'label' => 'লিঙ্গ',                  'weight' => 5,  'done' => !empty($user->gender)],
            ['key' => 'weight',        'label' => 'শরীরের ওজন',              'weight' => 5,  'done' => !empty($user->weight)],
            ['key' => 'district_id',   'label' => 'জেলা / লোকেশন',         'weight' => 15, 'done' => !empty($user->district_id)],
            ['key' => 'is_available',  'label' => 'ইমার্জেন্সি মোড চালু',   'weight' => 5,  'done' => (bool) $user->is_available],
            ['key' => 'nid_path',      'label' => 'পরিচয়পত্র (NID) আপলোড', 'weight' => 10, 'done' => !empty($user->nid_path)],
        ];

        $earned = collect($steps)->where('done', true)->sum('weight');
        return ['percent' => $earned, 'steps' => $steps];
    }

    /**
     * ডিসপ্লে ইউজার প্রোফাইল এডিট ফর্ম
     */
    public function edit(Request $request): View
    {
        $user       = $request->user();
        $completion = $this->calcCompletion($user);

        return view('profile.edit', [
            'user'          => $user,
            'organizations' => \App\Models\Organization::orderBy('name', 'asc')->get(),
            'completionPercent' => $completion['percent'],
            'completionSteps'   => $completion['steps'],
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
     * 🚨 ইমার্জেন্সি মোড টগল (Profile page থেকে)
     */
    public function toggleEmergencyMode(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->is_available = !$user->is_available;
        $user->save();

        $msg = $user->is_available
            ? '✅ ইমার্জেন্সি মোড চালু হয়েছে! এখন আপনি ডোনার সার্চে দৃশ্যমান।'
            : '⏸ ইমার্জেন্সি মোড বন্ধ করা হয়েছে।';

        return Redirect::route('profile.edit')->with('status', 'emergency-updated')->with('emergency_msg', $msg);
    }

    /**
     * 🚀 Welcome Back স্ট্যাটাস আপডেট
     */
    public function welcomeBackUpdate(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->is_available      = $request->has('is_available');
        $user->welcome_back_checked = true;
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
