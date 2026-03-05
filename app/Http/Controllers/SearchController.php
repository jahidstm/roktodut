<?php

namespace App\Http\Controllers;

use App\Enums\BloodGroup;
use App\Enums\UserRole;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $bloodGroups = BloodGroup::cases();
        $bloodGroupValues = array_map(fn($c) => $c->value, $bloodGroups);

        // only run query when required inputs exist
        $hasQuery = $request->filled('district') && $request->filled('blood_group');

        $request->validate([
            'division' => ['nullable', 'string', 'max:100'], // UI-only
            'district' => [$hasQuery ? 'required' : 'nullable', 'string', 'max:100'],
            'upazila'  => ['nullable', 'string', 'max:100'],
            'blood_group' => [$hasQuery ? 'required' : 'nullable', 'string', Rule::in($bloodGroupValues)],
        ]);

        $donors = collect();

        if ($hasQuery) {
            $now = Carbon::now();

            $q = User::query()
                ->where('role', UserRole::DONOR->value)
                ->where('is_available', true)
                ->where('blood_group', $request->input('blood_group'))
                ->where('district', $request->input('district'))
                ->where(function ($sub) use ($now) {
                    $sub->whereNull('cooldown_until')
                        ->orWhere('cooldown_until', '<', $now);
                });

            if ($request->filled('upazila')) {
                $q->where('upazila', $request->input('upazila'));
            }

            // ranking
            $q->orderByDesc('is_ready_now')
              ->orderByDesc('verified_badge')
              ->orderByRaw("CASE WHEN nid_status = 'approved' THEN 1 ELSE 0 END DESC")
              ->orderByDesc('total_donations')
              ->orderByRaw("COALESCE(last_login_at, '1970-01-01 00:00:00') DESC");

            $donors = $q->limit(50)->get();
        }

        return view('search.index', [
            'bloodGroups' => $bloodGroups,
            'donors' => $donors,
            'query' => $request->only(['division', 'district', 'upazila', 'blood_group']),
        ]);
    }
}