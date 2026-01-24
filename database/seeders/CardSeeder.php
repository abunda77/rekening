<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Card;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Card::truncate();
        Schema::enableForeignKeyConstraints();
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
