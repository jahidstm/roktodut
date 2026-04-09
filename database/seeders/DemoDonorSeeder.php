<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDonorSeeder extends Seeder
{
    /**
     * বিভিন্ন পয়েন্ট ও ব্যাজ সহ ২০ জন ডেমো ডোনার তৈরি করা
     * চালানোর জন্য: php artisan db:seed --class=DemoDonorSeeder
     */
    public function run(): void
    {
        $service = app(GamificationService::class);

        // ────────────────────────────────────────────────
        // বাংলাদেশী ডোনারদের ডেটা [নাম, রক্তের গ্রুপ, জেলা_id, ডিভিশন_id, ডোনেশন সংখ্যা]
        // ────────────────────────────────────────────────
        $donors = [
            // 🏆 Platinum Hero tier (২০+ ডোনেশন)
            ['name' => 'মাহমুদুল হাসান',   'blood' => 'O+',  'dist' => 1,  'div' => 1, 'don' => 25, 'ref' => null],
            ['name' => 'তানভীর আহমেদ',     'blood' => 'B+',  'dist' => 2,  'div' => 1, 'don' => 22, 'ref' => null],
            ['name' => 'সাবরিনা ইসলাম',    'blood' => 'A+',  'dist' => 3,  'div' => 2, 'don' => 21, 'ref' => null],

            // 🥇 Golden Guardian tier (১০+ ডোনেশন)
            ['name' => 'রাফি উদ্দিন',       'blood' => 'AB+', 'dist' => 4,  'div' => 2, 'don' => 15, 'ref' => null],
            ['name' => 'নাফিসা খানম',      'blood' => 'O-',  'dist' => 5,  'div' => 3, 'don' => 13, 'ref' => null],
            ['name' => 'শাহরিয়ার কবির',    'blood' => 'A-',  'dist' => 6,  'div' => 3, 'don' => 12, 'ref' => null],
            ['name' => 'ফারহানা বেগম',     'blood' => 'B-',  'dist' => 7,  'div' => 4, 'don' => 10, 'ref' => null],

            // 🥈 Silver Savior tier (৫+ ডোনেশন)
            ['name' => 'মেহেদী হাসান',     'blood' => 'B+',  'dist' => 8,  'div' => 4, 'don' => 8,  'ref' => null],
            ['name' => 'সুমাইয়া আক্তার',  'blood' => 'O+',  'dist' => 9,  'div' => 5, 'don' => 7,  'ref' => null],
            ['name' => 'রিফাত রহমান',      'blood' => 'A+',  'dist' => 10, 'div' => 5, 'don' => 6,  'ref' => null],
            ['name' => 'জান্নাত আরা',      'blood' => 'AB-', 'dist' => 11, 'div' => 6, 'don' => 5,  'ref' => null],
            ['name' => 'সজীব কুমার',       'blood' => 'O+',  'dist' => 12, 'div' => 6, 'don' => 5,  'ref' => null],

            // 🥉 Bronze Bloodline tier (১-৪ ডোনেশন)
            ['name' => 'নিলুফার ইয়াসমিন', 'blood' => 'A+',  'dist' => 13, 'div' => 7, 'don' => 4,  'ref' => null],
            ['name' => 'তৌহিদ ইসলাম',     'blood' => 'B+',  'dist' => 14, 'div' => 7, 'don' => 3,  'ref' => null],
            ['name' => 'সামিয়া হোসেন',   'blood' => 'O-',  'dist' => 15, 'div' => 8, 'don' => 2,  'ref' => null],
            ['name' => 'আরিফুল ইসলাম',   'blood' => 'AB+', 'dist' => 16, 'div' => 8, 'don' => 2,  'ref' => null],
            ['name' => 'মেহজাবিন চৌধুরী', 'blood' => 'A-',  'dist' => 17, 'div' => 1, 'don' => 1,  'ref' => null],
            ['name' => 'নাজমুল হক',       'blood' => 'B-',  'dist' => 18, 'div' => 1, 'don' => 1,  'ref' => null],
            ['name' => 'সাইমা তাবাস্সুম', 'blood' => 'O+',  'dist' => 19, 'div' => 2, 'don' => 1,  'ref' => null],
            ['name' => 'হাসান মাহমুদ',    'blood' => 'A+',  'dist' => 20, 'div' => 2, 'don' => 1,  'ref' => null],
        ];

        $this->command->info('🚀 Demo Donor তৈরি হচ্ছে...');
        $bar = $this->command->getOutput()->createProgressBar(count($donors));
        $bar->start();

        foreach ($donors as $data) {
            $slug = \Illuminate\Support\Str::slug($data['name'], '_');
            $email = "demo_{$slug}@roktodut.test";

            // ইতিমধ্যে থাকলে স্কিপ
            if (User::where('email', $email)->exists()) {
                $bar->advance();
                continue;
            }

            $user = User::create([
                'name'                     => $data['name'],
                'email'                    => $email,
                'password'                 => Hash::make('demo12345'),
                'role'                     => 'donor',
                'blood_group'              => $data['blood'],
                'division_id'              => $data['div'],
                'district_id'              => $data['dist'],
                'is_available'             => true,
                'is_onboarded'             => true,
                'total_verified_donations' => 0,
                'points'                   => 0,
            ]);

            // প্রতিটি ডোনেশন সিমুলেট করা — পয়েন্ট ও ব্যাজ অটো আপডেট হবে
            for ($i = 0; $i < $data['don']; $i++) {
                // প্রতি ৩য় ডোনেশনে First Responder বোনাস
                $service->processDonationReward(
                    donor:            $user,
                    bloodRequest:     new \App\Models\BloodRequest(),
                    isFirstResponder: ($i % 3 === 0),
                );
                $user->refresh();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('✅ ' . count($donors) . ' জন Demo Donor তৈরি সম্পন্ন!');
        $this->command->table(
            ['নাম', 'রক্তের গ্রুপ', 'পয়েন্ট', 'ডোনেশন', 'ব্যাজ'],
            User::where('email', 'like', 'demo_%@roktodut.test')
                ->with('badges')
                ->orderByDesc('points')
                ->get()
                ->map(fn($u) => [
                    $u->name,
                    $u->blood_group?->value ?? $u->blood_group,
                    $u->points,
                    $u->total_verified_donations,
                    $u->badges->pluck('name')->implode(', ') ?: '—',
                ])
                ->toArray()
        );
    }
}
