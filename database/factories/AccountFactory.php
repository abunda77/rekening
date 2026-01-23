<?php

namespace Database\Factories;

use App\Models\Agent;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $banks = ['MANDIRI', 'BCA', 'BRI', 'BNI', 'CIMB', 'BTN', 'PERMATA', 'DANAMON'];

        return [
            'customer_id' => Customer::factory(),
            'agent_id' => Agent::factory(),
            'bank_name' => fake()->randomElement($banks),
            'branch' => fake()->city(),
            'account_number' => fake()->unique()->numerify('#############'),
            'opening_date' => fake()->date(),
            'note' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(['aktif', 'bermasalah', 'nonaktif']),
        ];
    }

    /**
     * Indicate that the account is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'aktif',
        ]);
    }

    /**
     * Indicate that the account has problems.
     */
    public function problematic(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'bermasalah',
        ]);
    }

    /**
     * Indicate that the account is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'nonaktif',
        ]);
    }
}
