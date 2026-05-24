<?php

namespace Database\Seeders;

use App\Enums\BloodJourneyStatus;
use App\Enums\DonationStatus;
use App\Enums\UserRole;
use App\Models\District;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    public function run(): void
    {
        $donorIds = User::where('role', UserRole::DONOR->value)->pluck('id');

        if ($donorIds->isEmpty()) {
            throw new \RuntimeException('No donor users found. Run UserSeeder first.');
        }

        $districts = District::pluck('name');
        $journeyDistribution = [
            BloodJourneyStatus::DELIVERED->value => 60,
            BloodJourneyStatus::VERIFIED->value => 10,
            BloodJourneyStatus::DONATED->value => 10,
            BloodJourneyStatus::ACCEPTED->value => 5,
            BloodJourneyStatus::MATCHED->value => 5,
            BloodJourneyStatus::DISCARDED->value => 10,
        ];

        $targetCount = 30000;
        $chunkSize = 1000;
        $created = 0;

        $this->command?->info("🩸 Seeding {$targetCount} donations (with delivered/discarded mix)...");
        $bar = $this->command?->getOutput()->createProgressBar($targetCount);
        $bar?->start();

        while ($created < $targetCount) {
            $count = min($chunkSize, $targetCount - $created);
            $rows = [];

            for ($i = 0; $i < $count; $i++) {
                $journeyStatus = $this->weightedPick($journeyDistribution);
                $claimStatus = match ($journeyStatus) {
                    BloodJourneyStatus::DELIVERED->value,
                    BloodJourneyStatus::VERIFIED->value => DonationStatus::CONFIRMED->value,
                    BloodJourneyStatus::DISCARDED->value => DonationStatus::DISPUTED->value,
                    default => DonationStatus::PENDING->value,
                };

                $donationDate = now()->subDays(fake()->numberBetween(5, 365));

                $rows[] = [
                    'donor_id' => $donorIds->random(),
                    'blood_request_id' => null,
                    'donation_date' => $donationDate,
                    'hospital' => fake()->company(),
                    'district' => $districts->isNotEmpty() ? $districts->random() : fake()->city(),
                    'claim_status' => $claimStatus,
                    'claim_deadline' => $donationDate->copy()->addDays(7),
                    'points_earned' => fake()->numberBetween(30, 80),
                    'notes' => fake()->optional()->sentence(),
                    'journey_status' => $journeyStatus,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Donation::insert($rows);
            $created += $count;
            $bar?->advance($count);
        }

        $bar?->finish();
        $this->command?->newLine();
        $this->command?->info("✅ {$created} donations seeded.");
    }

    private function weightedPick(array $weights): string
    {
        $total = array_sum($weights);
        $roll = fake()->numberBetween(1, $total);
        $current = 0;

        foreach ($weights as $value => $weight) {
            $current += $weight;
            if ($roll <= $current) {
                return $value;
            }
        }

        return array_key_first($weights);
    }
}
