<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon; // 🎯 তারিখ ক্যালকুলেশনের জন্য
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * স্মার্ট ডোনার সার্চ এবং ম্যাচিং অ্যালগরিদম
     */
    public function index(Request $request)
    {
        // 🛡️ বেস কোয়েরি: শুধুমাত্র ভেরিফাইড এবং যারা ডোনার হিসেবে রেজিস্টার্ড
        $query = User::query()
            ->whereIn('role', ['donor', 'org_admin']) // অ্যাডমিনরাও ডোনার হতে পারে
            ->where('is_verified', true);

        // 🔍 ১. রক্তের গ্রুপ ফিল্টার (Exact Match)
        if ($request->filled('blood_group')) {
            $query->where('blood_group', $request->blood_group);
        }

        // 🔍 ২. জেলা ফিল্টার
        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        // 🔍 ৩. উপজেলা বা এরিয়া ফিল্টার
        if ($request->filled('area')) {
            $query->where('thana', 'LIKE', '%' . $request->area . '%');
        }

        // ⚙️ ৪. Availability Logic (The Smart Algorithm)
        // রুল: একজন ডোনার রক্ত দেওয়ার ৯০ দিন (৩ মাস) পর পুনরায় রক্ত দিতে পারবেন।
        $ninetyDaysAgo = Carbon::now()->subDays(90);

        $query->where(function ($q) use ($ninetyDaysAgo) {
            $q->whereNull('last_donation_date') // ক. যিনি আগে কখনো প্ল্যাটফর্মে রক্ত দেননি
              ->orWhere('last_donation_date', '<=', $ninetyDaysAgo); // খ. যার রক্তদানের ৯০ দিন পার হয়েছে
        });

        // 🛡️ ৫. সেলফ-ফিল্টার: লগইন থাকলে নিজের নাম যেন সার্চ লিস্টে না আসে
        if (Auth::check()) {
            $query->where('id', '!=', Auth::id());
        }

        // 🚀 এক্সিকিউশন ও প্যাগিনেশন (প্রতি পেজে ১২ জন)
        // withQueryString() দেওয়া হয়েছে যাতে ২য় পেজে গেলেও ইউজারের ফিল্টারগুলো ঠিক থাকে
        $donors = $query->latest()->paginate(12)->withQueryString();

        // 🎨 ফ্রন্টএন্ড টিমের জন্য ডেটা পাস করা হলো (ভিউ ফাইল আলিফ বানাবে)
        return view('search.index', compact('donors'));
    }
}