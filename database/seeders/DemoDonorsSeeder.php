<?php

namespace Database\Seeders;

use App\Enums\BloodGroup;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDonorsSeeder extends Seeder
{
    public function run(): void
    {
        $path = public_path('data/bd-locations.json');
        $json = json_decode(File::get($path), true);

        $divisionsObj = $json['divisions'] ?? [];
        $pairs = [];

        foreach ($divisionsObj as $divisionName => $districtsObj) {
            if (!is_array($districtsObj)) {
                continue;
            }

            foreach ($districtsObj as $districtName => $upazilas) {
                if (!is_array($upazilas) || count($upazilas) === 0) {
                    $pairs[] = ['district' => $districtName, 'upazila' => null];
                    continue;
                }

                foreach ($upazilas as $u) {
                    $pairs[] = ['district' => $districtName, 'upazila' => $u];
                }
            }
        }

        if (count($pairs) === 0) {
            throw new \RuntimeException('No district/upazila pairs found in public/data/bd-locations.json');
        }

        $bloodGroupValues = array_map(fn($c) => $c->value, BloodGroup::cases());

        // --- 30 demo donors ---
        for ($i = 1; $i <= 30; $i++) {
            $pair = $pairs[($i * 7) % count($pairs)];
            $bg = $bloodGroupValues[($i - 1) % count($bloodGroupValues)];

            User::updateOrCreate(
                ['email' => "donor{$i}@demo.test"],
                [
                    'name' => "Demo Donor {$i}",
                    'password' => Hash::make('password'),
                    'phone' => '01' . str_pad((string) (100000000 + $i), 9, '0', STR_PAD_LEFT),
                    'role' => UserRole::DONOR->value,

                    'blood_group' => $bg,
                    'district' => $pair['district'],
                    'upazila' => $pair['upazila'],

                    'is_available' => true,
                    'is_ready_now' => $i % 6 === 0,
                    'verified_badge' => $i % 4 === 0,
                    'nid_status' => $i % 5 === 0 ? 'approved' : 'none',
                    'total_donations' => (int) ($i % 12),
                    'cooldown_until' => null,
                    'last_login_at' => now()->subDays($i % 20),

                    'email_verified_at' => null, // normal donors not auto-verified
                    'remember_token' => Str::random(10),
                ]
            );
        }

        // --- Base recipient (not necessarily verified) ---
        User::updateOrCreate(
            ['email' => 'recipient@demo.test'],
            [
                'name' => 'Demo Recipient',
                'password' => Hash::make('password'),
                'phone' => '01700000000',
                'role' => UserRole::RECIPIENT->value,

                // REQUIRED non-null
                'blood_group' => BloodGroup::O_POS->value,
                'district' => 'ঢাকা',
                'upazila' => 'ধানমন্ডি',

                'is_available' => false,
                'last_login_at' => now(),
                'email_verified_at' => null,
            ]
        );

        // --- VERIFIED ROLE USERS FOR ASSESSMENT DEMO ---
        $verifiedAt = now()->subDay();

        // Admin (must include required non-null fields: blood_group, district)
        User::updateOrCreate(
            ['email' => 'admin@demo.test'],
            [
                'name' => 'Demo Admin',
                'password' => Hash::make('password'),
                'phone' => '01700000001',
                'role' => UserRole::ADMIN->value,

                // REQUIRED non-null user fields
                'blood_group' => BloodGroup::O_POS->value,
                'district' => 'ঢাকা',
                'upazila' => 'ধানমন্ডি',

                'email_verified_at' => $verifiedAt,
                'last_login_at' => now(),
                'is_available' => false,
                'is_ready_now' => false,
            ]
        );

        // Organization Admin (must include required non-null fields)
        User::updateOrCreate(
            ['email' => 'orgadmin@demo.test'],
            [
                'name' => 'Demo Org Admin',
                'password' => Hash::make('password'),
                'phone' => '01700000002',
                'role' => UserRole::ORG_ADMIN->value,

                // REQUIRED non-null user fields
                'blood_group' => BloodGroup::A_POS->value,
                'district' => 'ঢাকা',
                'upazila' => 'ধানমন্ডি',

                'email_verified_at' => $verifiedAt,
                'last_login_at' => now(),
                'is_available' => false,
                'is_ready_now' => false,
            ]
        );

        // Verified Donor
        User::updateOrCreate(
            ['email' => 'donor_verified@demo.test'],
            [
                'name' => 'Verified Donor',
                'password' => Hash::make('password'),
                'phone' => '01700000003',
                'role' => UserRole::DONOR->value,

                'blood_group' => BloodGroup::O_POS->value,
                'district' => 'ঢাকা',
                'upazila' => 'ধানমন্ডি',

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

        // Verified Recipient
        User::updateOrCreate(
            ['email' => 'recipient_verified@demo.test'],
            [
                'name' => 'Verified Recipient',
                'password' => Hash::make('password'),
                'phone' => '01700000004',
                'role' => UserRole::RECIPIENT->value,

                'blood_group' => BloodGroup::A_POS->value,
                'district' => 'ঢাকা',
                'upazila' => 'ধানমন্ডি',

                'is_available' => false,
                'email_verified_at' => $verifiedAt,
                'last_login_at' => now(),
            ]
        );
    }
}