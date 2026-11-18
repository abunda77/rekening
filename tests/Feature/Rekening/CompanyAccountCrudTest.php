<?php

use App\Livewire\Rekening\CompanyAccountCrud;
use App\Models\Agent;
use App\Models\AgentSupplier;
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

it('can search company accounts by agent supplier name and contact number', function () {
    $supplier = AgentSupplier::factory()->create([
        'name' => 'PT Mitra Supplier',
        'contact_number' => '081299887766',
    ]);

    CompanyAccount::factory()->create([
        'company_name' => 'PT Sumber Dana',
        'account_number' => '6666000011',
        'agent_supplier_id' => $supplier->id,
    ]);
    CompanyAccount::factory()->create([
        'company_name' => 'PT Tanpa Supplier',
        'account_number' => '6666000022',
        'agent_supplier_id' => null,
    ]);

    Livewire::actingAs($this->user)
        ->test(CompanyAccountCrud::class)
        ->set('search', 'Mitra Supplier')
        ->assertSee('6666000011')
        ->assertDontSee('6666000022')
        ->set('search', '081299887766')
        ->assertSee('6666000011')
        ->assertDontSee('6666000022');
});

it('can search company accounts by mobile banking keyword', function () {
    CompanyAccount::factory()->create([
        'company_name' => 'PT Digital Nusantara',
        'account_number' => '8888000011',
        'mobile_banking' => "User : pt_nusantara\nPassword : rahasia123\nPIN : 654321",
    ]);
    CompanyAccount::factory()->create([
        'company_name' => 'PT Karya Abadi',
        'account_number' => '8888000022',
        'mobile_banking' => "User : pt_abadi\nPIN : 123456",
    ]);

    Livewire::actingAs($this->user)
        ->test(CompanyAccountCrud::class)
        ->set('search', 'pt_nusantara')
        ->assertSee('8888000011')
        ->assertDontSee('8888000022');
});

it('does not search company account mobile banking with a numeric keyword', function () {
    CompanyAccount::factory()->create([
        'company_name' => 'PT Digital Nusantara',
        'account_number' => '8888000011',
        'mobile_banking' => "User : pt_nusantara\nPassword : rahasia123\nPIN : 654321",
    ]);
    CompanyAccount::factory()->create([
        'company_name' => 'PT Karya Abadi',
        'account_number' => '8888000022',
        'mobile_banking' => "User : pt_abadi\nPIN : 123456",
    ]);

    Livewire::actingAs($this->user)
        ->test(CompanyAccountCrud::class)
        ->set('search', '654321')
        ->assertDontSee('8888000011')
        ->assertDontSee('8888000022');
});

it('can filter company accounts by user and agent supplier', function () {
    $agent = Agent::factory()->create();
    $otherAgent = Agent::factory()->create();
    $supplier = AgentSupplier::factory()->create();
    $otherSupplier = AgentSupplier::factory()->create();

    CompanyAccount::factory()->create([
        'company_name' => 'PT Filter Satu',
        'account_number' => '5050505050',
        'agent_id' => $agent->id,
        'agent_supplier_id' => $supplier->id,
    ]);
    CompanyAccount::factory()->create([
        'company_name' => 'PT Filter Dua',
        'account_number' => '6060606060',
        'agent_id' => $otherAgent->id,
        'agent_supplier_id' => $otherSupplier->id,
    ]);

    Livewire::actingAs($this->user)
        ->test(CompanyAccountCrud::class)
        ->set('filterAgent', $agent->id)
        ->assertSee('5050505050')
        ->assertDontSee('6060606060')
        ->set('filterAgent', '')
        ->set('filterAgentSupplier', $supplier->id)
        ->assertSee('5050505050')
        ->assertDontSee('6060606060');
});

it('can sort company accounts by user and agent supplier name', function () {
    $agentA = Agent::factory()->create(['agent_name' => 'Aaa User']);
    $agentZ = Agent::factory()->create(['agent_name' => 'Zzz User']);
    $supplierA = AgentSupplier::factory()->create(['name' => 'Aaa Supplier']);
    $supplierZ = AgentSupplier::factory()->create(['name' => 'Zzz Supplier']);

    CompanyAccount::factory()->create([
        'company_name' => 'PT Sortir Satu',
        'account_number' => '7070707070',
        'agent_id' => $agentA->id,
        'agent_supplier_id' => $supplierZ->id,
    ]);
    CompanyAccount::factory()->create([
        'company_name' => 'PT Sortir Dua',
        'account_number' => '8080808080',
        'agent_id' => $agentZ->id,
        'agent_supplier_id' => $supplierA->id,
    ]);

    Livewire::actingAs($this->user)
        ->test(CompanyAccountCrud::class)
        ->call('sortBy', 'agent_name')
        ->assertSet('sortField', 'agent_name')
        ->assertSeeInOrder(['7070707070', '8080808080'])
        ->call('sortBy', 'agent_supplier_name')
        ->assertSet('sortField', 'agent_supplier_name')
        ->assertSeeInOrder(['8080808080', '7070707070']);
});

it('can search company accounts by note', function () {
    CompanyAccount::factory()->create([
        'company_name' => 'PT Sumber Rejeki',
        'account_number' => '7777888899',
        'note' => 'rekening khusus dividen tahunan',
    ]);
    CompanyAccount::factory()->create([
        'company_name' => 'PT Anugerah Abadi',
        'account_number' => '1111222233',
        'note' => 'rekening operasional gaji',
    ]);

    Livewire::actingAs($this->user)
        ->test(CompanyAccountCrud::class)
        ->set('search', 'dividen')
        ->assertSee('7777888899')
        ->assertDontSee('1111222233');
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
