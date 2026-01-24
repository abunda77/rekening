<?php

namespace Database\Seeders;

use App\Models\Agent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus semua data dulu
        Schema::disableForeignKeyConstraints();
        Agent::truncate();
        Schema::enableForeignKeyConstraints();

        // Create specific agent first
        Agent::create([
            'agent_code' => 'KSP987',
            'agent_name' => 'Admin Agent',
            'usertelegram' => '@admin_agent',
            'password' => 'password',
        ]);

        // Create additional random agents
        Agent::factory(5)->create();
    }
}
