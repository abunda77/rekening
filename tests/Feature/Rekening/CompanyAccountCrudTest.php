<?php

use App\Livewire\Rekening\CompanyAccountCrud;
use App\Models\Agent;
use App\Models\CompanyAccount;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can access the company accounts page when authenticated', function () {
    $this->actingAs($this->user)
        ->get(route('rekening.company-accounts'))
        ->assertOk()
        ->assertSee('Rekening PT')
        ->assertSeeLivewire(CompanyAccountCrud::class);
});

it('creates a company account without customer and agent relationships', function () {
    Livewire::actingAs($this->user)
        ->test(CompanyAccountCrud::class)
        ->call('openModal')
        ->set('company_name', 'PT Mandiri Sejahtera')
        ->set('bank_name', 'BCA')
        ->set('account_number', '9876543210')
        ->call('save')
        ->assertHasNoErrors();

    $account = CompanyAccount::query()->where('account_number', '9876543210')->firstOrFail();

    expect($account->customer_id)->toBeNull()
        ->and($account->agent_id)->toBeNull()
        ->and($account->status)->toBe('aktif');
});

it('creates a company account with customer and agent relationships', function () {
    $customer = Customer::factory()->create();
    $agent = Agent::factory()->create();

    Livewire::actingAs($this->user)
        ->test(CompanyAccountCrud::class)
        ->call('openModal')
        ->set('customer_id', $customer->id)
        ->set('agent_id', $agent->id)
        ->set('company_name', 'PT Relasi Indonesia')
        ->set('bank_name', 'MANDIRI')
        ->set('account_number', '1122334455')
        ->call('save')
        ->assertHasNoErrors();

    $account = CompanyAccount::query()->where('account_number', '1122334455')->firstOrFail();

    expect($account->customer->is($customer))->toBeTrue()
        ->and($account->agent->is($agent))->toBeTrue()
        ->and($customer->companyAccounts->contains($account))->toBeTrue()
        ->and($agent->companyAccounts->contains($account))->toBeTrue();
});

it('sets relationships to null when related records are deleted', function () {
    $customer = Customer::factory()->create();
    $agent = Agent::factory()->create();
    $account = CompanyAccount::factory()->create([
        'customer_id' => $customer->id,
        'agent_id' => $agent->id,
    ]);

    $customer->delete();
    $agent->delete();

    expect($account->fresh()->customer_id)->toBeNull()
        ->and($account->fresh()->agent_id)->toBeNull();
});

it('can update and delete a company account', function () {
    $account = CompanyAccount::factory()->create(['company_name' => 'PT Lama']);

    Livewire::actingAs($this->user)
        ->test(CompanyAccountCrud::class)
        ->call('openModal', $account->id)
        ->set('company_name', 'PT Baru')
        ->call('save')
        ->assertHasNoErrors()
        ->call('confirmDelete', $account->id)
        ->call('delete');

    expect($account->fresh())->toBeNull();
});

it('displays opening and expiry dates in the company account table', function () {
    CompanyAccount::factory()->create([
        'opening_date' => '2026-07-01',
        'expired_on' => '2026-07-31',
    ]);

    Livewire::actingAs($this->user)
        ->test(CompanyAccountCrud::class)
        ->assertSee('Tanggal Buka')
        ->assertSee('Tanggal Berakhir')
        ->assertSee('01 Jul 2026')
        ->assertSee('31 Jul 2026');
});
