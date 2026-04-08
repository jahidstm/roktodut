<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        \Schema::disableForeignKeyConstraints();
        DB::table('badges')->truncate();
        \Schema::enableForeignKeyConstraints();

        $badges = [
            // ─── মাইলস্টোন ব্যাজ ───────────────────────────────────────
            [
                'name'               => 'bronze_bloodline',
                'bn_name'            => 'ব্রোঞ্জ ব্লাডলাইন',
                'icon'               => '🥉',
                'emoji'              => '🥉',
                'description'        => 'প্রথম রক্তদান বা ৫০ পয়েন্ট অর্জনে এই ব্যাজ পাওয়া যায়।',
                'type'               => 'milestone',
                'requirement'        => 1,    // ডোনেশন কাউন্ট
                'points_requirement' => 50,   // বা এতো পয়েন্ট
                'color'              => '#b45309',
            ],
            [
                'name'               => 'silver_savior',
                'bn_name'            => 'সিলভার সেভিয়ার',
                'icon'               => '🥈',
                'emoji'              => '🥈',
                'description'        => '৫ বার রক্তদান বা ৩০০ পয়েন্ট অর্জনে এই ব্যাজ পাওয়া যায়।',
                'type'               => 'milestone',
                'requirement'        => 5,
                'points_requirement' => 300,
                'color'              => '#475569',
            ],
            [
                'name'               => 'golden_guardian',
                'bn_name'            => 'গোল্ডেন গার্ডিয়ান',
                'icon'               => '🏅',
                'emoji'              => '🏅',
                'description'        => '১০ বার রক্তদান বা ৬০০ পয়েন্ট অর্জনে এই ব্যাজ পাওয়া যায়।',
                'type'               => 'milestone',
                'requirement'        => 10,
                'points_requirement' => 600,
                'color'              => '#d97706',
            ],
            [
                'name'               => 'platinum_hero',
                'bn_name'            => 'প্লাটিনাম হিরো',
                'icon'               => '🏆',
                'emoji'              => '🏆',
                'description'        => '২০+ বার রক্তদান বা ১৫০০+ পয়েন্ট অর্জনে এই সর্বোচ্চ সম্মানের ব্যাজ পাওয়া যায়।',
                'type'               => 'milestone',
                'requirement'        => 20,
                'points_requirement' => 1500,
                'color'              => '#7c3aed',
            ],

            // ─── স্পেশাল আইডেন্টিটি ব্যাজ ────────────────────────────
            [
                'name'               => 'campus_hero',
                'bn_name'            => 'ক্যাম্পাস হিরো',
                'icon'               => '🎓',
                'emoji'              => '🎓',
                'description'        => '.edu বা .ac.bd ইমেইল দিয়ে নিবন্ধিত বিশ্ববিদ্যালয়ের শিক্ষার্থীরা এই ব্যাজ পান।',
                'type'               => 'special',
                'requirement'        => 0,
                'points_requirement' => 0,
                'color'              => '#1d4ed8',
            ],
            [
                'name'               => 'verified_donor',
                'bn_name'            => 'ভেরিফাইড ডোনার',
                'icon'               => '🛡️',
                'emoji'              => '🛡️',
                'description'        => 'NID বা ন্যাশনাল আইডি দ্বারা পরিচয় ভেরিফাইড ডোনাররা এই ব্যাজ পান।',
                'type'               => 'special',
                'requirement'        => 0,
                'points_requirement' => 0,
                'color'              => '#059669',
            ],
            [
                'name'               => 'ready_now',
                'bn_name'            => 'রেডি নাউ',
                'icon'               => '⚡',
                'emoji'              => '⚡',
                'description'        => 'ইমার্জেন্সি মোড চালু রেখে যেকোনো সময় রক্ত দিতে প্রস্তুত থাকলে এই ব্যাজ পাওয়া যায়।',
                'type'               => 'special',
                'requirement'        => 0,
                'points_requirement' => 0,
                'color'              => '#ea580c',
            ],
            [
                'name'               => 'rare_blood_hero',
                'bn_name'            => 'রেয়ার ব্লাড হিরো',
                'icon'               => '💎',
                'emoji'              => '💎',
                'description'        => 'নেগেটিভ রক্তের গ্রুপ (O-, A-, B-, AB-) ওয়ালা যারা কমপক্ষে একবার রক্ত দিয়েছেন।',
                'type'               => 'special',
                'requirement'        => 1,
                'points_requirement' => 0,
                'color'              => '#be185d',
            ],
            [
                'name'               => 'midnight_savior',
                'bn_name'            => 'মিডনাইট সেভিয়ার',
                'icon'               => '🌙',
                'emoji'              => '🌙',
                'description'        => 'রাত ১২টা থেকে ভোর ৬টার মধ্যে ইমার্জেন্সিতে রক্তদান করলে এই বিশেষ ব্যাজ পাওয়া যায়।',
                'type'               => 'special',
                'requirement'        => 0,
                'points_requirement' => 0,
                'color'              => '#4338ca',
            ],
        ];

        foreach ($badges as $badge) {
            DB::table('badges')->insert([
                ...$badge,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
