<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Card;
use Illuminate\Database\Seeder;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create cards for each account
        $accounts = Account::all();

        foreach ($accounts as $account) {
            Card::factory()
                ->count(rand(1, 2))
                ->create([
                    'account_id' => $account->id,
                ]);
        }
    }
}
