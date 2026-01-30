<?php

use App\Livewire\Rekening\PermissionCrud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

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
        ->test(PermissionCrud::class)
        ->assertSuccessful();
});

it('displays permissions in the list', function () {
    Livewire::actingAs($this->user)
        ->test(PermissionCrud::class)
        ->assertSee('view users')
        ->assertSee('create users')
        ->assertSee('edit users')
        ->assertSee('delete users');
});

it('groups permissions by module', function () {
    $component = Livewire::actingAs($this->user)
        ->test(PermissionCrud::class);

    $permissionsByModule = $component->get('permissionsByModule');

    expect($permissionsByModule)->toHaveKey('users');
    expect($permissionsByModule)->toHaveKey('agents');
    expect($permissionsByModule)->toHaveKey('customers');
});

it('can search permissions', function () {
    Livewire::actingAs($this->user)
        ->test(PermissionCrud::class)
        ->set('search', 'view')
        ->assertSee('view users')
        ->assertSee('view agents')
        ->assertDontSee('create users');
});

it('can filter permissions by module', function () {
    Livewire::actingAs($this->user)
        ->test(PermissionCrud::class)
        ->set('selectedModule', 'users')
        ->assertSee('view users')
        ->assertSee('create users')
        ->assertDontSee('view agents');
});

it('displays roles that have each permission', function () {
    Livewire::actingAs($this->user)
        ->test(PermissionCrud::class)
        ->assertSee('Super Admin');
});

it('can sort permissions by name', function () {
    Livewire::actingAs($this->user)
        ->test(PermissionCrud::class)
        ->call('sortBy', 'name')
        ->assertSet('sortField', 'name')
        ->assertSet('sortDirection', 'asc');
});

it('denies access to unauthorized users', function () {
    $unauthorizedUser = User::factory()->create();
    $unauthorizedUser->assignRole('User');

    $this->actingAs($unauthorizedUser)
        ->get(route('rekening.permissions'))
        ->assertForbidden();
});

it('allows access to users with view permissions permission', function () {
    $authorizedUser = User::factory()->create();
    $authorizedUser->givePermissionTo('view permissions');

    $this->actingAs($authorizedUser)
        ->get(route('rekening.permissions'))
        ->assertSuccessful();
});

it('shows total permission count', function () {
    Livewire::actingAs($this->user)
        ->test(PermissionCrud::class)
        ->assertSee('Total: 34 permission');
});

it('displays correct module list', function () {
    $component = Livewire::actingAs($this->user)
        ->test(PermissionCrud::class);

    $modules = $component->get('modules');

    expect($modules)->toContain('users');
    expect($modules)->toContain('agents');
    expect($modules)->toContain('customers');
    expect($modules)->toContain('accounts');
    expect($modules)->toContain('roles');
    expect($modules)->toContain('permissions');
});

it('shows permission action badges correctly', function () {
    Livewire::actingAs($this->user)
        ->test(PermissionCrud::class)
        ->assertSee('create')
        ->assertSee('view')
        ->assertSee('edit')
        ->assertSee('delete');
});

it('displays permission details in table', function () {
    Livewire::actingAs($this->user)
        ->test(PermissionCrud::class)
        ->assertSee('Detail Permission')
        ->assertSee('Permission')
        ->assertSee('Modul')
        ->assertSee('Roles');
});
