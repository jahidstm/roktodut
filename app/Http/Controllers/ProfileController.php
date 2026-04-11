<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\GamificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private GamificationService $gamification) {}

    /**
     * প্রোফাইল কমপ্লিশন পার্সেন্টেজ হিসাব করা
     */
    public function calcCompletion($user): array
    {
        $steps = [
            ['key' => 'name',          'label' => 'পূর্ণ নাম',              'weight' => 10, 'done' => !empty($user->name)],
            ['key' => 'email',         'label' => 'ইমেইল অ্যাড্রেস',        'weight' => 10, 'done' => !empty($user->email)],
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
     * প্রোফাইল এডিট ফর্ম দেখান
     */
    public function edit(Request $request): View
    {
        $user       = $request->user();
        $completion = $this->calcCompletion($user);

        return view('profile.edit', [
            'user'              => $user,
            'organizations'     => \App\Models\Organization::orderBy('name', 'asc')->get(),
            'completionPercent' => $completion['percent'],
            'completionSteps'   => $completion['steps'],
        ]);
    }

    /**
     * প্রোফাইল আপডেট করা + ১০০% হলে গ্যামিফিকেশন বোনাস
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $validatedData = $request->validated();
        $user->fill($validatedData);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // প্রোফাইল পিকচার আপলোড
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        // অর্গানাইজেশন পরিবর্তনে NID re-verify
        if ($user->isDirty('organization_id') && $user->organization_id != null) {
            $user->nid_status = 'pending';
        }

        $user->save();
        $user->refresh();

        // ─── গ্যামিফিকেশন: প্রোফাইল ১০০% হলে বোনাস দাও ─────────────
        $completion = $this->calcCompletion($user);
        if ($completion['percent'] >= 100) {
            $awarded = $this->gamification->awardProfileCompletionBonus($user);
            if ($awarded) {
                return Redirect::route('profile.edit')
                    ->with('status', 'profile-updated')
                    ->with('bonus_msg', '🎉 অভিনন্দন! প্রোফাইল ১০০% সম্পূর্ণ করায় আপনি +২০ পয়েন্ট পেয়েছেন!');
            }
        }

        // ─── গ্যামিফিকেশন: ব্যাজ রিচেক (লগইন করা ছাড়াও প্রোফাইল আপডেট করলে যেন ব্যাজ পায়) ────────
        $this->gamification->checkAndAwardBadges($user);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * 🚨 Emergency Mode টগল + Ready Now ব্যাজ সিঙ্ক
     */
    public function toggleEmergencyMode(Request $request)
    {
        $user = $request->user();
        $user->is_available = !$user->is_available;
        $user->save();

        // ─── গ্যামিফিকেশন: ব্যাজ সিঙ্ক ─────────────────────────────
        $this->gamification->handleReadyNowBadge($user, $user->is_available);

        $msg = $user->is_available
            ? '✅ ইমার্জেন্সি মোড চালু! আপনি এখন ডোনার সার্চে দৃশ্যমান এবং 🏅 Ready Now ব্যাজ পেয়েছেন।'
            : '⏸ ইমার্জেন্সি মোড বন্ধ করা হয়েছে এবং Ready Now ব্যাজ সরানো হয়েছে।';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_available' => (bool)$user->is_available,
                'message' => $msg
            ]);
        }

        return Redirect::route('profile.edit')
            ->with('status', 'emergency-updated')
            ->with('emergency_msg', $msg);
    }

    /**
     * Welcome Back স্ট্যাটাস আপডেট
     */
    public function welcomeBackUpdate(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->is_available         = $request->has('is_available');
        $user->welcome_back_checked = true;
        $user->save();

        // ব্যাজ সিঙ্ক
        $this->gamification->handleReadyNowBadge($user, $user->is_available);

        return back()->with('success', 'আপনার স্ট্যাটাস সফলভাবে আপডেট করা হয়েছে। রক্তদূতে আবার স্বাগতম!');
    }

    /**
     * NID ডকুমেন্ট ও নাম্বার আপলোড/সেভ করা
     */
    public function uploadNid(Request $request): RedirectResponse
    {
        $request->validate([
            'nid_number'   => ['nullable', 'string', 'min:10', 'max:20'],
            'nid_document' => ['nullable', 'file', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $user    = $request->user();
        $changed = false;

        // NID নাম্বার সেভ করা
        if ($request->filled('nid_number')) {
            $user->nid_number = $request->nid_number;
            $changed = true;
        }

        // NID ডকুমেন্ট আপলোড করা (Private Storage)
        if ($request->hasFile('nid_document')) {
            $path           = $request->file('nid_document')->store('nid_uploads', 'local');
            $user->nid_path = $path;
            $user->nid_status = 'pending'; // নতুন ডকুমেন্টে সর্বদা pending
            $changed = true;
        }

        if ($changed) {
            $user->save();
            $user->refresh();

            // NID আপলোড করলে প্রোফাইল ১০০% হয় কিনা চেক করা
            $completion = $this->calcCompletion($user);
            if ($completion['percent'] >= 100) {
                $this->gamification->awardProfileCompletionBonus($user);
            }
        }

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated')
            ->with('success_msg', '✅ এনআইডি তথ্য সফলভাবে জমা দেওয়া হয়েছে! অ্যাডমিন যাচাই করার পর আপনার Verified Donor ব্যাজ যুক্ত হবে।');
    }

    /**
     * অ্যাকাউন্ট ডিলিট
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

    /**
     * Secure NID Viewer Method
     * Only the actual user or an Administrator can view the private NID file.
     */
    public function viewNid(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);

        // পারমিশন চেক: নিজে অথবা অ্যাডমিন অথবা নিজ প্রতিষ্ঠানের অ্যাডমিন কি না
        $isOwner = $request->user()->id === $user->id;
        $isAdmin = $request->user()->isAdmin();
        $isOrgAdmin = $request->user()->isOrgAdmin() && $request->user()->organization_id === $user->organization_id;

        if (!$isOwner && !$isAdmin && !$isOrgAdmin) {
            abort(403, 'Unauthorized access to NID document.');
        }

        if (!$user->nid_path) {
            abort(404, 'NID document not found.');
        }

        $path = storage_path('app/private/' . $user->nid_path);

        if (!file_exists($path)) {
            abort(404, 'File not found on server: ' . $path);
        }

        return response()->file($path);
    }
}
