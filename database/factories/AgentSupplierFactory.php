<?php

namespace Database\Factories;

use App\Models\AgentSupplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AgentSupplier>
 */
class AgentSupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->name(),
            'contact_number' => fake()->optional()->numerify('08##########'),
        ];
    }
}
