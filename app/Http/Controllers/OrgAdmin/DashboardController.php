<?php

namespace App\Http\Controllers\OrgAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * অর্গানাইজেশন ড্যাশবোর্ড এবং মেম্বার লিস্ট প্রদর্শন
     */
    public function index(Request $request)
    {
        $admin = Auth::user();

        // 🛡️ সিকিউরিটি চেক: অ্যাডমিনের নিজের কোনো অর্গানাইজেশন আইডি না থাকলে এক্সেস পাবে না
        if (!$admin->organization_id) {
            abort(403, 'আপনার কোনো অর্গানাইজেশন অ্যাসাইন করা নেই।');
        }

        // 🔍 শুধু এই অ্যাডমিনের অর্গানাইজেশনের মেম্বার (ডোনার) দের খুঁজে বের করো
        $query = User::where('organization_id', $admin->organization_id)
            ->where('role', 'donor');

        // ফিল্টারিং (Pending, Approved, Rejected)
        if ($request->filled('status')) {
            $query->where('nid_status', $request->status);
        }

        $members = $query->latest()->paginate(15)->withQueryString();

        // 📊 সামারি স্ট্যাটাস (অ্যানালিটিক্স এর জন্য) - 🚀 Updated to count only 'donors'
        $stats = [
            'total'    => User::where('organization_id', $admin->organization_id)->where('role', 'donor')->count(),
            'pending'  => User::where('organization_id', $admin->organization_id)->where('role', 'donor')->where('nid_status', 'pending')->count(),
            'approved' => User::where('organization_id', $admin->organization_id)->where('role', 'donor')->where('nid_status', 'approved')->count(),
        ];

        return view('org.dashboard', compact('members', 'stats', 'request'));
    }

    /**
     * ডোনারের ভেরিফিকেশন স্ট্যাটাস আপডেট করা (অ্যাপ্রুভ/রিজেক্ট)
     */
    public function updateVerificationStatus(Request $request, User $donor)
    {
        $admin = Auth::user();

        // 🛡️ সিকিউরিটি চেক: অ্যাডমিন কি তার নিজের অর্গানাইজেশনের বাইরের কাউকে অ্যাপ্রুভ করার চেষ্টা করছে?
        if ($donor->organization_id !== $admin->organization_id) {
            abort(403, 'আনঅথোরাইজড অ্যাক্সেস! আপনি শুধুমাত্র আপনার অর্গানাইজেশনের মেম্বারদের ভেরিফাই করতে পারবেন।');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        // স্ট্যাটাস আপডেট এবং ব্লু ব্যাজ (verified) লজিক
        $donor->nid_status = $request->status;

        if ($request->status === 'approved') {
            $donor->is_onboarded = true; // ডোনার ভেরিফাইড হলে তাকে ফুল্লি অনবোর্ডেড ধরা হবে
            $donor->verified_badge = true; // ব্লু ব্যাজ এনাবেল করা
        } else {
            $donor->verified_badge = false; // রিজেক্ট হলে ব্যাজ রিমুভ করা (নিরাপত্তার জন্য)
        }

        $donor->save();

        $message = $request->status === 'approved'
            ? 'ডোনারকে সফলভাবে ভেরিফাই করা হয়েছে এবং ব্লু ব্যাজ প্রদান করা হয়েছে।'
            : 'ডোনারের ভেরিফিকেশন রিকোয়েস্ট রিজেক্ট করা হয়েছে।';

        return back()->with('success', $message);
    }
}
