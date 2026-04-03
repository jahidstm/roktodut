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
}
