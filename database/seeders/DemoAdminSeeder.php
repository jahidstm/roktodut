<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * DemoAdminSeeder
 *
 * সিস্টেমের প্রধান অ্যাডমিন ইউজার নিশ্চিত করে।
 * - email: admin@roktodut.test
 * - password: password
 * - role: admin
 *
 * ভাইভা/ডেমোতে অ্যাডমিন প্যানেল দেখার জন্য ব্যবহার করুন।
 */
class DemoAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@roktodut.test'],
            [
                'name'              => 'RoktoDut Admin',
                'password'          => Hash::make('password'),
                'phone'             => '01700000001',
                'role'              => UserRole::ADMIN->value,
                'blood_group'       => 'B+',
                'is_onboarded'      => true,
                'is_shadowbanned'   => false,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ DemoAdminSeeder: admin@roktodut.test (password: password) — role=admin নিশ্চিত।');
    }
}
