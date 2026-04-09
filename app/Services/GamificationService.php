<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\BloodRequest;
use App\Models\PointLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class GamificationService
{
    // ==========================================
    // পয়েন্ট কনফিগারেশন
    // ==========================================
    const POINTS_SUCCESSFUL_DONATION    = 50;  // সফল রক্তদান
    const POINTS_FIRST_RESPONDER_BONUS  = 10;  // ৩ ঘণ্টার মধ্যে ইমার্জেন্সিতে রেসপন্ড
    const POINTS_REFERRAL_SIGNUP        = 10;  // রেফারেল সাইন-আপ+প্রোফাইল
    const POINTS_REFERRAL_FIRST_DONATE  = 30;  // রেফারড ব্যক্তির প্রথম রক্তদান
    const POINTS_RECIPIENT_REVIEW       = 10;  // গ্রহীতার পজিটিভ রিভিউ
    const POINTS_PROFILE_COMPLETE       = 20;  // প্রোফাইল ১০০% ও NID ভেরিফাই
    const POINTS_REQUEST_SHARE_DAILY    = 5;   // ব্লাড রিকোয়েস্ট শেয়ার (দিনে ৩ বার পর্যন্ত)

    // ==========================================
    // Anti-Cheat কনফিগারেশন
    // ==========================================

    /** জৈবিক কুলডাউন — মানবদেহে রক্ত পুনর্গঠনে প্রয়োজনীয় ন্যূনতম সময় (দিন) */
    const DONATION_COOLDOWN_DAYS = 120;

    // ==========================================
    // পয়েন্ট দেওয়া
    // ==========================================

    /**
     * যেকোনো কাজে পয়েন্ট দেওয়া + point_logs-এ অডিট রেকর্ড রাখা।
     *
     * @param  User   $user       — যাকে পয়েন্ট দেওয়া হবে
     * @param  int    $points     — কত পয়েন্ট (+/- উভয়ই হতে পারে)
     * @param  string $actionType — PointLog::ACTION_* কনস্ট্যান্ট
     * @param  array  $metadata   — অতিরিক্ত তথ্য (যেমন blood_request_id)
     */
    public function awardPoints(User $user, int $points, string $actionType, array $metadata = []): void
    {
        DB::transaction(function () use ($user, $points, $actionType, $metadata) {
            // ১. users টেবিলে পয়েন্ট যোগ করা
            $user->increment('points', $points);

            // ২. point_logs-এ অডিট ট্রেইল রাখা
            PointLog::create([
                'user_id'     => $user->id,
                'points'      => $points,
                'action_type' => $actionType,
                'metadata'    => $metadata ?: null,
            ]);
        });
    }

    /**
     * সম্পূর্ণ ডোনেশন রিওয়ার্ড প্রসেস:
     *  → Anti-Cheat: ১২০ দিনের জৈবিক কুলডাউন চেক
     *  → total_verified_donations +১
     *  → last_donated_at আপডেট
     *  → +৫০ পয়েন্ট (+ First Responder বোনাস)
     *  → মাসিক পয়েন্ট আপডেট
     *  → ব্যাজ চেক ও আনলক
     *  → রেফারার বোনাস (প্রথম ডোনেশনে)
     *
     * @throws RuntimeException  যদি ১২০ দিনের কুলডাউন পার না হয়
     */
    public function processDonationReward(
        User         $donor,
        BloodRequest $bloodRequest,
        bool         $isFirstResponder = false,
    ): void {
        // ─── ০. Anti-Cheat: ১২০-দিনের জৈবিক কুলডাউন গেটকিপার ─────────
        //
        // বাস্তবে একজন মানুষ প্রতি ৩-৪ মাসে (≈ ১২০ দিন) একবার রক্ত দিতে পারেন।
        // এর আগে পয়েন্ট/ব্যাজ ক্লেইম করার যেকোনো চেষ্টাকে সিস্টেম ব্লক করবে।
        //
        // নতুন ডোনার (last_donated_at = null) → প্রথমবার বলে বাইপাস করা হবে।
        $this->enforceDonationCooldown($donor);

        // ─── ১. ডোনেশন কাউন্ট ও তারিখ আপডেট ──────────────────────────
        $donor->increment('total_verified_donations');
        $donor->update(['last_donated_at' => now()->toDateString()]);
        $donor->refresh();

        // ─── ২. মূল ডোনেশন পয়েন্ট (+৫০) ──────────────────────────────
        $this->awardPoints(
            user:       $donor,
            points:     self::POINTS_SUCCESSFUL_DONATION,
            actionType: PointLog::ACTION_DONATION_COMPLETED,
            metadata:   [
                'blood_request_id' => $bloodRequest->id,
                'blood_group'      => $bloodRequest->blood_group,
                'district_id'      => $bloodRequest->district_id,
            ],
        );
        $this->updateMonthlyPoints($donor, self::POINTS_SUCCESSFUL_DONATION);

        // ─── ৩. First Responder বোনাস (+১০) — ৩ ঘণ্টার মধ্যে রেসপন্ড ──
        if ($isFirstResponder) {
            $this->awardPoints(
                user:       $donor,
                points:     self::POINTS_FIRST_RESPONDER_BONUS,
                actionType: PointLog::ACTION_FIRST_RESPONDER_BONUS,
                metadata:   ['blood_request_id' => $bloodRequest->id],
            );
            $this->updateMonthlyPoints($donor, self::POINTS_FIRST_RESPONDER_BONUS);
        }

        // ─── ৪. ব্যাজ চেক ও আনলক ─────────────────────────────────────
        $this->checkAndAwardBadges($donor);

        // ─── ৫. রেফারার বোনাস (প্রথম ডোনেশনেই কেবল) ──────────────────
        if ($donor->total_verified_donations === 1 && $donor->referred_by) {
            $referrer = User::find($donor->referred_by);
            if ($referrer) {
                $this->awardPoints(
                    user:       $referrer,
                    points:     self::POINTS_REFERRAL_FIRST_DONATION,
                    actionType: PointLog::ACTION_REFERRAL_FIRST_DONATION,
                    metadata:   ['referred_donor_id' => $donor->id],
                );
                $this->checkAndAwardBadges($referrer);
            }
        }
    }

    // ==========================================
    // Anti-Cheat: কুলডাউন এনফোর্সমেন্ট
    // ==========================================

    /**
     * ১২০-দিনের জৈবিক কুলডাউন নিশ্চিত করে।
     *
     * নিয়ম:
     *  • last_donated_at = null  → প্রথম ডোনেশন, পাস করো।
     *  • last_donated_at + 120 দিন > আজ → এখনো কুলডাউনে আছেন, ব্লক করো।
     *  • last_donated_at + 120 দিন ≤ আজ → কুলডাউন শেষ, পাস করো।
     *
     * @throws RuntimeException  কুলডাউন পার না হলে
     */
    private function enforceDonationCooldown(User $donor): void
    {
        // নতুন ডোনার — কোনো পূর্ববর্তী ডোনেশন নেই, বাইপাস করো
        if (is_null($donor->last_donated_at)) {
            return;
        }

        $lastDonation  = Carbon::parse($donor->last_donated_at)->startOfDay();
        $cooldownEnds  = $lastDonation->copy()->addDays(self::DONATION_COOLDOWN_DAYS);
        $today         = Carbon::today();

        if ($today->lt($cooldownEnds)) {
            $daysRemaining = (int) $today->diffInDays($cooldownEnds);

            // Security log — সন্দেহজনক রিকোয়েস্ট লগ করো
            Log::warning('[GamificationService] Anti-Cheat: Donation cooldown violation detected.', [
                'user_id'         => $donor->id,
                'last_donated_at' => $donor->last_donated_at,
                'cooldown_ends'   => $cooldownEnds->toDateString(),
                'days_remaining'  => $daysRemaining,
                'ip'              => request()->ip(),
            ]);

            throw new RuntimeException(
                "অ্যান্টি-চিট: কুলডাউন এখনো শেষ হয়নি। "
                . "পরবর্তী ডোনেশন রিওয়ার্ড পাওয়া যাবে {$daysRemaining} দিন পরে "
                . "({$cooldownEnds->toDateString()})।"
            );
        }
    }

    /**
     * পুরনো মেথড — এখনো ব্যবহারযোগ্য (DonationClaimController compatibility)
     * @deprecated processDonationReward() ব্যবহার করুন
     */
    public function awardDonationPoints(User $donor, bool $isFirstResponder = false): void
    {
        $this->processDonationReward(
            donor:            $donor,
            bloodRequest:     new BloodRequest(), // fallback, নতুন কোডে event দিয়ে call করুন
            isFirstResponder: $isFirstResponder,
        );
    }

    public function awardReferralSignupPoints(User $referrer): void
    {
        $this->addPoints($referrer, self::POINTS_REFERRAL_SIGNUP);
        $this->checkAndAwardBadges($referrer);
    }

    public function awardReviewPoints(User $donor): void
    {
        $this->addPoints($donor, self::POINTS_RECIPIENT_REVIEW);
        $this->checkAndAwardBadges($donor);
    }

    public function awardProfileCompletionPoints(User $user): void
    {
        // শুধুমাত্র একবার দেওয়া হবে
        if ($user->points === 0 || !$user->badges()->where('name', 'profile_complete_bonus')->exists()) {
            $this->addPoints($user, self::POINTS_PROFILE_COMPLETE);
            $this->checkAndAwardBadges($user);
        }
    }

    // ==========================================
    // ব্যাজ চেকিং ও প্রদান
    // ==========================================

    public function checkAndAwardBadges(User $user): void
    {
        $user->refresh(); // সর্বশেষ ডেটা নিন

        $this->checkMilestoneBadges($user);
        $this->checkSpecialBadges($user);
    }

    private function checkMilestoneBadges(User $user): void
    {
        $donations = $user->total_verified_donations ?? 0;
        $points    = $user->points ?? 0;

        // Bronze Bloodline: ১ম ডোনেশন বা ৫০ পয়েন্ট
        if ($donations >= 1 || $points >= 50) {
            $this->awardBadgeIfNotOwned($user, 'bronze_bloodline');
        }

        // Silver Savior: ৫ ডোনেশন বা ৩০০ পয়েন্ট
        if ($donations >= 5 || $points >= 300) {
            $this->awardBadgeIfNotOwned($user, 'silver_savior');
        }

        // Golden Guardian: ১০ ডোনেশন বা ৬০০ পয়েন্ট
        if ($donations >= 10 || $points >= 600) {
            $this->awardBadgeIfNotOwned($user, 'golden_guardian');
        }

        // Platinum Hero: ২০+ ডোনেশন বা ১৫০০+ পয়েন্ট
        if ($donations >= 20 || $points >= 1500) {
            $this->awardBadgeIfNotOwned($user, 'platinum_hero');
        }
    }

    private function checkSpecialBadges(User $user): void
    {
        // Campus Hero: .edu বা .ac.bd ইমেইল
        $email = $user->edu_email ?? $user->email ?? '';
        if (str_ends_with($email, '.edu') || str_contains($email, '.ac.bd') || str_ends_with($email, '.edu.bd')) {
            $this->awardBadgeIfNotOwned($user, 'campus_hero');
            if (!$user->is_campus_hero) {
                $user->update(['is_campus_hero' => true]);
            }
        }

        // Verified Donor: NID ভেরিফাইড
        if ($user->nid_status === 'verified' || $user->verified_badge) {
            $this->awardBadgeIfNotOwned($user, 'verified_donor');
        }

        // Ready Now: ইমার্জেন্সি মোড চালু
        if ($user->is_ready_now) {
            $this->awardBadgeIfNotOwned($user, 'ready_now');
        }

        // Rare Blood Hero: নেগেটিভ ব্লাড গ্রুপ + ডোনেশন করেছেন
        $bloodGroup = $user->blood_group?->value ?? $user->blood_group ?? '';
        if (str_contains((string)$bloodGroup, '-') && ($user->total_verified_donations ?? 0) >= 1) {
            $this->awardBadgeIfNotOwned($user, 'rare_blood_hero');
        }
    }

    private function awardBadgeIfNotOwned(User $user, string $badgeSlug): void
    {
        $badge = Badge::where('name', $badgeSlug)->first();
        if (!$badge) return;

        $alreadyHas = $user->badges()->where('badge_id', $badge->id)->exists();
        if (!$alreadyHas) {
            $user->badges()->attach($badge->id, [
                'earned_at'  => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // ==========================================
    // লিডারবোর্ড
    // ==========================================

    public function getLeaderboard(string $scope = 'national', ?int $districtId = null, string $period = 'all_time', int $limit = 50)
    {
        $query = User::where('role', 'donor')
            ->where(function ($q) {
                $q->where('total_verified_donations', '>', 0)
                  ->orWhere('points', '>', 0);
            })
            ->with(['badges', 'district']);

        if ($scope === 'district' && $districtId) {
            $query->where('district_id', $districtId);
        }

        if ($period === 'monthly') {
            $currentMonth = now()->format('Y-m');
            $query->where('monthly_points_month', $currentMonth)
                  ->orderByDesc('monthly_points')
                  ->orderByDesc('total_verified_donations');
        } else {
            // All-time: প্রথমে ডোনেশন কাউন্ট, তারপর পয়েন্ট
            $query->orderByDesc('total_verified_donations')
                  ->orderByDesc('points');
        }

        return $query->limit($limit)->get();
    }

    // ==========================================
    // রেফারেল কোড
    // ==========================================

    public function generateReferralCode(User $user): string
    {
        if ($user->referral_code) {
            return $user->referral_code;
        }

        do {
            $code = strtoupper(Str::random(4)) . $user->id;
        } while (User::where('referral_code', $code)->exists());

        $user->update(['referral_code' => $code]);
        return $code;
    }

    public function processReferral(User $newUser, string $referralCode): void
    {
        $referrer = User::where('referral_code', $referralCode)->first();
        if (!$referrer || $referrer->id === $newUser->id) return;

        $newUser->update(['referred_by' => $referrer->id]);
        // নতুন ইউজার প্রোফাইল কমপ্লিট করলে রেফারার পয়েন্ট পাবে
    }

    // ==========================================
    // Private Helpers
    // ==========================================

    /**
     * @deprecated awardPoints() ব্যবহার করুন (DB logging নেই এতে)
     */
    private function addPoints(User $user, int $amount): void
    {
        $user->increment('points', $amount);
    }

    private function updateMonthlyPoints(User $user, int $amount): void
    {
        $currentMonth = now()->format('Y-m');

        // মাস বদলে গেলে রিসেট করো
        if ($user->monthly_points_month !== $currentMonth) {
            $user->update([
                'monthly_points'       => $amount,
                'monthly_points_month' => $currentMonth,
            ]);
        } else {
            $user->increment('monthly_points', $amount);
        }
    }

    // ব্যাজের ডিসপ্লে ডেটা হেল্পার
    public static function getBadgeDisplayData(string $slug): array
    {
        return match ($slug) {
            'bronze_bloodline' => [
                'label' => 'Bronze Bloodline',
                'bn'    => 'ব্রোঞ্জ ব্লাডলাইন',
                'emoji' => '🥉',
                'color' => 'text-amber-700 bg-amber-50 border-amber-200',
                'glow'  => 'shadow-amber-200',
            ],
            'silver_savior' => [
                'label' => 'Silver Savior',
                'bn'    => 'সিলভার সেভিয়ার',
                'emoji' => '🥈',
                'color' => 'text-slate-600 bg-slate-50 border-slate-200',
                'glow'  => 'shadow-slate-200',
            ],
            'golden_guardian' => [
                'label' => 'Golden Guardian',
                'bn'    => 'গোল্ডেন গার্ডিয়ান',
                'emoji' => '🏅',
                'color' => 'text-yellow-700 bg-yellow-50 border-yellow-200',
                'glow'  => 'shadow-yellow-200',
            ],
            'platinum_hero' => [
                'label' => 'Platinum Hero',
                'bn'    => 'প্লাটিনাম হিরো',
                'emoji' => '🏆',
                'color' => 'text-purple-700 bg-purple-50 border-purple-200',
                'glow'  => 'shadow-purple-300',
            ],
            'campus_hero' => [
                'label' => 'Campus Hero',
                'bn'    => 'ক্যাম্পাস হিরো',
                'emoji' => '🎓',
                'color' => 'text-blue-700 bg-blue-50 border-blue-200',
                'glow'  => 'shadow-blue-200',
            ],
            'verified_donor' => [
                'label' => 'Verified Donor',
                'bn'    => 'ভেরিফাইড ডোনার',
                'emoji' => '🛡️',
                'color' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
                'glow'  => 'shadow-emerald-200',
            ],
            'ready_now' => [
                'label' => 'Ready Now',
                'bn'    => 'রেডি নাউ',
                'emoji' => '⚡',
                'color' => 'text-orange-700 bg-orange-50 border-orange-200',
                'glow'  => 'shadow-orange-200',
            ],
            'rare_blood_hero' => [
                'label' => 'Rare Blood Hero',
                'bn'    => 'রেয়ার ব্লাড হিরো',
                'emoji' => '💎',
                'color' => 'text-pink-700 bg-pink-50 border-pink-200',
                'glow'  => 'shadow-pink-200',
            ],
            'midnight_savior' => [
                'label' => 'Midnight Savior',
                'bn'    => 'মিডনাইট সেভিয়ার',
                'emoji' => '🌙',
                'color' => 'text-indigo-700 bg-indigo-50 border-indigo-200',
                'glow'  => 'shadow-indigo-200',
            ],
            default => [
                'label' => $slug,
                'bn'    => $slug,
                'emoji' => '🏅',
                'color' => 'text-slate-600 bg-slate-50 border-slate-200',
                'glow'  => 'shadow-slate-200',
            ],
        };
    }
}
