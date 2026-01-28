<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipment>
 */
class ShipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'agent_id' => \App\Models\Agent::factory(),
            'account_id' => \App\Models\Account::factory(),
            'delivery_date' => fake()->date(),
            'expedition' => fake()->randomElement(['JNE', 'JNT', 'POS', 'TIKI']),
            'status' => fake()->randomElement(['SENT', 'PROCESS', 'OTW']),
            'receipt_number' => fake()->optional()->uuid(),
        ];
    }
}
