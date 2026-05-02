<?php

namespace App\Services;

use App\Enums\UrgencyLevel;
use App\Models\BloodRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * DonorMatchingService
 *
 * রক্তের অনুরোধের সাথে যোগ্য ডোনারদের ম্যাচ করে।
 * Smart Sorting: Ready Now > Org Verified > NID Verified > Regular
 * Cap: Emergency → 50, Otherwise → 20
 */
class DonorMatchingService
{
    private const CAP_EMERGENCY = 50;
    private const CAP_NORMAL    = 20;
    private const COOLDOWN_DAYS = 120;

    /**
     * নতুন ব্লাড রিকোয়েস্টের জন্য যোগ্য ডোনারদের তালিকা ফেরত দেয়।
     */
    public function match(BloodRequest $request): Collection
    {
        $cap            = $this->getCap($request->urgency);
        $cooldownCutoff = Carbon::now()->subDays(self::COOLDOWN_DAYS);

        return User::query()
            // ✅ রোল ফিল্টার: ডোনার এবং অর্গ অ্যাডমিন
            ->whereIn('role', ['donor', 'org_admin'])

            // ✅ লোকেশন ম্যাচ: একই জেলা
            ->where('district_id', $request->district_id)

            // ✅ ব্লাড গ্রুপ ম্যাচ
            ->where('blood_group', $request->blood_group->value)

            // ✅ রিকোয়েস্টকারী নিজে বাদ (guest হলে skip)
            ->when(
                $request->requested_by !== null,
                fn($q) => $q->where('id', '!=', $request->requested_by)
            )

            // ✅ শ্যাডোব্যান্ড ইউজার বাদ
            ->where('is_shadowbanned', false)

            // ✅ যারা availability explicitly বন্ধ করেননি তারা অন্তর্ভুক্ত
            ->where(function ($q) {
                $q->whereNull('is_available')
                    ->orWhere('is_available', true);
            })

            // ✅ ১২০ দিনের কুলডাউন পার হয়েছে (last_donated_at কলাম)
            ->where(function ($q) use ($cooldownCutoff) {
                $q->whereNull('last_donated_at')
                    ->orWhere('last_donated_at', '<=', $cooldownCutoff);
            })

            // 🚀 Smart Sorting (Gamification Tier):
            // ১. is_ready_now = 1 সবার আগে
            // ২. verified_badge = 1 (Org Verified)
            // ৩. nid_status = 'approved' (NID Verified)
            // ৪. points বেশি
            ->orderByDesc('is_ready_now')
            ->orderByDesc('verified_badge')
            ->orderByRaw("CASE WHEN nid_status = 'approved' THEN 1 ELSE 0 END DESC")
            ->orderByDesc('points')

            // 🎯 Cap: অতিরিক্ত নোটিফিকেশন এড়ানো
            ->limit($cap)

            ->get(['id', 'name', 'blood_group', 'district_id', 'is_ready_now', 'verified_badge']);
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
