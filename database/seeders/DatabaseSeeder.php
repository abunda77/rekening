<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default admin user (or update if exists)
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );

        // Call all seeders in order (respecting foreign keys)
        $this->call([
            AgentSeeder::class,
            CustomerSeeder::class,
            AccountSeeder::class,
            CardSeeder::class,
            ComplaintSeeder::class,
        ]);
    }
}
