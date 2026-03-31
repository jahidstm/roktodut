<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * আইডি-ভিত্তিক রিয়েলটাইম ডোনার সার্চ ইঞ্জিন
     */
    public function index(Request $request)
    {
        // 🛡️ বেস কোয়েরি: শুধুমাত্র ভেরিফাইড এবং ডোনাররা আসবে
        $query = User::query()
            ->whereIn('role', ['donor', 'org_admin'])
            ->where('is_verified', true);

        // 🔍 ১. রক্তের গ্রুপ ফিল্টার (String Match)
        if ($request->filled('blood_group')) {
            $query->where('blood_group', $request->blood_group);
        }

        // 🔍 ২. বিভাগ ফিল্টার (ID Match)
        if ($request->filled('division')) {
            $query->where('division_id', $request->division);
        }

        // 🔍 ৩. জেলা ফিল্টার (ID Match)
        if ($request->filled('district')) {
            $query->where('district_id', $request->district);
        }

        // 🔍 ৪. উপজেলা ফিল্টার (ID Match)
        if ($request->filled('upazila')) {
            $query->where('upazila_id', $request->upazila);
        }

        // ⚙️ ৫. ৯০ দিনের ডোনেশন গ্যাপ অ্যালগরিদম
        $ninetyDaysAgo = Carbon::now()->subDays(90);

        $query->where(function ($q) use ($ninetyDaysAgo) {
            $q->whereNull('last_donation_date')
              ->orWhere('last_donation_date', '<=', $ninetyDaysAgo);
        });

        // 🛡️ ৬. সেলফ ফিল্টার
        if (Auth::check()) {
            $query->where('id', '!=', Auth::id());
        }

        // রেজাল্ট এবং প্যাগিনেশন
        $donors = $query->latest()->paginate(12)->withQueryString();

        return view('search.index', compact('donors', 'request'));
    }
}