<?php

namespace App\Http\Controllers\OrgAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    // ... show method ...

    public function approve(Request $request, $id)
    {
        $donor = User::findOrFail($id);

        // 🚨 Anti-Hacking Guard: Cross-Organization Breach Prevention
        if ($donor->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Security Violation: আপনি অন্য অর্গানাইজেশনের ডোনারকে ভেরিফাই করতে পারবেন না।');
        }

        // ভেরিফাই করা হলো
        $donor->update([
            'is_verified' => true,
            'verified_by' => auth()->id(), // কে ভেরিফাই করলো তার রেকর্ড রাখা ভালো
            'verified_at' => now(),
        ]);

        return back()->with('success', 'ডোনার প্রোফাইল সফলভাবে ভেরিফাই করা হয়েছে।');
    }

    public function reject(Request $request, $id)
    {
        $donor = User::findOrFail($id);

        // 🚨 Anti-Hacking Guard
        if ($donor->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Security Violation: Access Denied.');
        }

        // রিজেক্ট লজিক (হতে পারে তুমি তাকে আনভেরিফাইড করবে বা অ্যাপ্লিকেশন মুছে দেবে)
        $donor->update(['is_verified' => false]);

        return back()->with('error', 'ডোনার অ্যাপ্লিকেশনটি বাতিল করা হয়েছে।');
    }
}