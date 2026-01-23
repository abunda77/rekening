<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Card>
 */
class CardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cardTypes = ['Debit', 'Kredit', 'Mastercard', 'Visa', 'GPN'];

        return [
            'account_id' => Account::factory(),
            'card_number' => fake()->unique()->creditCardNumber(),
            'cvv' => fake()->numerify('###'),
            'expiry_date' => fake()->dateTimeBetween('+1 year', '+5 years')->format('Y-m-d'),
            'pin_hash' => fake()->numerify('######'),
            'card_type' => fake()->randomElement($cardTypes),
        ];
    }
}
