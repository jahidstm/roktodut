<?php

namespace Database\Factories;

use App\Enums\BloodGroup;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $priorityTier = $this->randomPriorityTier();
        $tokens = $priorityTier === 'gold'
            ? fake()->numberBetween(1, 2)
            : ($priorityTier === 'silver' ? fake()->numberBetween(0, 1) : 0);
        $bloodGroups = array_map(fn(BloodGroup $group) => $group->value, BloodGroup::cases());

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => UserRole::DONOR->value,
            'blood_group' => fake()->randomElement($bloodGroups),
            'is_donor' => true,
            'is_onboarded' => true,
            'is_available' => fake()->boolean(85),
            'is_ready_now' => fake()->boolean(20),
            'last_login_at' => fake()->dateTimeBetween('-60 days', 'now'),
            'dfi_score' => fake()->randomFloat(2, 0, 100),
            'priority_tier' => $priorityTier,
            'super_critical_tokens' => $tokens,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    private function randomPriorityTier(): string
    {
        $roll = fake()->numberBetween(1, 100);

        if ($roll <= 10) {
            return 'gold';
        }

        if ($roll <= 30) {
            return 'silver';
        }

        return 'standard';
    }
}
