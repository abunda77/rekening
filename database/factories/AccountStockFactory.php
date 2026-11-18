<?php

namespace Database\Factories;

use App\Models\AccountStock;
use App\Models\Agent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccountStock>
 */
class AccountStockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_number' => fake()->unique()->numerify('##########'),
            'customer_name' => fake()->optional()->name(),
            'bank_name' => fake()->randomElement(['BCA', 'BRI', 'BNI', 'MANDIRI', 'CIMB']),
            'account_type' => fake()->randomElement([AccountStock::TYPE_PERSONAL, AccountStock::TYPE_PERSONAL, AccountStock::TYPE_PT]),
            'agent_id' => null,
            'is_in_stock' => fake()->boolean(80),
            'user' => fake()->optional()->userName(),
            'note' => null,
            'is_vip' => fake()->boolean(20),
            'image' => null,
        ];
    }

    /**
     * Attach the stock to an agent.
     */
    public function forAgent(?Agent $agent = null): static
    {
        return $this->state(fn (): array => [
            'agent_id' => $agent?->id ?? Agent::factory(),
        ]);
    }
}
