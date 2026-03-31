<?php

namespace Database\Seeders;

use App\Enums\BloodGroup;
use App\Enums\UserRole;
use App\Models\User;
use App\Models\Division;
use App\Models\District;
use App\Models\Upazila;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDonorsSeeder extends Seeder
{
    public function run(): void
    {
        // 🔍 ১. ডাটাবেস থেকে সরাসরি লোকেশন ডেটা নিয়ে আসা (JSON পার্স করার ঝামেলা শেষ)
        $upazilas = Upazila::with('district')->get();

        if ($upazilas->isEmpty()) {
            throw new \RuntimeException('No location data found in database. Run LocationSeeder first.');
        }

        $bloodGroupValues = array_map(fn($c) => $c->value, BloodGroup::cases());

        // --- 30 demo donors (unique phone range: 01710000001..01710000030) ---
        for ($i = 1; $i <= 30; $i++) {
            // ডাইনামিক লোকেশন অবজেক্ট সিলেক্ট করা
            $upz = $upazilas[($i * 7) % count($upazilas)];
            $bg = $bloodGroupValues[($i - 1) % count($bloodGroupValues)];

            User::updateOrCreate(
                ['email' => "donor{$i}@demo.test"],
                [
                    'name' => "Demo Donor {$i}",
                    'password' => Hash::make('password'),
                    'phone' => '0171' . str_pad((string) $i, 7, '0', STR_PAD_LEFT), // ✅ unique
                    'role' => UserRole::DONOR->value,
                    'blood_group' => $bg,

                    // 📍 নতুন রিলেশনাল লোকেশন আইডি
                    'division_id' => $upz->district->division_id,
                    'district_id' => $upz->district_id,
                    'upazila_id'  => $upz->id,

                    'is_onboarded' => true,
                    'is_available' => true,
                    'is_ready_now' => $i % 6 === 0,
                    'verified_badge' => $i % 4 === 0,
                    'nid_status' => $i % 5 === 0 ? 'approved' : 'none',
                    'total_donations' => (int) ($i % 12),
                    'cooldown_until' => null,
                    'last_login_at' => now()->subDays($i % 20),

                    'email_verified_at' => null,
                    'remember_token' => Str::random(10),
                ]
            );
        }

        // --- নির্দিষ্ট ডেমো ইউজারদের জন্য 'ঢাকা -> ঢাকা -> ধানমন্ডি' আইডি খুঁজে বের করা ---
        $dhakaDiv = Division::where('name', 'ঢাকা')->first();
        $dhakaDist = District::where('name', 'ঢাকা')->where('division_id', optional($dhakaDiv)->id)->first();
        $dhanmondiUpz = Upazila::where('name', 'ধানমন্ডি')->where('district_id', optional($dhakaDist)->id)->first();

        // যদি কোনো কারণে ধানমন্ডি না থাকে, তবে সেফ ফলব্যাক হিসেবে ডিফল্ট আইডি (১) ব্যবহার হবে
        $divId = $dhakaDiv->id ?? 1;
        $distId = $dhakaDist->id ?? 1;
        $upzId = $dhanmondiUpz->id ?? 1;

        // --- Base recipient (unique phone) ---
        User::updateOrCreate(
            ['email' => 'recipient@demo.test'],
            [
                'name' => 'Demo Recipient',
                'password' => Hash::make('password'),
                'phone' => '01720000000',
                'role' => UserRole::RECIPIENT->value,
                'blood_group' => BloodGroup::O_POS->value,

                // 📍 নতুন আইডি
                'division_id' => $divId,
                'district_id' => $distId,
                'upazila_id'  => $upzId,

                'is_onboarded' => true,
                'is_available' => false,
                'last_login_at' => now(),
                'email_verified_at' => null,
            ]
        );

        // --- VERIFIED ROLE USERS FOR ASSESSMENT DEMO ---
        $verifiedAt = now()->subDay();

        User::updateOrCreate(
            ['email' => 'admin@demo.test'],
            [
                'name' => 'Demo Admin',
                'password' => Hash::make('password'),
                'phone' => '01730000001',
                'role' => UserRole::ADMIN->value,
                'blood_group' => BloodGroup::O_POS->value,

                'division_id' => $divId,
                'district_id' => $distId,
                'upazila_id'  => $upzId,

                'is_onboarded' => true,
                'email_verified_at' => $verifiedAt,
                'last_login_at' => now(),
                'is_available' => false,
                'is_ready_now' => false,
            ]
        );

        User::updateOrCreate(
            ['email' => 'orgadmin@demo.test'],
            [
                'name' => 'Demo Org Admin',
                'password' => Hash::make('password'),
                'phone' => '01730000002',
                'role' => UserRole::ORG_ADMIN->value,
                'blood_group' => BloodGroup::A_POS->value,

                'division_id' => $divId,
                'district_id' => $distId,
                'upazila_id'  => $upzId,

                'is_onboarded' => true,
                'email_verified_at' => $verifiedAt,
                'last_login_at' => now(),
                'is_available' => false,
                'is_ready_now' => false,
            ]
        );

        User::updateOrCreate(
            ['email' => 'donor_verified@demo.test'],
            [
                'name' => 'Verified Donor',
                'password' => Hash::make('password'),
                'phone' => '01730000003',
                'role' => UserRole::DONOR->value,
                'blood_group' => BloodGroup::O_POS->value,

                'division_id' => $divId,
                'district_id' => $distId,
                'upazila_id'  => $upzId,

                'is_onboarded' => true,
                'is_available' => true,
                'is_ready_now' => true,
                'verified_badge' => true,
                'nid_status' => 'approved',
                'total_donations' => 10,
                'cooldown_until' => null,

                'email_verified_at' => $verifiedAt,
                'last_login_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'recipient_verified@demo.test'],
            [
                'name' => 'Verified Recipient',
                'password' => Hash::make('password'),
                'phone' => '01730000004',
                'role' => UserRole::RECIPIENT->value,
                'blood_group' => BloodGroup::A_POS->value,

                'division_id' => $divId,
                'district_id' => $distId,
                'upazila_id'  => $upzId,

                'is_onboarded' => true,
                'is_available' => false,
                'email_verified_at' => $verifiedAt,
                'last_login_at' => now(),
            ]
        );
    }
}