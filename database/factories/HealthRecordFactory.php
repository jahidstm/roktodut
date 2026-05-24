<?php

namespace Database\Factories;

use App\Models\HealthRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HealthRecord>
 */
class HealthRecordFactory extends Factory
{
    protected $model = HealthRecord::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'systolic_bp' => $this->faker->numberBetween(105, 130),
            'diastolic_bp' => $this->faker->numberBetween(65, 85),
            'hemoglobin_level' => $this->faker->randomFloat(2, 11.5, 15.5),
            'weight_kg' => $this->faker->randomFloat(2, 48, 85),
            'recorded_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'source' => $this->faker->randomElement(['self_reported', 'verified_donation']),
        ];
    }
}
