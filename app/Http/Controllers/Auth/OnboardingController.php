<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    /**
     * অনবোর্ডিং পেজ দেখানো
     */
    public function show()
    {
        // ইউজার যদি অলরেডি অনবোর্ডিং শেষ করে থাকে, তবে তাকে ড্যাশবোর্ডে পাঠিয়ে দাও
        if (Auth::user()->is_onboarded) {
            return redirect()->route('dashboard');
        }

        // ব্লেড ফাইলের ড্রপডাউনের জন্য সব বিভাগ পাঠানো হচ্ছে
        $divisions = Division::all();
        return view('auth.onboarding', compact('divisions'));
    }

    /**
     * অনবোর্ডিং ডেটা সেভ করা (লোকেশন ও ডোনেশন ডেটা)
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // ১. স্মার্ট ভ্যালিডেশন
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'district_id' => 'required|exists:districts,id',
            'upazila_id'  => 'required|exists:upazilas,id',
            'last_donation_date' => 'nullable|date|before_or_equal:today',
        ]);

        // ২. ইউজারের প্রোফাইল আপডেট
        $user->update([
            'division_id' => $request->division_id,
            'district_id' => $request->district_id,
            'upazila_id'  => $request->upazila_id,
            'last_donation_date' => $request->last_donation_date,
            'is_onboarded' => true, // ✅ অনবোর্ডিং কমপ্লিট ফ্ল্যাগ
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'আপনার প্রোফাইল সফলভাবে সম্পন্ন হয়েছে। রক্তদূত-এ স্বাগতম!');
    }
}
