<?php

namespace Database\Seeders;

use App\Enums\BloodGroup;
use App\Enums\UserRole;
use App\Models\Upazila;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $upazilas = Upazila::with('district.division')->get();

        if ($upazilas->isEmpty()) {
            throw new \RuntimeException('No location data found. Run LocationSeeder first.');
        }

        $bloodGroups = array_map(fn(BloodGroup $group) => $group->value, BloodGroup::cases());
        $targetCount = 10000;
        $chunkSize = 500;
        $created = 0;
        $pickTier = function (): string {
            $roll = fake()->numberBetween(1, 100);

            if ($roll <= 10) {
                return 'gold';
            }

            if ($roll <= 30) {
                return 'silver';
            }

            return 'standard';
        };

        $this->command?->info("🚀 Seeding {$targetCount} users (enterprise simulation)...");
        $bar = $this->command?->getOutput()->createProgressBar($targetCount);
        $bar?->start();

        while ($created < $targetCount) {
            $count = min($chunkSize, $targetCount - $created);

            $users = User::factory()
                ->count($count)
                ->state(function () use ($upazilas, $bloodGroups, $pickTier) {
                    $upz = $upazilas->random();
                    $priorityTier = $pickTier();
                    $tokens = $priorityTier === 'gold'
                        ? fake()->numberBetween(1, 2)
                        : ($priorityTier === 'silver' ? fake()->numberBetween(0, 1) : 0);
                    $totalDonations = fake()->numberBetween(0, 25);

                    return [
                        'role' => UserRole::DONOR->value,
                        'blood_group' => fake()->randomElement($bloodGroups),
                        'division_id' => $upz->district->division_id,
                        'district_id' => $upz->district_id,
                        'upazila_id' => $upz->id,
                        'is_donor' => true,
                        'is_onboarded' => true,
                        'is_available' => fake()->boolean(85),
                        'is_ready_now' => fake()->boolean(20),
                        'dfi_score' => fake()->randomFloat(2, 0, 100),
                        'priority_tier' => $priorityTier,
                        'super_critical_tokens' => $tokens,
                        'total_donations' => $totalDonations,
                        'total_verified_donations' => max(0, $totalDonations - fake()->numberBetween(0, 3)),
                        'last_donated_at' => $totalDonations > 0
                            ? now()->subDays(fake()->numberBetween(30, 360))->toDateString()
                            : null,
                        'gender' => fake()->randomElement(['male', 'female']),
                        'date_of_birth' => now()->subYears(fake()->numberBetween(18, 55))->toDateString(),
                        'weight' => fake()->numberBetween(45, 85),
                    ];
                })
                ->createQuietly();

            $created += $users->count();
            $bar?->advance($users->count());
        }

        $bar?->finish();
        $this->command?->newLine();
        $this->command?->info("✅ {$created} users seeded.");
    }
}
