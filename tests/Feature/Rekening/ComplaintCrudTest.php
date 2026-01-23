<?php

use App\Livewire\Rekening\ComplaintCrud;
use App\Models\Agent;
use App\Models\Complaint;
use App\Models\Customer;
use App\Models\User;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->customer = Customer::factory()->create();
    $this->agent = Agent::factory()->create();
});

it('can access complaints page when authenticated', function () {
    $this->actingAs($this->user)
        ->get(route('rekening.complaints'))
        ->assertOk()
        ->assertSeeLivewire(ComplaintCrud::class);
});

it('can create a new complaint', function () {
    Livewire::actingAs($this->user)
        ->test(ComplaintCrud::class)
        ->call('openModal')
        ->set('customer_id', $this->customer->id)
        ->set('agent_id', $this->agent->id)
        ->set('subject', 'Test Complaint')
        ->set('description', 'This is a test complaint')
        ->set('status', 'pending')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('complaints', [
        'subject' => 'Test Complaint',
        'status' => 'pending',
    ]);
});

it('can update complaint status', function () {
    $complaint = Complaint::factory()->create([
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    Livewire::actingAs($this->user)
        ->test(ComplaintCrud::class)
        ->call('updateStatus', $complaint->id, 'processing')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('complaints', [
        'id' => $complaint->id,
        'status' => 'processing',
    ]);
});

it('can filter complaints by status', function () {
    Complaint::factory()->create([
        'subject' => 'Pending Complaint',
        'status' => 'pending',
        'customer_id' => $this->customer->id,
    ]);
    Complaint::factory()->create([
        'subject' => 'Resolved Complaint',
        'status' => 'resolved',
        'customer_id' => $this->customer->id,
    ]);

    Livewire::actingAs($this->user)
        ->test(ComplaintCrud::class)
        ->set('filterStatus', 'pending')
        ->assertSee('Pending Complaint')
        ->assertDontSee('Resolved Complaint');
});

it('can delete a complaint', function () {
    $complaint = Complaint::factory()->create(['customer_id' => $this->customer->id]);

    Livewire::actingAs($this->user)
        ->test(ComplaintCrud::class)
        ->call('confirmDelete', $complaint->id)
        ->call('delete')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('complaints', ['id' => $complaint->id]);
});
