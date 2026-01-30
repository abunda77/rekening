<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions grouped by module
        $permissions = [
            // User Management
            ['name' => 'view users', 'module' => 'user'],
            ['name' => 'create users', 'module' => 'user'],
            ['name' => 'edit users', 'module' => 'user'],
            ['name' => 'delete users', 'module' => 'user'],

            // Role Management
            ['name' => 'view roles', 'module' => 'role'],
            ['name' => 'create roles', 'module' => 'role'],
            ['name' => 'edit roles', 'module' => 'role'],
            ['name' => 'delete roles', 'module' => 'role'],

            // Permission Management
            ['name' => 'view permissions', 'module' => 'permission'],
            ['name' => 'assign permissions', 'module' => 'permission'],

            // Agent Management
            ['name' => 'view agents', 'module' => 'agent'],
            ['name' => 'create agents', 'module' => 'agent'],
            ['name' => 'edit agents', 'module' => 'agent'],
            ['name' => 'delete agents', 'module' => 'agent'],

            // Customer Management
            ['name' => 'view customers', 'module' => 'customer'],
            ['name' => 'create customers', 'module' => 'customer'],
            ['name' => 'edit customers', 'module' => 'customer'],
            ['name' => 'delete customers', 'module' => 'customer'],

            // Account Management
            ['name' => 'view accounts', 'module' => 'account'],
            ['name' => 'create accounts', 'module' => 'account'],
            ['name' => 'edit accounts', 'module' => 'account'],
            ['name' => 'delete accounts', 'module' => 'account'],

            // Card Management
            ['name' => 'view cards', 'module' => 'card'],
            ['name' => 'create cards', 'module' => 'card'],
            ['name' => 'edit cards', 'module' => 'card'],
            ['name' => 'delete cards', 'module' => 'card'],

            // Complaint Management
            ['name' => 'view complaints', 'module' => 'complaint'],
            ['name' => 'create complaints', 'module' => 'complaint'],
            ['name' => 'edit complaints', 'module' => 'complaint'],
            ['name' => 'delete complaints', 'module' => 'complaint'],

            // Shipment Management
            ['name' => 'view shipments', 'module' => 'shipment'],
            ['name' => 'create shipments', 'module' => 'shipment'],
            ['name' => 'edit shipments', 'module' => 'shipment'],
            ['name' => 'delete shipments', 'module' => 'shipment'],
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'web',
            ]);
        }

        // Create roles
        $superAdmin = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $manager = Role::create(['name' => 'Manager', 'guard_name' => 'web']);
        $user = Role::create(['name' => 'User', 'guard_name' => 'web']);

        // Assign permissions to Super Admin (all permissions)
        $superAdmin->givePermissionTo(Permission::all());

        // Assign permissions to Admin (all except role/permission management)
        $adminPermissions = Permission::whereNotIn('name', [
            'create roles',
            'edit roles',
            'delete roles',
            'assign permissions',
        ])->get();
        $admin->givePermissionTo($adminPermissions);

        // Assign permissions to Manager (view and edit within specific modules)
        $managerPermissions = Permission::whereIn('name', [
            'view users',
            'edit users',
            'view agents',
            'create agents',
            'edit agents',
            'view customers',
            'create customers',
            'edit customers',
            'view accounts',
            'create accounts',
            'edit accounts',
            'view cards',
            'create cards',
            'edit cards',
            'view complaints',
            'create complaints',
            'edit complaints',
            'view shipments',
            'create shipments',
            'edit shipments',
        ])->get();
        $manager->givePermissionTo($managerPermissions);

        // Assign permissions to User (view-only permissions)
        $userPermissions = Permission::where('name', 'like', 'view %')->get();
        $user->givePermissionTo($userPermissions);
    }
}
