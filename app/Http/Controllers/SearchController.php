<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Division;
use App\Models\District;
use App\Models\Upazila;
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
        $onlyAvailable = $request->boolean('only_available', true);

        // 🛡️ বেস কোয়েরি: শুধুমাত্র ডোনার এবং অর্গ-অ্যাডমিনরা
        $query = User::query()
            ->with(['district:id,name', 'upazila:id,name'])
            ->whereIn('users.role', ['donor', 'org_admin'])
            ->when($onlyAvailable, function ($q) {
                $q->where('users.is_available', true) // ম্যানুয়াল অফলাইন স্ট্যাটাস চেক
                    ->where(function ($inner) {
                        // ⚙️ অটো-কুলডাউন ইঞ্জিন (৪ মাসের গ্যাপ চেক)
                        $inner->whereNull('users.cooldown_until')
                            ->orWhere('users.cooldown_until', '<=', now());
                    });
            });

        // 🔍 ১. রক্তের গ্রুপ ফিল্টার
        if ($request->filled('blood_group')) {
            $query->where('users.blood_group', $request->blood_group);
        }

        // 🔍 ২. লোকেশন ফিল্টারস (বিভাগ, জেলা, উপজেলা)
        if ($request->filled('division_id')) {
            $query->where('users.division_id', $request->division_id);
        }
        if ($request->filled('district_id')) {
            $query->where('users.district_id', $request->district_id);
        }
        if ($request->filled('upazila_id')) {
            $query->where('users.upazila_id', $request->upazila_id);
        }

        // 🛡️ ৩. সেলফ ফিল্টার (নিজে খুঁজলে নিজেকে দেখানো হবে না)
        if (Auth::check()) {
            $query->where('users.id', '!=', Auth::id());
        }

        // 🎯 ৪. দ্য স্মার্ট প্রায়োরিটি অ্যালগরিদম (Smart Sorting)
        $query->leftJoin('organizations', 'users.organization_id', '=', 'organizations.id')
            ->select('users.*', 'organizations.status as org_status')
            ->selectRaw("
                (
                    (CASE WHEN users.is_ready_now = 1 THEN 1000 ELSE 0 END) +
                    (CASE WHEN users.organization_id IS NOT NULL AND organizations.status = 'approved' THEN 100 ELSE 0 END) +
                    (CASE WHEN users.nid_status = 'approved' OR users.nid_status = 'verified' THEN 10 ELSE 0 END)
                ) as priority_score
            ")
            ->orderBy('priority_score', 'desc')
            ->orderBy('users.last_donated_at', 'asc') // Tie-breaker: those who haven't donated recently or at all
            ->orderBy('users.created_at', 'desc');

        $donors = $query->paginate(12)->withQueryString();

        // 🎯 ফ্রন্টএন্ড ড্রপডাউনের জন্য ব্লাড গ্রুপের লিস্ট নেওয়া হলো
        $bloodGroups = BloodGroup::cases();

        $selectedFilters = [];
        if ($request->filled('blood_group')) {
            $selectedFilters[] = 'Blood Group: ' . $request->blood_group;
        }
        if ($request->filled('division_id')) {
            $divisionName = Division::whereKey($request->division_id)->value('name');
            if ($divisionName) {
                $selectedFilters[] = 'বিভাগ: ' . $divisionName;
            }
        }
        if ($request->filled('district_id')) {
            $districtName = District::whereKey($request->district_id)->value('name');
            if ($districtName) {
                $selectedFilters[] = 'জেলা: ' . $districtName;
            }
        }
        if ($request->filled('upazila_id')) {
            $upazilaName = Upazila::whereKey($request->upazila_id)->value('name');
            if ($upazilaName) {
                $selectedFilters[] = 'উপজেলা: ' . $upazilaName;
            }
        }
        if ($onlyAvailable) {
            $selectedFilters[] = 'Available';
        }

        return view('search.index', compact('donors', 'request', 'bloodGroups', 'selectedFilters', 'onlyAvailable'));
    }
}
