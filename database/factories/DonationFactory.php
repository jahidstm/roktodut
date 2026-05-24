<?php

namespace Database\Factories;

use App\Enums\BloodJourneyStatus;
use App\Enums\DonationStatus;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    protected $model = Donation::class;

    public function definition(): array
    {
        return [
            'donor_id' => User::factory(),
            'blood_request_id' => null,
            'donation_date' => $this->faker->dateTimeBetween('-12 months', 'now'),
            'hospital' => $this->faker->company(),
            'district' => $this->faker->city(),
            'claim_status' => $this->faker->randomElement([
                DonationStatus::CONFIRMED->value,
                DonationStatus::AUTO_APPROVED->value,
                DonationStatus::PENDING->value,
            ]),
            'claim_deadline' => $this->faker->dateTimeBetween('now', '+30 days'),
            'points_earned' => $this->faker->numberBetween(30, 80),
            'notes' => $this->faker->optional()->sentence(),
            'journey_status' => $this->faker->randomElement(array_map(
                fn(BloodJourneyStatus $status) => $status->value,
                BloodJourneyStatus::cases()
            )),
        ];
    }
}
