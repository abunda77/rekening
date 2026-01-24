<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Agent;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Account::truncate();
        Schema::enableForeignKeyConstraints();
        // Get Wimpi Gindasari customer
        $wimpi = Customer::where('nik', '3213096212020011')->first();
        $agent = Agent::first();

        if ($wimpi && $agent) {
            // Create specific account from Planning.md
            Account::create([
                'customer_id' => $wimpi->id,
                'agent_id' => $agent->id,
                'bank_name' => 'MANDIRI',
                'branch' => 'Subang',
                'account_number' => '1250016567422',
                'opening_date' => '2002-12-22',
                'status' => 'aktif',
                'note' => 'Rekening utama',
            ]);
        }

        // Create additional random accounts
        $customers = Customer::all();
        $agents = Agent::all();

        foreach ($customers->take(5) as $customer) {
            Account::factory()
                ->count(rand(1, 2))
                ->create([
                    'customer_id' => $customer->id,
                    'agent_id' => $agents->random()->id,
                ]);
        }
    }
}
