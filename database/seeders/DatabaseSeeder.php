<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 🚀 সিকোয়েন্স ফিক্সড: আগে লোকেশন, এরপর ডোনার ও অর্গানাইজেশন
        $this->call([
            LocationSeeder::class,
            BadgeSeeder::class,       // 🏅 ব্যাজ আগে সিড হতে হবে
            DemoDonorsSeeder::class,  // 👤 ডোনার/ইউজার (BlogSeeder-এর author pool)
            BlogSeeder::class,        // 📝 Health Blogs + Success Stories
        ]);
    }
}