<?php

namespace Database\Factories;

use App\Models\CompanyAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanyAccount>
 */
class CompanyAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => null,
            'agent_id' => null,
            'company_name' => fake()->company(),
            'bank_name' => fake()->randomElement(['MANDIRI', 'BCA', 'BRI', 'BNI', 'CIMB']),
            'branch' => fake()->city(),
            'account_number' => fake()->unique()->numerify('#############'),
            'opening_date' => fake()->date(),
            'expired_on' => fake()->optional()->date(),
            'mobile_banking' => fake()->optional()->text(),
            'note' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(['aktif', 'bermasalah', 'nonaktif']),
        ];
    }
}
