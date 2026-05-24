<?php

namespace Database\Seeders;

use App\Models\HealthRecord;
use App\Models\User;
use Illuminate\Database\Seeder;

class HealthRecordSeeder extends Seeder
{
    public function run(): void
    {
        $totalUsers = User::count();
        $this->command?->info("📈 Seeding health records for {$totalUsers} users...");
        $bar = $this->command?->getOutput()->createProgressBar($totalUsers);
        $bar?->start();

        $points = 12; // 6 months, every ~15 days
        $intervalDays = 15;
        $now = now();

        User::select('id')->orderBy('id')->chunk(200, function ($users) use ($points, $intervalDays, $now, $bar) {
            $rows = [];

            foreach ($users as $user) {
                $isAnomaly = fake()->boolean(12); // ~12% gradual drop
                $baseHb = fake()->randomFloat(2, 12.0, 15.5);
                $baseWeight = fake()->randomFloat(2, 50, 85);
                $dropStart = $points - 4;

                for ($i = 0; $i < $points; $i++) {
                    $recordedAt = $now->copy()->subDays($intervalDays * ($points - 1 - $i));
                    $hb = $baseHb + fake()->randomFloat(2, -0.2, 0.2);

                    if ($isAnomaly && $i >= $dropStart) {
                        $hb -= 0.2 * ($i - $dropStart + 1);
                    }

                    $rows[] = [
                        'user_id' => $user->id,
                        'systolic_bp' => fake()->numberBetween(105, 130),
                        'diastolic_bp' => fake()->numberBetween(65, 85),
                        'hemoglobin_level' => max(8.0, round($hb, 2)),
                        'weight_kg' => max(40.0, round($baseWeight + fake()->randomFloat(2, -1.5, 1.5), 2)),
                        'recorded_at' => $recordedAt,
                        'source' => fake()->boolean(65) ? 'verified_donation' : 'self_reported',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            HealthRecord::insert($rows);
            $bar?->advance($users->count());
        });

        $bar?->finish();
        $this->command?->newLine();
        $this->command?->info('✅ Health records seeded.');
    }
}
