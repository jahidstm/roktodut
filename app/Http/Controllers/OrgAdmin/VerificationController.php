<?php

namespace App\Http\Controllers\OrgAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // 🎯 ইনটেলফেন্স এরর ফিক্স করতে এটি যুক্ত করা হয়েছে

class VerificationController extends Controller
{
    // ... show method ...

    /**
     * ডোনার প্রোফাইল অ্যাপ্রুভ করা
     */
    public function approve(Request $request, $id)
    {
        $donor = User::findOrFail($id);
        $admin = Auth::user(); // ফ্যাসাড ব্যবহার করা হয়েছে

        // 🚨 Anti-Hacking Guard: Cross-Organization Breach Prevention
        if ($donor->organization_id !== $admin->organization_id) {
            abort(403, 'Security Violation: আপনি অন্য অর্গানাইজেশনের ডোনারকে ভেরিফাই করতে পারবেন না।');
        }

        // ভেরিফাই করা হলো
        $donor->update([
            'is_verified' => true,
            'verified_by' => Auth::id(), // বর্তমান লগইন করা অ্যাডমিনের আইডি
            'verified_at' => now(),
        ]);

        return back()->with('success', 'ডোনার প্রোফাইল সফলভাবে ভেরিফাই করা হয়েছে।');
    }

    /**
     * ডোনার রিকোয়েস্ট রিজেক্ট করা
     */
    public function reject(Request $request, $id)
    {
        $donor = User::findOrFail($id);
        $admin = Auth::user();

        // 🚨 Anti-Hacking Guard
        if ($donor->organization_id !== $admin->organization_id) {
            abort(403, 'Security Violation: Access Denied.');
        }

        // রিজেক্ট লজিক: মেম্বারকে আনভেরিফাইড করা
        $donor->update([
            'is_verified' => false,
            'verified_by' => null,
            'verified_at' => null,
        ]);

        return back()->with('error', 'ডোনার অ্যাপ্লিকেশনটি বাতিল করা হয়েছে।');
    }
}