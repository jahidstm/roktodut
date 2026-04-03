<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ১. আগে লোকেশন এবং ডেমো ডোনারদের সিড করা
        $this->call([
            LocationSeeder::class,
            DemoDonorsSeeder::class,
        ]);

        // ২. ডাটাবেস থেকে প্রথম ইউজারকে খুঁজে বের করা (অ্যাডমিন বানানোর জন্য)
        $admin = User::first();

        if ($admin) {
            // ৩. একটি ডিফল্ট অর্গানাইজেশন তৈরি করা
            $org = new Organization();
            $org->name = 'Roktodut Central Bank';
            $org->type = 'blood_bank';
            $org->district = 'Dhaka';
            $org->admin_id = $admin->id;
            $org->save();

            // ৪. প্রথম ইউজারকে অ্যাডমিন হিসেবে সেট করা
            $admin->organization_id = $org->id;
            $admin->role = 'org_admin';
            $admin->save();

            // 🚀 ৫. টেস্টিংয়ের জন্য একটি "পেন্ডিং ডোনার" তৈরি করা (যাতে ড্যাশবোর্ড ফাঁকা না থাকে)
            $pendingDonor = new User();
            $pendingDonor->name = 'Pending Test Donor';
            $pendingDonor->email = 'pending@demo.com';
            $pendingDonor->password = bcrypt('password');
            $pendingDonor->role = 'donor';
            $pendingDonor->organization_id = $org->id;
            $pendingDonor->nid_status = 'pending';
            $pendingDonor->nid_image = 'dummy_nid_image.jpg';
            $pendingDonor->is_onboarded = true;
            $pendingDonor->blood_group = 'AB+';
            $pendingDonor->phone = '01899999999';
            $pendingDonor->save();
        }
    }
}
