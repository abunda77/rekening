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
        // Call RolePermissionSeeder first (required before user creation)
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // Create default admin user (or update if exists)
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );

        // Assign Super Admin role to admin user (sync to ensure it's assigned)
        $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'Super Admin')->first();
        if ($superAdminRole && ! $adminUser->hasRole('Super Admin')) {
            $adminUser->assignRole($superAdminRole);
        }

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
