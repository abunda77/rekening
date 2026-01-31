<?php

namespace Database\Factories;

use App\Models\Agent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AgentNotification>
 */
class AgentNotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['account', 'card', 'shipment', 'complaint'];
        $actions = ['created', 'updated'];

        return [
            'agent_id' => Agent::factory(),
            'type' => fake()->randomElement($types),
            'action' => fake()->randomElement($actions),
            'notifiable_type' => 'App\\Models\\Account',
            'notifiable_id' => fake()->uuid(),
            'title' => fake()->sentence(3),
            'message' => fake()->sentence(10),
            'read_at' => fake()->optional(0.3)->dateTimeThisMonth(),
        ];
    }

    /**
     * Indicate that the notification is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => null,
        ]);
    }

    /**
     * Indicate that the notification is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => now(),
        ]);
    }
}
