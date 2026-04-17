<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 🚀 সিকোয়েন্স ফিক্সড — নির্ভরতা অনুযায়ী ক্রম:
        // ১. লোকেশন (divisions, districts, upazilas) — সব ইউজারের আগে চাই
        // ২. ব্যাজ — DemoDonorsSeeder ব্যাজ অ্যাসাইন করে, তাই আগে চাই
        // ৩. Admin ইউজার — role=admin নিশ্চিত করা
        // ৪. ডেমো ডোনার/অর্গ — BlogSeeder-এর author pool
        // ৫. ব্লগ — ইউজার থেকে author নেয়
        // ৬. Contact বার্তা — ইউজার FK দরকার, তাই সবার শেষে
        $this->call([
            LocationSeeder::class,          // 📍 বিভাগ, জেলা, উপজেলা
            BadgeSeeder::class,             // 🏅 ব্যাজ ডেফিনিশন
            DemoAdminSeeder::class,         // 🔑 admin@roktodut.test (role=admin)
            DemoDonorsSeeder::class,        // 👤 ৩০+ ডোনার + অর্গ অ্যাডমিন
            BlogSeeder::class,              // 📝 Health Blogs + Success Stories
            ContactMessagesSeeder::class,   // 📬 ডেমো contact_messages (৪ স্ট্যাটাস)
        ]);
    }
}