<?php

use App\Livewire\Rekening\CardCrud;
use App\Models\Account;
use App\Models\Card;
use App\Models\Customer;
use App\Models\User;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->customer = Customer::factory()->create();
    $this->account = Account::factory()->create(['customer_id' => $this->customer->id]);
});

it('can access cards page when authenticated', function () {
    $this->actingAs($this->user)
        ->get(route('rekening.cards'))
        ->assertOk()
        ->assertSeeLivewire(CardCrud::class);
});

it('can create a new card', function () {
    Livewire::actingAs($this->user)
        ->test(CardCrud::class)
        ->call('openModal')
        ->set('account_id', $this->account->id)
        ->set('card_number', '4111111111111111')
        ->set('cvv', '123')
        ->set('pin_hash', '123456')
        ->set('card_type', 'Visa')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('cards', [
        'card_number' => '4111111111111111',
        'card_type' => 'Visa',
    ]);
});

it('can delete a card', function () {
    $card = Card::factory()->create(['account_id' => $this->account->id]);

    Livewire::actingAs($this->user)
        ->test(CardCrud::class)
        ->call('confirmDelete', $card->id)
        ->call('delete')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('cards', ['id' => $card->id]);
});

it('validates cvv must be 3 digits', function () {
    Livewire::actingAs($this->user)
        ->test(CardCrud::class)
        ->call('openModal')
        ->set('account_id', $this->account->id)
        ->set('card_number', '4111111111111111')
        ->set('cvv', '12')
        ->set('pin_hash', '123456')
        ->call('save')
        ->assertHasErrors(['cvv']);
});
