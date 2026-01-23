<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agent>
 */
class AgentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'agent_code' => strtoupper(Str::random(5)),
            'agent_name' => fake()->name(),
            'usertelegram' => '@'.fake()->userName(),
            'password' => 'password123',
        ];
    }
}
