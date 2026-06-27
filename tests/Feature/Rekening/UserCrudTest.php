<?php

use App\Livewire\Rekening\UserCrud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Super Admin');
});

it('renders successfully', function () {
    Livewire::actingAs($this->admin)
        ->test(UserCrud::class)
        ->assertSuccessful();
});

it('can delete another user', function () {
    $target = User::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(UserCrud::class)
        ->call('confirmDelete', (string) $target->id)
        ->assertSet('showDeleteModal', true)
        ->assertSet('deleteId', (string) $target->id)
        ->call('delete');

    expect(User::find($target->id))->toBeNull();
});

it('prevents deleting self via confirmDelete', function () {
    Livewire::actingAs($this->admin)
        ->test(UserCrud::class)
        ->call('confirmDelete', (string) $this->admin->id)
        ->assertSet('showDeleteModal', false)
        ->assertSet('deleteId', null);

    expect(User::find($this->admin->id))->not->toBeNull();
});

it('prevents deleting self via delete method', function () {
    $component = Livewire::actingAs($this->admin)
        ->test(UserCrud::class);

    $component->set('deleteId', (string) $this->admin->id);
    $component->set('showDeleteModal', true);
    $component->call('delete');

    expect(User::find($this->admin->id))->not->toBeNull();
});

it('prevents deleting last super admin', function () {
    $editor = User::factory()->create();
    $editor->assignRole('Admin');

    User::whereHas('roles', fn ($q) => $q->where('name', 'Super Admin'))
        ->where('id', '!=', $this->admin->id)
        ->delete();

    Livewire::actingAs($editor)
        ->test(UserCrud::class)
        ->call('confirmDelete', (string) $this->admin->id)
        ->assertSet('showDeleteModal', false);

    expect(User::find($this->admin->id))->not->toBeNull();
});

it('can cancel delete', function () {
    $target = User::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(UserCrud::class)
        ->call('confirmDelete', (string) $target->id)
        ->assertSet('showDeleteModal', true)
        ->call('cancelDelete')
        ->assertSet('showDeleteModal', false)
        ->assertSet('deleteId', null);

    expect(User::find($target->id))->not->toBeNull();
});

it('can search users', function () {
    $john = User::factory()->create(['name' => 'John Doe']);
    $jane = User::factory()->create(['name' => 'Jane Smith']);

    Livewire::actingAs($this->admin)
        ->test(UserCrud::class)
        ->set('search', 'John')
        ->assertSee('John Doe')
        ->assertDontSee('Jane Smith');
});

it('can sort users', function () {
    Livewire::actingAs($this->admin)
        ->test(UserCrud::class)
        ->call('sortBy', 'name')
        ->assertSet('sortField', 'name')
        ->assertSet('sortDirection', 'asc');
});

it('can bulk delete users', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(UserCrud::class)
        ->set('selected', [(string) $user1->id, (string) $user2->id])
        ->call('confirmBulkDelete')
        ->call('bulkDelete');

    expect(User::find($user1->id))->toBeNull();
    expect(User::find($user2->id))->toBeNull();
});

it('denies access to unauthorized users', function () {
    $unauthorized = User::factory()->create();
    $unauthorized->assignRole('User');

    $this->actingAs($unauthorized)
        ->get(route('rekening.users'))
        ->assertForbidden();
});
