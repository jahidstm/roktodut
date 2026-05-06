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
    const POINTS_REFERRAL_FIRST_DONATION = 30;  // রেফারড ব্যক্তির প্রথম রক্তদান
    const POINTS_RECIPIENT_REVIEW       = 10;  // গ্রহীতার পজিটিভ রিভিউ
    const POINTS_PROFILE_COMPLETE       = 20;  // প্রোফাইল ১০০% ও NID ভেরিফাই
    const POINTS_REQUEST_SHARE_DAILY    = 5;   // ব্লাড রিকোয়েস্ট শেয়ার (দিনে ৩ বার পর্যন্ত)

    // ==========================================
    // Anti-Cheat কনফিগারেশন
    // ==========================================

    /** জৈবিক কুলডাউন — মানবদেহে রক্ত পুনর্গঠনে প্রয়োজনীয় ন্যূনতম সময় (দিন) */
    const DONATION_COOLDOWN_DAYS = 120;

    // ==========================================
    // প্রোফাইল কমপ্লিশন ও Emergency Mode
    // ==========================================

    /**
     * প্রোফাইল ১০০% সম্পূর্ণ হলে একবারের জন্য +২০ পয়েন্ট দেওয়া।
     * Idempotent — একই ইউজারকে দ্বিতীয়বার দেবে না।
     */
    public function awardProfileCompletionBonus(User $user): bool
    {
        // ইতোমধ্যে পুরস্কার পেয়েছে কি না চেক করা
        $alreadyAwarded = PointLog::where('user_id', $user->id)
            ->where('action_type', PointLog::ACTION_PROFILE_COMPLETION)
            ->exists();

        if ($alreadyAwarded) {
            return false; // দ্বিতীয়বার দেব না
        }

        $this->awardPoints(
            user:       $user,
            points:     self::POINTS_PROFILE_COMPLETE,
            actionType: PointLog::ACTION_PROFILE_COMPLETION,
            metadata:   ['reason' => 'প্রোফাইল ১০০% সম্পূর্ণ করা হয়েছে।'],
        );
        
        $user->notify(new \App\Notifications\GamificationRewardNotification(
            title: '✅ প্রোফাইল ১০০% কমপ্লিট!',
            message: 'অভিনন্দন! আপনার প্রোফাইল সফলভাবে ১০০% সম্পূর্ণ হয়েছে। বোনাস হিসেবে আপনি ' . self::POINTS_PROFILE_COMPLETE . ' পয়েন্ট পেয়েছেন।',
            points: self::POINTS_PROFILE_COMPLETE
        ));

        Log::info("GamificationService: Profile completion bonus awarded.", ['user_id' => $user->id]);

        // রেফারেল সাইন আপ বোনাস (নতুন ইউজার প্রোফাইল ১০০% করলে রেফারার পয়েন্ট পাবে)
        if ($user->referred_by) {
            $referrer = User::find($user->referred_by);
            if ($referrer) {
                $this->awardReferralSignupPoints($referrer);
            }
        }

        return true;
    }

    /**
     * Emergency Mode (is_available) চালু/বন্ধের সাথে "Ready Now" ব্যাজ সিঙ্ক করা।
     *  - $isAvailable = true  → ব্যাজ যুক্ত করো (যদি না থাকে)
     *  - $isAvailable = false → ব্যাজ রিমুভ করো
     */
    public function handleReadyNowBadge(User $user, bool $isAvailable): void
    {
        $badge = Badge::where('name', 'ready_now')->first();

        if (!$badge) {
            Log::warning("GamificationService: 'ready_now' badge not found in DB.");
            return;
        }

        if ($isAvailable) {
            // Attach — syncWithoutDetaching() ব্যবহার করলে duplicate হবে না
            $user->badges()->syncWithoutDetaching([$badge->id]);
        } else {
            $user->badges()->detach($badge->id);
        }
    }

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
        bool         $isMidnightSavior = false,
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
            
            $donor->notify(new \App\Notifications\GamificationRewardNotification(
                title: '⚡ First Responder বোনাস!',
                message: 'আপনি ইমার্জেন্সি রিকোয়েস্টে ৩ ঘণ্টার মধ্যে দ্রুত রেসপন্ড করে রক্তদান করায় বিশেষ ' . self::POINTS_FIRST_RESPONDER_BONUS . ' পয়েন্ট বোনাস পেয়েছেন!',
                points: self::POINTS_FIRST_RESPONDER_BONUS
            ));
        }

        // ─── ৩.৫ Midnight Savior 배지 (রাত ১২টা - ভোর ৫টা) ──────────
        if ($isMidnightSavior) {
            $this->awardBadgeIfNotOwned($donor, 'midnight_savior');
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
                
                $referrer->notify(new \App\Notifications\GamificationRewardNotification(
                    title: '🎁 স্পেশাল রেফারেল বোনাস!',
                    message: 'আপনার রেফার করা ব্যক্তি জীবনে প্রথমবার রক্তদান করেছেন! এই বিশেষ অর্জনের জন্য আপনি ' . self::POINTS_REFERRAL_FIRST_DONATION . ' পয়েন্ট বোনাস পেয়েছেন।',
                    points: self::POINTS_REFERRAL_FIRST_DONATION
                ));
                
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
        $this->awardPoints(
            user:       $referrer,
            points:     self::POINTS_REFERRAL_SIGNUP,
            actionType: PointLog::ACTION_REFERRAL_SIGNUP,
            metadata:   ['reason' => 'রেফারেল সাইন-আপ বোনাস'],
        );
        
        $referrer->notify(new \App\Notifications\GamificationRewardNotification(
            title: '🎉 রেফারেল বোনাস অর্জিত!',
            message: 'আপনার রেফারেল কোড ব্যবহার করে একজন নতুন বন্ধু যুক্ত হয়েছেন। আপনি ' . self::POINTS_REFERRAL_SIGNUP . ' পয়েন্ট পেয়েছেন!',
            points: self::POINTS_REFERRAL_SIGNUP
        ));
        
        $this->checkAndAwardBadges($referrer);
    }

    public function awardReviewPoints(User $donor): void
    {
        $this->awardPoints(
            user:       $donor,
            points:     self::POINTS_RECIPIENT_REVIEW,
            actionType: PointLog::ACTION_RECIPIENT_REVIEW,
            metadata:   ['reason' => 'গ্রহীতার পজিটিভ রিভিউ'],
        );
        
        $donor->notify(new \App\Notifications\GamificationRewardNotification(
            title: '💬 পজিটিভ রিভিউ বোনাস!',
            message: 'রক্তগ্রহীতা আপনার রক্তদান নিশ্চিত করেছেন এবং পজিটিভ ফিডব্যাক দিয়েছেন। আপনি ' . self::POINTS_RECIPIENT_REVIEW . ' পয়েন্ট পেয়েছেন!',
            points: self::POINTS_RECIPIENT_REVIEW
        ));
        
        $this->checkAndAwardBadges($donor);
    }

    public function awardProfileCompletionPoints(User $user): void
    {
        $this->awardProfileCompletionBonus($user);
        $this->checkAndAwardBadges($user);
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
            
            // Send notification for badge unlock
            $user->notify(new \App\Notifications\GamificationRewardNotification(
                title: '🏅 নতুন ব্যাজ আনলক হয়েছে!',
                message: "অভিনন্দন! আপনি '{$badge->name_bn}' ব্যাজটি অর্জন করেছেন।",
                points: 0 // No points, just a badge
            ));
        }
    }

    // ==========================================
    // লিডারবোর্ড
    // ==========================================

    /**
     * লিডারবোর্ড কোয়েরি — সময় ও অঞ্চল ফিল্টার সহ।
     *
     * @param  string   $scope      'bd' বা 'district' (পুরনো: 'national')
     * @param  int|null $districtId জেলার ID (scope=district হলে)
     * @param  string   $time       'all' বা 'month' (পুরনো: 'all_time'/'monthly')
     * @param  int      $limit      সর্বোচ্চ কতজন (ডিফল্ট ৫০)
     */
    public function getLeaderboard(string $scope = 'bd', ?int $districtId = null, string $time = 'all', int $limit = 50)
    {
        // পুরনো param মান normalize করো (backward compat)
        if ($time === 'all_time') $time = 'all';
        if ($time === 'monthly')  $time = 'month';
        if ($scope === 'national') $scope = 'bd';

        $query = User::where('role', 'donor')
            ->notShadowbanned()
            ->where(function ($q) {
                $q->where('total_verified_donations', '>', 0)
                  ->orWhere('points', '>', 0);
            })
            ->with(['badges', 'district']);

        // অঞ্চল ফিল্টার
        if ($scope === 'district' && $districtId) {
            $query->where('district_id', $districtId);
        }

        // সময় ফিল্টার
        if ($time === 'month') {
            $currentMonth = now()->format('Y-m');
            $query->where('monthly_points_month', $currentMonth)
                  ->orderByDesc('monthly_points')
                  ->orderByDesc('total_verified_donations');
        } else {
            // সর্বকাল: প্রথমে ডোনেশন কাউন্ট, তারপর পয়েন্ট
            $query->orderByDesc('total_verified_donations')
                  ->orderByDesc('points');
        }

        return $query->limit($limit)->get();
    }

    /**
     * পোডিয়ামের জন্য শীর্ষ ৩ ডোনার।
     * getLeaderboard() এর মতোই কিন্তু limit=3।
     */
    public function getTop3(string $scope = 'bd', ?int $districtId = null, string $time = 'all')
    {
        return $this->getLeaderboard($scope, $districtId, $time, 3);
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
            'camp_donor' => [
                'label' => 'Camp Donor',
                'bn'    => 'ক্যাম্প ডোনার',
                'emoji' => '🏕️',
                'color' => 'text-teal-700 bg-teal-50 border-teal-200',
                'glow'  => 'shadow-teal-200',
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

    // =========================================================
    // NID ভেরিফিকেশন ব্যাজ — Org Admin ও System Admin উভয়ই ব্যবহার করে
    // =========================================================

    /**
     * NID verified হলে 'verified_donor' ব্যাজ দাও।
     * idempotent — ইউজারের কাছে ইতিমধ্যে ব্যাজ থাকলে duplicate হবে না।
     */
    public function awardVerifiedBadge(User $user): void
    {
        $badge = \App\Models\Badge::where('name', 'verified_donor')->first();
        if ($badge) {
            $alreadyHas = $user->badges()->where('badge_id', $badge->id)->exists();
            if (! $alreadyHas) {
                $user->badges()->attach($badge->id, [
                    'earned_at'  => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } else {
            \Illuminate\Support\Facades\Log::warning("GamificationService: 'verified_donor' badge not found in DB.");
        }

        // ─── QR Smart Card Token ─────────────────────────────────────────
        // NID verify হওয়ার সাথে সাথে একটি cryptographically secure token তৈরি
        // করা হয়। একবার তৈরি হলে আর বদলানো হবে না (idempotent)।
        $this->generateQrTokenIfMissing($user);

        $user->update(['verified_badge' => true, 'nid_status' => 'verified']);
    }

    /**
     * NID rejected হলে 'verified_donor' ব্যাজ সরিয়ে নাও।
     * Note: qr_token ইচ্ছাকৃতভাবে রিভোকে NULL করা হয় না —
     *       পুনরায় verify হলে একই token পুনরায় সক্রিয় হবে।
     */
    public function revokeVerifiedBadge(User $user): void
    {
        $badge = \App\Models\Badge::where('name', 'verified_donor')->first();
        if ($badge) {
            $user->badges()->detach($badge->id);
        }
        $user->update(['verified_badge' => false, 'nid_status' => 'unverified']);
    }

    // =========================================================
    // Dynamic QR Smart Card Token
    // =========================================================

    /**
     * ক্রিপ্টোগ্রাফিক্যালি সিকিউর QR token তৈরি করে users.qr_token-এ সেভ করে।
     *
     * বৈশিষ্ট্য:
     *  • random_bytes(32) → 256-bit entropy → bin2hex → 64-char hex string
     *  • DB-তে unique constraint আছে, তাই collision loop দিয়ে নিশ্চিত করা হয়
     *  • Idempotent: token আগে থেকে থাকলে নতুন token তৈরি করে না
     *  • PII-free: নাম/ফোন/ইমেইল/NID নম্বর কিছুই token-এ নেই
     */
    public function generateQrTokenIfMissing(User $user): void
    {
        // ইতিমধ্যে token আছে — আর কিছু করতে হবে না
        if (! empty($user->qr_token)) {
            return;
        }

        // Collision-safe loop: DB-তে unique constraint আছে বলে
        // যতক্ষণ না unique token পাওয়া যায় retry করো।
        do {
            $token = bin2hex(random_bytes(32)); // 64-char hex, 256-bit entropy
        } while (User::where('qr_token', $token)->exists());

        $user->update(['qr_token' => $token]);

        Log::info('[QR SmartCard] Token generated for user.', [
            'user_id' => $user->id,
            'token'   => substr($token, 0, 8) . '...', // লগে শুধু prefix দেখাও
        ]);
    }
}
