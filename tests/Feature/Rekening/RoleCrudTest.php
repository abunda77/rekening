<?php

use App\Livewire\Rekening\RoleCrud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed roles and permissions
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    // Create and authenticate user with proper permissions
    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');
});

it('renders successfully for authorized user', function () {
    Livewire::actingAs($this->user)
        ->test(RoleCrud::class)
        ->assertSuccessful();
});

it('displays roles in the list', function () {
    Livewire::actingAs($this->user)
        ->test(RoleCrud::class)
        ->assertSee('Super Admin')
        ->assertSee('Admin')
        ->assertSee('Manager')
        ->assertSee('User');
});

it('can create a new role', function () {
    Livewire::actingAs($this->user)
        ->test(RoleCrud::class)
        ->call('openModal')
        ->set('roleName', 'Test Role')
        ->set('selectedPermissions', ['view users', 'edit users'])
        ->call('save')
        ->assertDispatched('showModal', false);

    expect(Role::where('name', 'Test Role')->exists())->toBeTrue();

    $role = Role::where('name', 'Test Role')->first();
    expect($role->hasPermissionTo('view users'))->toBeTrue();
    expect($role->hasPermissionTo('edit users'))->toBeTrue();
});

it('validates role name is required', function () {
    Livewire::actingAs($this->user)
        ->test(RoleCrud::class)
        ->call('openModal')
        ->set('roleName', '')
        ->call('save')
        ->assertHasErrors(['roleName' => 'required']);
});

it('validates role name is unique', function () {
    Livewire::actingAs($this->user)
        ->test(RoleCrud::class)
        ->call('openModal')
        ->set('roleName', 'Super Admin')
        ->call('save')
        ->assertHasErrors(['roleName' => 'unique']);
});

it('can edit an existing role', function () {
    $role = Role::create(['name' => 'Test Role', 'guard_name' => 'web']);
    $role->givePermissionTo('view users');

    Livewire::actingAs($this->user)
        ->test(RoleCrud::class)
        ->call('openModal', $role->id)
        ->set('roleName', 'Updated Role')
        ->set('selectedPermissions', ['view users', 'create users'])
        ->call('save');

    $role->refresh();
    expect($role->name)->toBe('Updated Role');
    expect($role->hasPermissionTo('create users'))->toBeTrue();
});

it('can delete a role', function () {
    $role = Role::create(['name' => 'Deletable Role', 'guard_name' => 'web']);

    Livewire::actingAs($this->user)
        ->test(RoleCrud::class)
        ->call('confirmDelete', $role->id)
        ->call('delete');

    expect(Role::where('name', 'Deletable Role')->exists())->toBeFalse();
});

it('prevents deleting super admin role', function () {
    $superAdmin = Role::where('name', 'Super Admin')->first();

    Livewire::actingAs($this->user)
        ->test(RoleCrud::class)
        ->call('confirmDelete', $superAdmin->id)
        ->call('delete')
        ->assertDispatched('flash', ['error' => 'Role Super Admin tidak dapat dihapus.']);

    expect(Role::where('name', 'Super Admin')->exists())->toBeTrue();
});

it('can search roles', function () {
    Role::create(['name' => 'Marketing Manager', 'guard_name' => 'web']);
    Role::create(['name' => 'Sales Rep', 'guard_name' => 'web']);

    Livewire::actingAs($this->user)
        ->test(RoleCrud::class)
        ->set('search', 'Marketing')
        ->assertSee('Marketing Manager')
        ->assertDontSee('Sales Rep');
});

it('can sort roles by name', function () {
    Livewire::actingAs($this->user)
        ->test(RoleCrud::class)
        ->call('sortBy', 'name')
        ->assertSet('sortField', 'name')
        ->assertSet('sortDirection', 'asc');
});

it('shows correct permissions count for roles', function () {
    $role = Role::where('name', 'Super Admin')->first();

    Livewire::actingAs($this->user)
        ->test(RoleCrud::class)
        ->assertSee((string) $role->permissions->count());
});

it('denies access to unauthorized users', function () {
    $unauthorizedUser = User::factory()->create();
    $unauthorizedUser->assignRole('User');

    $this->actingAs($unauthorizedUser)
        ->get(route('rekening.roles'))
        ->assertForbidden();
});

it('allows access to users with view roles permission', function () {
    $authorizedUser = User::factory()->create();
    $authorizedUser->assignRole('Super Admin');

    $this->actingAs($authorizedUser)
        ->get(route('rekening.roles'))
        ->assertSuccessful();
});

it('can bulk delete roles', function () {
    $role1 = Role::create(['name' => 'Role 1', 'guard_name' => 'web']);
    $role2 = Role::create(['name' => 'Role 2', 'guard_name' => 'web']);

    Livewire::actingAs($this->user)
        ->test(RoleCrud::class)
        ->set('selected', [$role1->id, $role2->id])
        ->call('confirmBulkDelete')
        ->call('bulkDelete');

    expect(Role::where('name', 'Role 1')->exists())->toBeFalse();
    expect(Role::where('name', 'Role 2')->exists())->toBeFalse();
});

it('prevents bulk deleting super admin role', function () {
    $superAdmin = Role::where('name', 'Super Admin')->first();
    $otherRole = Role::create(['name' => 'Other Role', 'guard_name' => 'web']);

    Livewire::actingAs($this->user)
        ->test(RoleCrud::class)
        ->set('selected', [$superAdmin->id, $otherRole->id])
        ->call('confirmBulkDelete')
        ->call('bulkDelete');

    expect(Role::where('name', 'Super Admin')->exists())->toBeTrue();
    expect(Role::where('name', 'Other Role')->exists())->toBeFalse();
});
