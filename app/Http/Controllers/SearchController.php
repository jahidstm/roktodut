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
        $query->orderByDesc('is_ready_now') // যারা ইমার্জেন্সির জন্য রেডি, তারা সবার আগে
            ->orderByDesc('verified_badge') // এরপর ব্লু ব্যাজধারী ভেরিফাইড মেম্বার
            ->orderByRaw("FIELD(nid_status, 'approved', 'pending', 'none')") // এনআইডি স্ট্যাটাস অনুযায়ী
            ->latest(); // সবশেষে নতুন ডোনার

        $donors = $query->paginate(12)->withQueryString();

        // 🎯 ফ্রন্টএন্ড ড্রপডাউনের জন্য ব্লাড গ্রুপের লিস্ট নেওয়া হলো
        $bloodGroups = BloodGroup::cases();

        return view('search.index', compact('donors', 'request', 'bloodGroups'));
    }
}
