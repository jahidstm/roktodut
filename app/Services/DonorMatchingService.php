<?php

namespace App\Services;

use App\Enums\UrgencyLevel;
use App\Models\BloodRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * DonorMatchingService — Geospatial-First Matching
 *
 * Priority order:
 *   1. Radius-based (Haversine) — hospital lat/lng → ডোনারের lat/lng
 *      Emergency: 10 km | Urgent: 15 km | Normal: 20 km
 *   2. Fallback: same district (existing behaviour) — যখন request-এ lat/lng নেই
 *      বা radius-এ পর্যাপ্ত ডোনার পাওয়া না গেলে।
 *
 * Smart Sorting: Ready Now > Org Verified > NID Verified > Closest Distance > Points
 * Cap: Emergency → 50, Otherwise → 20
 */
class DonorMatchingService
{
    private const CAP_EMERGENCY = 50;
    private const CAP_NORMAL    = 20;
    private const COOLDOWN_DAYS = 120;

    // 📍 Radius per urgency (km)
    private const RADIUS = [
        'emergency' => 10.0,
        'urgent'    => 15.0,
        'normal'    => 20.0,
    ];

    /**
     * নতুন ব্লাড রিকোয়েস্টের জন্য যোগ্য ডোনারদের তালিকা ফেরত দেয়।
     * Radius-based matching → district fallback।
     */
    public function match(BloodRequest $request): Collection
    {
        $cap            = $this->getCap($request->urgency);
        $cooldownCutoff = Carbon::now()->subDays(self::COOLDOWN_DAYS);

        // ── রিকোয়েস্টে lat/lng আছে কিনা চেক করা ──────────────────────────
        $hasCoords = $request->latitude !== null && $request->longitude !== null;

        if ($hasCoords) {
            $urgencyValue = $request->urgency instanceof UrgencyLevel
                ? $request->urgency->value
                : (string) $request->urgency;

            $radiusKm = self::RADIUS[$urgencyValue] ?? self::RADIUS['normal'];

            $donors = $this->buildBaseQuery($request, $cooldownCutoff)
                ->closeTo((float) $request->latitude, (float) $request->longitude, $radiusKm)
                // Smart Sorting: দূরত্ব আগে (scopeCloseTo already sorts by distance)
                // তারপর gamification tier
                ->orderByDesc('is_ready_now')
                ->orderByDesc('verified_badge')
                ->orderByRaw("CASE WHEN nid_status = 'approved' THEN 1 ELSE 0 END DESC")
                ->orderByDesc('points')
                ->limit($cap)
                ->get(['id', 'name', 'blood_group', 'district_id', 'latitude', 'longitude', 'is_ready_now', 'verified_badge', 'distance_km']);

            // ── পর্যাপ্ত ডোনার পাওয়া গেছে? ────────────────────────────────
            if ($donors->count() >= min(5, $cap)) {
                return $donors;
            }

            // ── কম ডোনার পেলে radius দ্বিগুণ করে আবার চেষ্টা (expanded search) ─
            $expandedDonors = $this->buildBaseQuery($request, $cooldownCutoff)
                ->closeTo((float) $request->latitude, (float) $request->longitude, $radiusKm * 2)
                ->orderByDesc('is_ready_now')
                ->orderByDesc('verified_badge')
                ->orderByRaw("CASE WHEN nid_status = 'approved' THEN 1 ELSE 0 END DESC")
                ->orderByDesc('points')
                ->limit($cap)
                ->get(['id', 'name', 'blood_group', 'district_id', 'latitude', 'longitude', 'is_ready_now', 'verified_badge', 'distance_km']);

            if ($expandedDonors->isNotEmpty()) {
                return $expandedDonors;
            }
        }

        // ── Fallback: জেলা-ভিত্তিক পুরনো পদ্ধতি ────────────────────────────
        return $this->buildBaseQuery($request, $cooldownCutoff)
            ->where('district_id', $request->district_id)
            ->orderByDesc('is_ready_now')
            ->orderByDesc('verified_badge')
            ->orderByRaw("CASE WHEN nid_status = 'approved' THEN 1 ELSE 0 END DESC")
            ->orderByDesc('points')
            ->limit($cap)
            ->get(['id', 'name', 'blood_group', 'district_id', 'latitude', 'longitude', 'is_ready_now', 'verified_badge']);
    }

    /**
     * Base query — সব রিকোয়েস্টে common ফিল্টারগুলো।
     */
    private function buildBaseQuery(BloodRequest $request, Carbon $cooldownCutoff)
    {
        return User::query()
            // ✅ রোল ফিল্টার
            ->whereIn('role', ['donor', 'org_admin'])

            // ✅ ব্লাড গ্রুপ ম্যাচ
            ->where('blood_group', $request->blood_group->value)

            // ✅ রিকোয়েস্টকারী নিজে বাদ (guest হলে skip)
            ->when(
                $request->requested_by !== null,
                fn($q) => $q->where('id', '!=', $request->requested_by)
            )

            // ✅ শ্যাডোব্যান্ড ইউজার বাদ
            ->where('is_shadowbanned', false)

            // ✅ যারা availability বন্ধ করেননি
            ->where(function ($q) {
                $q->whereNull('is_available')
                    ->orWhere('is_available', true);
            })

            // ✅ ১২০ দিনের কুলডাউন পার হয়েছে
            ->where(function ($q) use ($cooldownCutoff) {
                $q->whereNull('last_donated_at')
                    ->orWhere('last_donated_at', '<=', $cooldownCutoff);
            });
    }

    /**
     * urgency অনুযায়ী recipient cap নির্ধারণ করে।
     */
    private function getCap(mixed $urgency): int
    {
        $value = $urgency instanceof UrgencyLevel ? $urgency->value : (string) $urgency;

        return $value === UrgencyLevel::EMERGENCY->value
            ? self::CAP_EMERGENCY
            : self::CAP_NORMAL;
    }
}
