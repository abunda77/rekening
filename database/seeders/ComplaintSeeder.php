<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Complaint;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ComplaintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Complaint::truncate();
        Schema::enableForeignKeyConstraints();
        $customers = Customer::all();
        $agents = Agent::all();

        // Create some sample complaints
        foreach ($customers->take(5) as $customer) {
            Complaint::factory()
                ->count(rand(0, 2))
                ->create([
                    'customer_id' => $customer->id,
                    'agent_id' => $agents->random()->id,
                ]);
        }

        // Create a few resolved complaints
        Complaint::factory()
            ->count(3)
            ->resolved()
            ->create([
                'customer_id' => $customers->random()->id,
                'agent_id' => $agents->random()->id,
            ]);
    }
}
