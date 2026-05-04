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

        // Merge target list: only verified hospitals
        $verified = Hospital::verified()
            ->select('id', 'name', 'name_bn')
            ->orderBy('name')
            ->get();

        return view('admin.hospitals.unverified', compact('hospitals', 'verified'));
    }

    // ─────────────────────────────────────────────────────────────
    // Admin: Flow 1 — Merge with existing (PATCH)
    // ─────────────────────────────────────────────────────────────
    public function merge(Request $request, Hospital $hospital)
    {
        $request->validate([
            'target_hospital_id' => 'required|integer|exists:hospitals,id|different:hospital',
        ]);

        abort_if($hospital->is_verified, 403, 'Verified হাসপাতাল merge করা যাবে না।');

        $target = Hospital::findOrFail($request->target_hospital_id);

        \Illuminate\Support\Facades\DB::transaction(function () use ($hospital, $target) {
            // ১. সব blood_requests-এর hospital_id পরিবর্তন করা
            \App\Models\BloodRequest::where('hospital_id', $hospital->id)
                ->update(['hospital_id' => $target->id]);

            // ২. টাইপোটি target-এর aliases-এ যোগ করা (self-learning!)
            $aliases = $target->aliases ?? [];
            if (!in_array($hospital->name, $aliases)) {
                $aliases[] = $hospital->name;
                $target->update(['aliases' => $aliases]);
            }

            // ৩. আনভেরিফাইড এন্ট্রি ডিলিট
            $hospital->delete();
        });

        Cache::flush();
        return back()->with('success', "✅ মার্জ সম্পন্ন! '{$hospital->name}' → '{$target->name}' এ সফলভাবে মার্জ হয়েছে।");
    }

    // ─────────────────────────────────────────────────────────────
    // Admin: Flow 2 — Approve as New (PATCH)
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

        Cache::flush();
        return back()->with('success', "✅ '{$hospital->name}' সফলভাবে ভেরিফাই করা হয়েছে।");
    }

    // ─────────────────────────────────────────────────────────────
    // Admin: Flow 3 — Reject & Nullify (DELETE — safe)
    // ─────────────────────────────────────────────────────────────
    public function destroy(Hospital $hospital)
    {
        abort_if($hospital->is_verified, 403, 'Verified হাসপাতাল ডিলিট করা যাবে না।');

        $name = $hospital->name;

        // nullOnDelete() migration constraint দ্বারা hospital_id স্বয়ংক্রিয় null হবে
        // তবুও explicit করে null করা — extra safety layer
        \App\Models\BloodRequest::where('hospital_id', $hospital->id)
            ->update(['hospital_id' => null]);

        $hospital->delete();
        Cache::flush();

        return back()->with('success', "🗑️ '{$name}' প্রত্যাখ্যান করা হয়েছে। সংশ্লিষ্ট রিকোয়েস্টগুলো অক্ষত আছে।");
    }
}
