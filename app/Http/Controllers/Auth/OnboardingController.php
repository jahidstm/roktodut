<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    /**
     * অনবোর্ডিং পেজ দেখানো
     */
    public function show()
    {
        // ইউজার যদি অলরেডি অনবোর্ডিং শেষ করে থাকে, তবে তাকে ড্যাশবোর্ডে পাঠিয়ে দাও
        if (Auth::user()->is_onboarded) {
            return redirect()->route('dashboard');
        }

        // ব্লেড ফাইলের ড্রপডাউনের জন্য অর্গানাইজেশন পাঠানো হচ্ছে (লোকেশন কম্পোনেন্ট নিজেই লোকেশন লোড করবে)
        $organizations = Organization::orderBy('name', 'asc')->get();

        return view('auth.onboarding', compact('organizations'));
    }

    /**
     * অনবোর্ডিং ডেটা সেভ করা (লোকেশন, ডোনেশন ও প্রোফাইল ডেটা)
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // ১. বেসিক লোকেশন ভ্যালিডেশন
        $rules = [
            'division_id' => 'required|exists:divisions,id',
            'district_id' => 'required|exists:districts,id',
            'upazila_id'  => 'required|exists:upazilas,id',
        ];

        // ২. যদি ইউজার 'ডোনার' হয়, তবে তার জন্য অতিরিক্ত ফিল্ড ভ্যালিডেশন
        $roleValue = $user->role instanceof \App\Enums\UserRole ? $user->role->value : $user->role;
        if ($roleValue === 'donor') {
            $rules['gender'] = 'required|in:male,female';
            $rules['weight'] = 'required|numeric|min:30';
            $rules['last_donation_date'] = 'nullable|date|before_or_equal:today';
            $rules['organization_id'] = 'nullable|exists:organizations,id';
        }

        $request->validate($rules);

        // ৩. যদি ইউজার কোনো অর্গানাইজেশন সিলেক্ট করে, তবে তার NID স্ট্যাটাস 'pending' হবে
        $nidStatus = $request->organization_id ? 'pending' : 'none';

        // ৪. ইউজারের প্রোফাইল আপডেট
        $user->update([
            'division_id'     => $request->division_id,
            'district_id'     => $request->district_id,
            'upazila_id'      => $request->upazila_id,
            'gender'          => $request->gender,
            'weight'          => $request->weight,
            'last_donated_at' => $request->last_donation_date, // ডাটাবেস কলাম অনুযায়ী
            'organization_id' => $request->organization_id,
            'nid_status'      => $nidStatus,
            'is_onboarded'    => true, // ✅ অনবোর্ডিং কমপ্লিট ফ্ল্যাগ
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'আপনার প্রোফাইল সফলভাবে সম্পন্ন হয়েছে। রক্তদূত-এ স্বাগতম!');
    }
}
