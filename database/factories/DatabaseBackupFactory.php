<?php

namespace Database\Factories;

use App\Models\DatabaseBackup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DatabaseBackup>
 */
class DatabaseBackupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DatabaseBackup::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $timestamp = now()->format('Y-m-d_H-i-s');

        return [
            'filename' => "backup_{$timestamp}.sql",
            'type' => $this->faker->randomElement(['manual', 'scheduled']),
            'status' => $this->faker->randomElement(['success', 'failed']),
            'size' => $this->faker->numberBetween(1024, 1073741824), // 1 KB to 1 GB
        ];
    }

    /**
     * Indicate that the backup is manual.
     */
    public function manual(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'manual',
        ]);
    }

    /**
     * Indicate that the backup is scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'scheduled',
        ]);
    }

    /**
     * Indicate that the backup was successful.
     */
    public function successful(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'success',
            'size' => $this->faker->numberBetween(1024, 1073741824),
        ]);
    }

    /**
     * Indicate that the backup failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'size' => 0,
        ]);
    }
}
