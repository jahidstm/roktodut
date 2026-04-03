<?php

namespace Database\Seeders;

use App\Enums\BloodGroup;
use App\Enums\UserRole;
use App\Models\User;
use App\Models\Division;
use App\Models\District;
use App\Models\Upazila;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDonorsSeeder extends Seeder
{
    public function run(): void
    {
        $upazilas = Upazila::with('district')->get();

        if ($upazilas->isEmpty()) {
            throw new \RuntimeException('No location data found in database. Run LocationSeeder first.');
        }

        // --- নির্দিষ্ট ডেমো ইউজারদের জন্য লোকেশন আইডি বের করা ---
        $dhakaDiv = Division::where('name', 'ঢাকা')->first();
        $dhakaDist = District::where('name', 'ঢাকা')->where('division_id', optional($dhakaDiv)->id)->first();
        $dhanmondiUpz = Upazila::where('name', 'ধানমন্ডি')->where('district_id', optional($dhakaDist)->id)->first();

        $divId = $dhakaDiv->id ?? 1;
        $distId = $dhakaDist->id ?? 1;
        $upzId = $dhanmondiUpz->id ?? 1;

        // 🏢 ১. প্রথমে অর্গানাইজেশন এবং তার অ্যাডমিন তৈরি করা (Chicken-and-Egg Fix)
        $orgAdmin = User::updateOrCreate(
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
                'email_verified_at' => now(),
            ]
        );

        $org = Organization::updateOrCreate(
            ['name' => 'Roktodut Central Club'],
            [
                'type' => 'blood_bank',
                'district' => 'Dhaka',
                'admin_id' => $orgAdmin->id,
            ]
        );

        // অ্যাডমিনকে তার অর্গানাইজেশনের আইডি দেওয়া
        $orgAdmin->update(['organization_id' => $org->id]);

        // 👥 ২. ৩০ জন ডেমো ডোনার তৈরি করা (ডাইনামিক স্ট্যাটাস সহ)
        $bloodGroupValues = array_map(fn($c) => $c->value, BloodGroup::cases());

        for ($i = 1; $i <= 30; $i++) {
            $upz = $upazilas[($i * 7) % count($upazilas)];
            $bg = $bloodGroupValues[($i - 1) % count($bloodGroupValues)];

            // 🎯 ম্যাজিক লজিক: প্রতি ৩ জনে ১ জন অর্গানাইজেশনের মেম্বার হবে (অ্যাপ্রুভড বা পেন্ডিং)
            $isOrgMember = $i % 3 === 0;
            $nidStatus = $isOrgMember ? ($i % 2 === 0 ? 'approved' : 'pending') : 'none';

            User::updateOrCreate(
                ['email' => "donor{$i}@demo.test"],
                [
                    'name' => "Demo Donor {$i}",
                    'password' => Hash::make('password'),
                    'phone' => '0171' . str_pad((string) $i, 7, '0', STR_PAD_LEFT),
                    'role' => UserRole::DONOR->value,
                    'blood_group' => $bg,
                    'division_id' => $upz->district->division_id,
                    'district_id' => $upz->district_id,
                    'upazila_id'  => $upz->id,
                    
                    'organization_id' => $isOrgMember ? $org->id : null, // রিলেশন তৈরি
                    'nid_status' => $nidStatus, // ভেরিফিকেশন স্ট্যাটাস
                    
                    'is_onboarded' => true,
                    'is_available' => true,
                    'is_ready_now' => $i % 6 === 0,
                    'verified_badge' => $nidStatus === 'approved', // ব্লু ব্যাজ
                    'total_donations' => (int) ($i % 12),
                    'last_login_at' => now()->subDays($i % 20),
                    'remember_token' => Str::random(10),
                ]
            );
        }

        // 🎯 ৩. টেস্টিংয়ের জন্য স্পেসিফিক ইউজার (আলিফ ও তাবাসসুমের ডিরেক্ট টেস্টের জন্য)
        
        // Verified Donor (তাবাসসুমের ব্যাজ টেস্টের জন্য)
        User::updateOrCreate(
            ['email' => 'donor_verified@demo.test'],
            [
                'name' => 'Verified Donor Kamal',
                'password' => Hash::make('password'),
                'phone' => '01730000003',
                'role' => UserRole::DONOR->value,
                'blood_group' => BloodGroup::O_POS->value,
                'division_id' => $divId,
                'district_id' => $distId,
                'upazila_id'  => $upzId,
                'organization_id' => $org->id,
                'nid_status' => 'approved',
                'is_onboarded' => true,
            ]
        );

        // Pending Donor (আলিফের বাটন টেস্টের জন্য)
        User::updateOrCreate(
            ['email' => 'pending@demo.test'],
            [
                'name' => 'Pending Test Donor',
                'password' => Hash::make('password'),
                'phone' => '01899999999',
                'role' => UserRole::DONOR->value,
                'blood_group' => BloodGroup::AB_POS->value,
                'division_id' => $divId,
                'district_id' => $distId,
                'upazila_id'  => $upzId,
                'organization_id' => $org->id,
                'nid_status' => 'pending',
                'nid_image' => 'dummy_nid.jpg',
                'is_onboarded' => true,
            ]
        );

        // সাধারণ Recipient
        User::updateOrCreate(
            ['email' => 'recipient@demo.test'],
            [
                'name' => 'Demo Recipient',
                'password' => Hash::make('password'),
                'phone' => '01720000000',
                'role' => UserRole::RECIPIENT->value,
                'blood_group' => BloodGroup::O_POS->value,
                'division_id' => $divId,
                'district_id' => $distId,
                'upazila_id'  => $upzId,
                'is_onboarded' => true,
            ]
        );
    }
}