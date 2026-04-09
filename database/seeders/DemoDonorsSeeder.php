<?php

namespace Database\Seeders;

use App\Enums\BloodGroup;
use App\Enums\UserRole;
use App\Models\Badge;
use App\Models\Organization;
use App\Models\District;
use App\Models\Division;
use App\Models\Upazila;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDonorsSeeder extends Seeder
{
    public function run(): void
    {
        $upazilas = Upazila::with('district.division')->get();

        if ($upazilas->isEmpty()) {
            throw new \RuntimeException('No location data found. Run LocationSeeder first.');
        }

        // ─── লোকেশন রেফারেন্স ────────────────────────────────────────
        $dhakaDiv   = Division::where('name', 'ঢাকা')->first();
        $dhakaDist  = District::where('name', 'ঢাকা')
                        ->where('division_id', optional($dhakaDiv)->id)->first();
        $dhanmondi  = Upazila::where('name', 'ধানমন্ডি')
                        ->where('district_id', optional($dhakaDist)->id)->first();

        $divId  = $dhakaDiv->id  ?? 1;
        $distId = $dhakaDist->id ?? 1;
        $upzId  = $dhanmondi->id ?? 1;

        // ─── ১. অর্গানাইজেশন অ্যাডমিন তৈরি ─────────────────────────
        $orgAdmin = User::updateOrCreate(
            ['email' => 'orgadmin@demo.test'],
            [
                'name'               => 'Demo Org Admin',
                'password'           => Hash::make('password'),
                'phone'              => '01730000002',
                'role'               => UserRole::ORG_ADMIN->value,
                'blood_group'        => BloodGroup::A_POS->value,
                'division_id'        => $divId,
                'district_id'        => $distId,
                'upazila_id'         => $upzId,
                'is_onboarded'       => true,
                'email_verified_at'  => now(),
            ]
        );

        $org = Organization::updateOrCreate(
            ['name' => 'Roktodut Central Club'],
            [
                'type'       => 'blood_bank',
                'district'   => 'Dhaka',
                'admin_id'   => $orgAdmin->id,
            ]
        );
        $orgAdmin->update(['organization_id' => $org->id]);

        // ─── ২. ব্যাজ লোড ────────────────────────────────────────────
        $badges = Badge::all()->keyBy('name');
        $currentMonth = now()->format('Y-m');

        // ─── ৩. গ্যামিফিকেশন প্রোফাইল সহ ৩০ জন ডোনার তৈরি ─────────
        $bloodGroupValues = array_map(fn($c) => $c->value, BloodGroup::cases());

        for ($i = 1; $i <= 30; $i++) {
            $upz = $upazilas[($i * 7) % count($upazilas)];
            $bg  = $bloodGroupValues[($i - 1) % count($bloodGroupValues)];

            // গ্যামিফিকেশন ডেটা: স্তরভেদে ভিন্ন মান
            $donations = match (true) {
                $i <= 3  => 20 + $i,      // Platinum Hero
                $i <= 8  => 10 + $i,      // Golden Guardian
                $i <= 15 => 5  + ($i % 4), // Silver Savior
                $i <= 22 => 1  + ($i % 3), // Bronze Bloodline
                default  => 0,             // নতুন ডোনার
            };

            $basePoints    = $donations * 50;
            $bonusPoints   = ($i % 3 === 0)  ? 10 : 0;  // First Responder
            $profileBonus  = ($i % 4 === 0)  ? 20 : 0;  // Profile Complete
            $totalPoints   = $basePoints + $bonusPoints + $profileBonus;

            $monthlyPoints = ($i <= 10) ? ($donations * 50) : 0;

            // nid_status — 'verified'/'pending'/'unverified'
            $isOrgMember = ($i % 3 === 0);
            $nidStatus   = $isOrgMember ? ($i % 6 === 0 ? 'verified' : 'pending') : 'unverified';

            $lastDonatedAt = ($donations > 0)
                ? now()->subDays(130 + ($i * 2))->toDateString() // কুলডাউন পেরিয়ে গেছে
                : null;

            $donor = User::updateOrCreate(
                ['email' => "donor{$i}@demo.test"],
                [
                    'name'                    => "ডেমো ডোনার {$i}",
                    'password'                => Hash::make('password'),
                    'phone'                   => '0171' . str_pad((string) $i, 7, '0', STR_PAD_LEFT),
                    'role'                    => UserRole::DONOR->value,
                    'blood_group'             => $bg,
                    'division_id'             => $upz->district->division_id ?? $divId,
                    'district_id'             => $upz->district_id,
                    'upazila_id'              => $upz->id,
                    'organization_id'         => $isOrgMember ? $org->id : null,
                    'nid_status'              => $nidStatus,
                    'nid_path'                => $nidStatus !== 'unverified' ? 'seed/dummy_nid.jpg' : null,
                    'is_onboarded'            => true,
                    'is_available'            => ($i % 5 !== 0), // ৮০% ইমার্জেন্সি মোড চালু
                    'is_ready_now'            => ($i % 6 === 0),
                    'verified_badge'          => ($nidStatus === 'verified'),
                    'total_verified_donations' => $donations,
                    'total_donations'         => $donations,
                    'points'                  => $totalPoints,
                    'monthly_points'          => $monthlyPoints,
                    'monthly_points_month'    => $monthlyPoints > 0 ? $currentMonth : null,
                    'last_donated_at'         => $lastDonatedAt,
                    'last_login_at'           => now()->subDays($i % 20),
                    'is_shadowbanned'         => false,
                    'remember_token'          => Str::random(10),
                    'email_verified_at'       => now(),
                    'gender'                  => ($i % 2 === 0) ? 'male' : 'female',
                    'date_of_birth'           => now()->subYears(20 + ($i % 20))->toDateString(),
                    'weight'                  => 50 + ($i % 30),
                ]
            );

            // ─── ব্যাজ অ্যাসাইন ─────────────────────────────────────
            $donor->badges()->detach(); // রিসেট করুন

            if ($donations >= 1  && $badges->has('bronze_bloodline'))  $this->attachBadge($donor, $badges['bronze_bloodline']);
            if ($donations >= 5  && $badges->has('silver_savior'))     $this->attachBadge($donor, $badges['silver_savior']);
            if ($donations >= 10 && $badges->has('golden_guardian'))   $this->attachBadge($donor, $badges['golden_guardian']);
            if ($donations >= 20 && $badges->has('platinum_hero'))     $this->attachBadge($donor, $badges['platinum_hero']);
            if ($nidStatus === 'verified' && $badges->has('verified_donor')) $this->attachBadge($donor, $badges['verified_donor']);
            if ($donor->is_available && $badges->has('ready_now'))     $this->attachBadge($donor, $badges['ready_now']);

            // নেগেটিভ ব্লাড গ্রুপে রেয়ার ব্লাড হিরো
            if (str_contains($bg, '-') && $donations >= 1 && $badges->has('rare_blood_hero')) {
                $this->attachBadge($donor, $badges['rare_blood_hero']);
            }
        }

        // ─── ৪. স্পেসিফিক টেস্ট ইউজার ───────────────────────────────

        // Platinum Hero ডোনার (সর্বোচ্চ স্তর পরীক্ষার জন্য)
        $platinumDonor = User::updateOrCreate(
            ['email' => 'platinum@demo.test'],
            [
                'name'                    => 'Kamal Platinum Hero',
                'password'                => Hash::make('password'),
                'phone'                   => '01730009999',
                'role'                    => UserRole::DONOR->value,
                'blood_group'             => BloodGroup::O_NEG->value, // রেয়ার গ্রুপ
                'division_id'             => $divId,
                'district_id'             => $distId,
                'upazila_id'              => $upzId,
                'organization_id'         => $org->id,
                'nid_status'              => 'verified',
                'nid_path'                => 'seed/dummy_nid.jpg',
                'is_onboarded'            => true,
                'is_available'            => true,
                'verified_badge'          => true,
                'total_verified_donations' => 25,
                'total_donations'         => 25,
                'points'                  => 1620,
                'monthly_points'          => 110,
                'monthly_points_month'    => $currentMonth,
                'last_donated_at'         => now()->subDays(135)->toDateString(),
                'is_shadowbanned'         => false,
                'email_verified_at'       => now(),
                'gender'                  => 'male',
                'date_of_birth'           => now()->subYears(28)->toDateString(),
                'weight'                  => 72,
            ]
        );
        $platinumDonor->badges()->detach();
        foreach (['bronze_bloodline','silver_savior','golden_guardian','platinum_hero','verified_donor','rare_blood_hero','ready_now'] as $slug) {
            if ($badges->has($slug)) $this->attachBadge($platinumDonor, $badges[$slug]);
        }

        // সাধারণ Recipient
        User::updateOrCreate(
            ['email' => 'recipient@demo.test'],
            [
                'name'              => 'Demo Recipient',
                'password'          => Hash::make('password'),
                'phone'             => '01720000000',
                'role'              => UserRole::RECIPIENT->value,
                'blood_group'       => BloodGroup::O_POS->value,
                'division_id'       => $divId,
                'district_id'       => $distId,
                'upazila_id'        => $upzId,
                'is_onboarded'      => true,
                'is_shadowbanned'   => false,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ DemoDonorsSeeder: 30+ ডোনার গ্যামিফিকেশন ডেটা সহ সিড সম্পন্ন।');
    }

    private function attachBadge(User $user, Badge $badge): void
    {
        $alreadyHas = $user->badges()->where('badge_id', $badge->id)->exists();
        if (! $alreadyHas) {
            $user->badges()->attach($badge->id, [
                'earned_at'  => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}