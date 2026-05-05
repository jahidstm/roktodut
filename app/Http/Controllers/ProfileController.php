<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Notifications\AdminTaskNotification;
use App\Services\GamificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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
            ['key' => 'nid_path',      'label' => 'পরিচয়পত্র (NID)',      'weight' => 15, 'done' => !empty($user->nid_path) || !empty($user->nid_number)],
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
     * 🛡️ Phone Privacy টগল (hide_phone)
     */
    public function toggleHidePhone(Request $request)
    {
        $user = $request->user();
        $user->hide_phone = !$user->hide_phone;
        $user->save();

        $msg = $user->hide_phone
            ? '🛡️ নম্বর সুরক্ষিত! এখন থেকে সার্চ পেজে আপনার নম্বর কেউ দেখতে পাবে না।'
            : '👁️ নম্বর দৃশ্যমান করা হয়েছে। ম্যাথ ক্যাপচার মাধ্যমে দেখা যাবে।';

        if ($request->wantsJson()) {
            return response()->json([
                'success'    => true,
                'hide_phone' => (bool) $user->hide_phone,
                'message'    => $msg,
            ]);
        }

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated')
            ->with('success_msg', $msg);
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
        $validator = Validator::make(
            $request->all(),
            [
                'nid_number'   => ['nullable', 'string', 'min:10', 'max:20'],
                'nid_document' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:2048'],
            ],
            [
                'nid_document.uploaded' => 'NID ডকুমেন্ট আপলোড করা যায়নি। ফাইল সাইজ কমিয়ে আবার চেষ্টা করুন।',
                'nid_document.max'      => 'NID ডকুমেন্টের সাইজ সর্বোচ্চ 2MB হতে হবে।',
                'nid_document.mimes'    => 'শুধুমাত্র JPG, PNG অথবা PDF ফাইল দিন।',
            ]
        );

        $validator->after(function ($validator) use ($request) {
            $hasNidNumber = filled(trim((string) $request->input('nid_number', '')));
            $hasNidDocument = $request->hasFile('nid_document');
            $hasNidDocumentError = $validator->errors()->has('nid_document');

            // Either NID number or document is required.
            // If document is present but invalid, show only document error (avoid confusing nid_number error).
            if (!$hasNidNumber && !$hasNidDocument && !$hasNidDocumentError) {
                $validator->errors()->add('nid_number', 'NID নম্বর অথবা ডকুমেন্টের যেকোনো একটি দিন।');
            }
        });

        $validator->validate();

        $user    = $request->user();
        $changed = false;

        // NID নাম্বার সেভ করা
        if ($request->filled('nid_number')) {
            $user->nid_number = $request->nid_number;
            $changed = true;
        }

        // NID ডকুমেন্ট আপলোড করা (Private Storage)
        if ($request->hasFile('nid_document')) {
            $path           = $request->file('nid_document')->store('nid_uploads', 'private');
            $user->nid_path = $path;
            $user->nid_status = 'pending'; // নতুন ডকুমেন্টে সর্বদা pending
            $changed = true;
        }

        if ($changed) {
            $user->save();
            $user->refresh();

            if ($user->nid_status === 'pending') {
                $admins = User::where('role', 'admin')->get();
                if ($admins->isNotEmpty()) {
                    Notification::send($admins, new AdminTaskNotification(
                        message: "{$user->name} নতুন NID তথ্য জমা দিয়েছে। যাচাই প্রয়োজন।",
                        url: route('admin.nid.reviews'),
                        title: '🪪 পেন্ডিং NID ভেরিফিকেশন',
                        taskType: 'nid_review',
                    ));
                }

                if ($user->organization_id && $user->organization && $user->organization->admin) {
                    $user->organization->admin->notify(new AdminTaskNotification(
                        message: "আপনার সদস্য {$user->name} নতুন NID তথ্য জমা দিয়েছে।",
                        url: route('org.dashboard'),
                        title: '🪪 সদস্যের NID ভেরিফিকেশন',
                        taskType: 'member_nid_review',
                    ));
                }
            }

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
            abort(403, 'এই ডকুমেন্টটি দেখার অনুমতি আপনার নেই।');
        }

        return $this->servePrivateNid($user);
    }

    public function viewNidForAdmin(Request $request, User $user)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'সাইনড লিংকটি আর বৈধ নেই।');
        }

        if (!$request->user()->isAdmin()) {
            abort(403, 'এই ডকুমেন্টটি দেখার অনুমতি আপনার নেই।');
        }

        return $this->servePrivateNid($user);
    }

    public function viewNidForOrg(Request $request, User $user)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'সাইনড লিংকটি আর বৈধ নেই।');
        }

        $viewer = $request->user();
        $isOrgAdmin = $viewer->isOrgAdmin() && $viewer->organization_id === $user->organization_id;

        if (!$isOrgAdmin) {
            abort(403, 'এই ডকুমেন্টটি দেখার অনুমতি আপনার নেই।');
        }

        return $this->servePrivateNid($user);
    }

    private function servePrivateNid(User $user)
    {
        if (!$user->nid_path) {
            abort(404, 'ডোনার এখনো এনআইডি ডকুমেন্ট আপলোড করেননি।');
        }

        if (!Storage::disk('private')->exists($user->nid_path)) {
            abort(404, 'ফাইলটি সার্ভারে পাওয়া যায়নি।');
        }

        return Storage::disk('private')->response($user->nid_path);
    }

    /**
     * 📍 ডোনারের GPS লোকেশন সেভ করা (Browser Geolocation API থেকে আসে)
     * POST /profile/location
     */
    public function updateLocation(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $user = $request->user();
        $user->latitude  = $validated['latitude'];
        $user->longitude = $validated['longitude'];
        $user->save();

        return response()->json([
            'success' => true,
            'message' => '✅ আপনার লোকেশন সফলভাবে সেভ হয়েছে!',
        ]);
    }

    public function upgradeToDonor(Request $request)
    {
        $user = $request->user();

        $rules = [
            'blood_group' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'division_id' => 'required|exists:divisions,id',
            'district_id' => 'required|exists:districts,id',
            'upazila_id'  => 'required|exists:upazilas,id',
            'gender'      => 'required|in:male,female',
            'weight'      => 'nullable|numeric|min:30',
            'last_donation_date' => 'nullable|date|before_or_equal:today',
        ];

        // If phone is missing from user, make it required
        if (empty($user->phone)) {
            $rules['phone'] = 'required|string|max:20|unique:users,phone';
        }

        $request->validate($rules, [
            'phone.required' => 'ফোন নম্বর প্রদান করা বাধ্যতামূলক।',
            'phone.unique' => 'এই ফোন নম্বরটি ইতিমধ্যে ব্যবহৃত হয়েছে।',
            'blood_group.required' => 'রক্তের গ্রুপ নির্বাচন করা বাধ্যতামূলক।',
            'division_id.required' => 'বিভাগ নির্বাচন করা বাধ্যতামূলক।',
            'district_id.required' => 'জেলা নির্বাচন করা বাধ্যতামূলক।',
            'upazila_id.required'  => 'উপজেলা নির্বাচন করা বাধ্যতামূলক।',
            'gender.required'      => 'লিঙ্গ নির্বাচন করা বাধ্যতামূলক।',
        ]);

        if (empty($user->phone) && $request->filled('phone')) {
            $user->phone = $request->phone;
        }

        $user->blood_group = $request->blood_group;
        $user->division_id = $request->division_id;
        $user->district_id = $request->district_id;
        $user->upazila_id = $request->upazila_id;
        $user->gender = $request->gender;
        if ($request->filled('weight')) {
            $user->weight = $request->weight;
        }
        if ($request->filled('last_donation_date')) {
            $user->last_donated_at = $request->last_donation_date;
        }

        $user->is_donor = true;
        $user->save();

        return redirect()->route('dashboard')
            ->with('status', 'donor-upgraded')
            ->with('success_msg', 'অভিনন্দন! আপনি এখন সফলভাবে রক্তদাতা হিসেবে যুক্ত হয়েছেন।');
    }
}
