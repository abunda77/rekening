<?php

use App\Livewire\Rekening\NoteCrud;
use App\Models\Note;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    // Ensure roles exist
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);
});

it('can access notes page when authenticated', function () {
    $this->actingAs($this->user)
        ->get(route('rekening.notes'))
        ->assertOk()
        ->assertSeeLivewire(NoteCrud::class);
});

it('can search notes', function () {
    Note::factory()->create([
        'title' => 'Meeting Notes',
        'user_id' => $this->user->id,
    ]);
    Note::factory()->create([
        'title' => 'Shopping List',
        'user_id' => $this->user->id,
    ]);

    Livewire::actingAs($this->user)
        ->test(NoteCrud::class)
        ->set('search', 'Meeting')
        ->assertSee('Meeting Notes')
        ->assertDontSee('Shopping List');
});

it('can create a new note', function () {
    Livewire::actingAs($this->user)
        ->test(NoteCrud::class)
        ->call('openModal')
        ->set('title', 'My Secret Note')
        ->set('content', 'This is a secret.')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('notes', [
        'title' => 'My Secret Note',
        'content' => 'This is a secret.',
        'user_id' => $this->user->id,
    ]);
});

it('can update a note', function () {
    $note = Note::factory()->create(['user_id' => $this->user->id]);

    Livewire::actingAs($this->user)
        ->test(NoteCrud::class)
        ->call('openModal', $note->id)
        ->set('title', 'Updated Title')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('notes', [
        'id' => $note->id,
        'title' => 'Updated Title',
    ]);
});

it('can delete a note', function () {
    $note = Note::factory()->create(['user_id' => $this->user->id]);

    Livewire::actingAs($this->user)
        ->test(NoteCrud::class)
        ->call('confirmDelete', $note->id)
        ->call('delete')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('notes', ['id' => $note->id]);
});

it('user can only see their own notes', function () {
    $otherUser = User::factory()->create();
    $myNote = Note::factory()->create(['user_id' => $this->user->id, 'title' => 'My Note']);
    $otherNote = Note::factory()->create(['user_id' => $otherUser->id, 'title' => 'Other Note']);

    Livewire::actingAs($this->user)
        ->test(NoteCrud::class)
        ->assertSee('My Note')
        ->assertDontSee('Other Note');
});

it('super admin can see all notes', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('Super Admin');

    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Note::factory()->create(['user_id' => $user1->id, 'title' => 'User 1 Note']);
    Note::factory()->create(['user_id' => $user2->id, 'title' => 'User 2 Note']);

    Livewire::actingAs($superAdmin)
        ->test(NoteCrud::class)
        ->assertSee('User 1 Note')
        ->assertSee('User 2 Note');
});

it('user cannot edit others note', function () {
    $otherUser = User::factory()->create();
    $otherNote = Note::factory()->create(['user_id' => $otherUser->id, 'title' => 'Other Note']);

    Livewire::actingAs($this->user)
        ->test(NoteCrud::class)
        ->call('openModal', $otherNote->id)
        ->assertForbidden();
});
