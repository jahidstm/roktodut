<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\BloodGroup;

class SearchController extends Controller
{
    /**
     * স্মার্ট প্রায়োরিটি-ভিত্তিক রিয়েলটাইম ডোনার সার্চ ইঞ্জিন
     */
    public function index(Request $request)
    {
        // 🛡️ বেস কোয়েরি: শুধুমাত্র ডোনার এবং অর্গ-অ্যাডমিনরা আসবে যারা এই মুহূর্তে অ্যাভেইলেবল
        $query = User::query()
            ->whereIn('role', ['donor', 'org_admin'])
            ->where('is_available', true) // ম্যানুয়াল অফলাইন স্ট্যাটাস চেক
            ->where(function ($q) {
                // ⚙️ অটো-কুলডাউন ইঞ্জিন (৪ মাসের গ্যাপ চেক)
                $q->whereNull('cooldown_until')
                    ->orWhere('cooldown_until', '<=', now());
            });

        // 🔍 ১. রক্তের গ্রুপ ফিল্টার
        if ($request->filled('blood_group')) {
            $query->where('blood_group', $request->blood_group);
        }

        // 🔍 ২. লোকেশন ফিল্টারস (বিভাগ, জেলা, উপজেলা)
        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }
        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }
        if ($request->filled('upazila_id')) {
            $query->where('upazila_id', $request->upazila_id);
        }

        // 🛡️ ৩. সেলফ ফিল্টার (নিজে খুঁজলে নিজেকে দেখানো হবে না)
        if (Auth::check()) {
            $query->where('id', '!=', Auth::id());
        }

        // 🎯 ৪. দ্য স্মার্ট প্রায়োরিটি অ্যালগরিদম (Smart Sorting)
        $query->leftJoin('organizations', 'users.organization_id', '=', 'organizations.id')
            ->select('users.*', 'organizations.verification_status as org_status')
            ->selectRaw("
                (
                    (CASE WHEN users.is_ready_now = 1 THEN 1000 ELSE 0 END) +
                    (CASE WHEN users.organization_id IS NOT NULL AND organizations.verification_status = 'approved' THEN 100 ELSE 0 END) +
                    (CASE WHEN users.nid_status = 'approved' OR users.nid_status = 'verified' THEN 10 ELSE 0 END)
                ) as priority_score
            ")
            ->orderBy('priority_score', 'desc')
            ->orderBy('users.last_donated_at', 'asc') // Tie-breaker: those who haven't donated recently or at all
            ->orderBy('users.created_at', 'desc');

        $donors = $query->paginate(12)->withQueryString();

        // 🎯 ফ্রন্টএন্ড ড্রপডাউনের জন্য ব্লাড গ্রুপের লিস্ট নেওয়া হলো
        $bloodGroups = BloodGroup::cases();

        return view('search.index', compact('donors', 'request', 'bloodGroups'));
    }
}
