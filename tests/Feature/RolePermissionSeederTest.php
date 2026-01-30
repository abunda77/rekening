<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);
});

it('creates all expected roles', function () {
    $expectedRoles = ['Super Admin', 'Admin', 'Manager', 'User'];

    foreach ($expectedRoles as $roleName) {
        expect(Role::where('name', $roleName)->exists())->toBeTrue();
    }

    expect(Role::count())->toBe(4);
});

it('creates all expected permissions', function () {
    $expectedPermissions = [
        // User Management
        'view users', 'create users', 'edit users', 'delete users',
        // Role Management
        'view roles', 'create roles', 'edit roles', 'delete roles',
        // Permission Management
        'view permissions', 'assign permissions',
        // Agent Management
        'view agents', 'create agents', 'edit agents', 'delete agents',
        // Customer Management
        'view customers', 'create customers', 'edit customers', 'delete customers',
        // Account Management
        'view accounts', 'create accounts', 'edit accounts', 'delete accounts',
        // Card Management
        'view cards', 'create cards', 'edit cards', 'delete cards',
        // Complaint Management
        'view complaints', 'create complaints', 'edit complaints', 'delete complaints',
        // Shipment Management
        'view shipments', 'create shipments', 'edit shipments', 'delete shipments',
    ];

    foreach ($expectedPermissions as $permissionName) {
        expect(Permission::where('name', $permissionName)->exists())->toBeTrue();
    }

    expect(Permission::count())->toBe(34);
});

it('assigns all permissions to super admin', function () {
    $superAdmin = Role::where('name', 'Super Admin')->first();

    expect($superAdmin->permissions)->toHaveCount(34);
    expect($superAdmin->hasPermissionTo('view roles'))->toBeTrue();
    expect($superAdmin->hasPermissionTo('create roles'))->toBeTrue();
    expect($superAdmin->hasPermissionTo('delete users'))->toBeTrue();
});

it('assigns correct permissions to admin role', function () {
    $admin = Role::where('name', 'Admin')->first();

    // Admin should have all permissions except role/permission management
    expect($admin->hasPermissionTo('view roles'))->toBeTrue();
    expect($admin->hasPermissionTo('create roles'))->toBeFalse();
    expect($admin->hasPermissionTo('edit roles'))->toBeFalse();
    expect($admin->hasPermissionTo('delete roles'))->toBeFalse();
    expect($admin->hasPermissionTo('assign permissions'))->toBeFalse();

    // But should have other management permissions
    expect($admin->hasPermissionTo('view users'))->toBeTrue();
    expect($admin->hasPermissionTo('create users'))->toBeTrue();
    expect($admin->hasPermissionTo('edit users'))->toBeTrue();
    expect($admin->hasPermissionTo('delete users'))->toBeTrue();
});

it('assigns correct permissions to manager role', function () {
    $manager = Role::where('name', 'Manager')->first();

    // Manager should have view and edit permissions
    expect($manager->hasPermissionTo('view users'))->toBeTrue();
    expect($manager->hasPermissionTo('edit users'))->toBeTrue();
    expect($manager->hasPermissionTo('create users'))->toBeFalse();
    expect($manager->hasPermissionTo('delete users'))->toBeFalse();

    // Manager should have create permissions for operational modules
    expect($manager->hasPermissionTo('create agents'))->toBeTrue();
    expect($manager->hasPermissionTo('create customers'))->toBeTrue();
});

it('assigns only view permissions to user role', function () {
    $user = Role::where('name', 'User')->first();

    // User should only have view permissions
    expect($user->hasPermissionTo('view users'))->toBeTrue();
    expect($user->hasPermissionTo('view agents'))->toBeTrue();
    expect($user->hasPermissionTo('view customers'))->toBeTrue();

    // User should not have create/edit/delete permissions
    expect($user->hasPermissionTo('create users'))->toBeFalse();
    expect($user->hasPermissionTo('edit users'))->toBeFalse();
    expect($user->hasPermissionTo('delete users'))->toBeFalse();
});
