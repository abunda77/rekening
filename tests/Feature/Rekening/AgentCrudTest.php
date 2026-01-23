<?php

use App\Livewire\Rekening\AgentCrud;
use App\Models\Agent;
use App\Models\User;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can access agents page when authenticated', function () {
    $this->actingAs($this->user)
        ->get(route('rekening.agents'))
        ->assertOk()
        ->assertSeeLivewire(AgentCrud::class);
});

it('redirects to login when not authenticated', function () {
    $this->get(route('rekening.agents'))
        ->assertRedirect(route('login'));
});

it('can search agents', function () {
    Agent::factory()->create(['agent_name' => 'John Doe', 'agent_code' => 'AG-001']);
    Agent::factory()->create(['agent_name' => 'Jane Smith', 'agent_code' => 'AG-002']);

    Livewire::actingAs($this->user)
        ->test(AgentCrud::class)
        ->set('search', 'John')
        ->assertSee('John Doe')
        ->assertDontSee('Jane Smith');
});

it('can create a new agent', function () {
    Livewire::actingAs($this->user)
        ->test(AgentCrud::class)
        ->call('openModal')
        ->set('agent_code', 'AG-NEW')
        ->set('agent_name', 'New Agent')
        ->set('usertelegram', '@newagent')
        ->set('password', 'password123')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('agents', [
        'agent_code' => 'AG-NEW',
        'agent_name' => 'New Agent',
    ]);
});

it('can edit an existing agent', function () {
    $agent = Agent::factory()->create(['agent_name' => 'Old Name']);

    Livewire::actingAs($this->user)
        ->test(AgentCrud::class)
        ->call('openModal', $agent->id)
        ->set('agent_name', 'Updated Name')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('agents', [
        'id' => $agent->id,
        'agent_name' => 'Updated Name',
    ]);
});

it('can delete an agent', function () {
    $agent = Agent::factory()->create();

    Livewire::actingAs($this->user)
        ->test(AgentCrud::class)
        ->call('confirmDelete', $agent->id)
        ->call('delete')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('agents', ['id' => $agent->id]);
});

it('validates required fields on create', function () {
    Livewire::actingAs($this->user)
        ->test(AgentCrud::class)
        ->call('openModal')
        ->set('agent_code', '')
        ->set('agent_name', '')
        ->set('password', '')
        ->call('save')
        ->assertHasErrors(['agent_code', 'agent_name', 'password']);
});
