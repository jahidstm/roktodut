<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HospitalController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // API: নতুন (unverified) হসপিটাল তৈরি করা (POST /api/hospitals)
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
        ]);

        // Same-name duplicate check (case-insensitive)
        $existing = Hospital::whereRaw('LOWER(name) = ?', [strtolower($request->name)])->first();

        if ($existing) {
            return response()->json([
                'id'          => $existing->id,
                'name'        => $existing->name,
                'display'     => $existing->name_bn ?? $existing->name,
                'is_verified' => $existing->is_verified,
            ]);
        }

        $hospital = Hospital::create([
            'name'        => $request->name,
            'is_verified' => false,
        ]);

        return response()->json([
            'id'          => $hospital->id,
            'name'        => $hospital->name,
            'display'     => $hospital->name,
            'is_verified' => false,
        ], 201);
    }

    // ─────────────────────────────────────────────────────────────
    // API: Autocomplete Search (GET /api/hospitals/search?q=xxx)
    // ─────────────────────────────────────────────────────────────
    public function search(Request $request)
    {
        $q          = (string) $request->input('q', '');
        $districtId = $request->input('district_id');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $cacheKey = 'hospital_search:' . md5($q . '|' . $districtId);

        $results = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($q, $districtId) {
            $query = Hospital::search($q)
                ->select('id', 'name', 'name_bn', 'aliases', 'district_id', 'is_verified')
                ->orderByDesc('is_verified') // verified প্রথমে দেখাবে
                ->limit(8);

            // ফিল্টার: একই জেলার হসপিটাল আগে (optional boost)
            if ($districtId) {
                $query->orderByRaw("CASE WHEN district_id = ? THEN 0 ELSE 1 END", [$districtId]);
            }

            return $query->get()->map(fn($h) => [
                'id'          => $h->id,
                'name'        => $h->name,
                'name_bn'     => $h->name_bn,
                'display'     => $h->name_bn ?? $h->name,
                'is_verified' => $h->is_verified,
            ]);
        });

        return response()->json($results);
    }

    // ─────────────────────────────────────────────────────────────
    // Admin: Unverified hospitals list
    // ─────────────────────────────────────────────────────────────
    public function unverified()
    {
        $hospitals = Hospital::unverified()
            ->with('district:id,name')
            ->withCount('bloodRequests')
            ->orderByDesc('blood_requests_count')
            ->paginate(20);

        return view('admin.hospitals.unverified', compact('hospitals'));
    }

    // ─────────────────────────────────────────────────────────────
    // Admin: Verify a hospital (PATCH)
    // ─────────────────────────────────────────────────────────────
    public function verify(Request $request, Hospital $hospital)
    {
        $request->validate([
            'name'    => 'required|string|max:200',
            'name_bn' => 'nullable|string|max:200',
        ]);

        $hospital->update([
            'name'        => $request->name,
            'name_bn'     => $request->name_bn,
            'is_verified' => true,
        ]);

        // Search cache invalidate করা
        Cache::flush();

        return back()->with('success', '✅ হাসপাতালটি ভেরিফাই করা হয়েছে।');
    }

    // ─────────────────────────────────────────────────────────────
    // Admin: Delete a duplicate/spam unverified hospital
    // ─────────────────────────────────────────────────────────────
    public function destroy(Hospital $hospital)
    {
        abort_if($hospital->is_verified, 403, 'Verified হাসপাতাল ডিলিট করা যাবে না।');

        $hospital->delete();
        return back()->with('success', 'হাসপাতালটি মুছে ফেলা হয়েছে।');
    }
}
