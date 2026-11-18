<?php

use App\Livewire\Rekening\AccountCrud;
use App\Livewire\Rekening\AgentSupplierCrud;
use App\Livewire\Rekening\CompanyAccountCrud;
use App\Models\Account;
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

it('can access the agent suppliers page when authenticated', function () {
    $this->actingAs($this->user)
        ->get(route('rekening.agent-suppliers'))
        ->assertOk()
        ->assertSee('Manajemen Agent Supplier')
        ->assertSeeLivewire(AgentSupplierCrud::class);
});

it('can create an agent supplier', function () {
    Livewire::actingAs($this->user)
        ->test(AgentSupplierCrud::class)
        ->call('openModal')
        ->set('name', 'CV Sumber Rejeki')
        ->set('contact_number', '081234567890')
        ->call('save')
        ->assertHasNoErrors();

    $supplier = AgentSupplier::query()->where('name', 'CV Sumber Rejeki')->firstOrFail();

    expect($supplier->contact_number)->toBe('081234567890');
});

it('requires a name and rejects duplicate names', function () {
    AgentSupplier::factory()->create(['name' => 'PT Duplikat']);

    Livewire::actingAs($this->user)
        ->test(AgentSupplierCrud::class)
        ->call('openModal')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required'])
        ->set('name', 'PT Duplikat')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

it('can update and delete an agent supplier', function () {
    $supplier = AgentSupplier::factory()->create(['name' => 'Nama Lama']);

    Livewire::actingAs($this->user)
        ->test(AgentSupplierCrud::class)
        ->call('openModal', $supplier->id)
        ->set('name', 'Nama Baru')
        ->set('contact_number', '089999999999')
        ->call('save')
        ->assertHasNoErrors()
        ->call('confirmDelete', $supplier->id)
        ->call('delete');

    expect($supplier->fresh())->toBeNull();
});

it('can search agent suppliers by contact number', function () {
    AgentSupplier::factory()->create(['name' => 'Supplier A', 'contact_number' => '081111111111']);
    AgentSupplier::factory()->create(['name' => 'Supplier B', 'contact_number' => '082222222222']);

    Livewire::actingAs($this->user)
        ->test(AgentSupplierCrud::class)
        ->set('search', '081111111111')
        ->assertSee('Supplier A')
        ->assertDontSee('Supplier B');
});

it('creates a new agent supplier inline from the bank account form and links it', function () {
    $customer = Customer::factory()->create();

    Livewire::actingAs($this->user)
        ->test(AccountCrud::class)
        ->call('openModal')
        ->set('customer_id', $customer->id)
        ->set('showNewSupplierForm', true)
        ->set('new_supplier_name', 'Supplier Baru Bank')
        ->set('new_supplier_contact', '081200000001')
        ->call('saveNewSupplier')
        ->assertHasNoErrors()
        ->set('bank_name', 'BCA')
        ->set('account_number', '5550001111')
        ->call('save')
        ->assertHasNoErrors();

    $supplier = AgentSupplier::query()->where('name', 'Supplier Baru Bank')->firstOrFail();
    $account = Account::query()->where('account_number', '5550001111')->firstOrFail();

    expect($account->agent_supplier_id)->toBe($supplier->id)
        ->and($account->agentSupplier->is($supplier))->toBeTrue()
        ->and($supplier->accounts->contains($account))->toBeTrue();
});

it('creates a new agent supplier inline from the company account form and links it', function () {
    Livewire::actingAs($this->user)
        ->test(CompanyAccountCrud::class)
        ->call('openModal')
        ->set('company_name', 'PT Supplier Baru')
        ->set('showNewSupplierForm', true)
        ->set('new_supplier_name', 'Supplier Baru PT')
        ->set('new_supplier_contact', '081200000002')
        ->call('saveNewSupplier')
        ->assertHasNoErrors()
        ->set('bank_name', 'MANDIRI')
        ->set('account_number', '5550002222')
        ->call('save')
        ->assertHasNoErrors();

    $supplier = AgentSupplier::query()->where('name', 'Supplier Baru PT')->firstOrFail();
    $account = CompanyAccount::query()->where('account_number', '5550002222')->firstOrFail();

    expect($account->agent_supplier_id)->toBe($supplier->id)
        ->and($account->agentSupplier->is($supplier))->toBeTrue()
        ->and($supplier->companyAccounts->contains($account))->toBeTrue();
});

it('validates the inline agent supplier fields', function () {
    Livewire::actingAs($this->user)
        ->test(AccountCrud::class)
        ->call('openModal')
        ->set('showNewSupplierForm', true)
        ->set('new_supplier_name', '')
        ->call('saveNewSupplier')
        ->assertHasErrors(['new_supplier_name' => 'required']);

    expect(AgentSupplier::query()->count())->toBe(0);
});

it('nulls the agent supplier reference when the supplier is deleted', function () {
    $supplier = AgentSupplier::factory()->create();
    $account = Account::factory()->create(['agent_supplier_id' => $supplier->id]);
    $companyAccount = CompanyAccount::factory()->create(['agent_supplier_id' => $supplier->id]);

    $supplier->delete();

    expect($account->fresh()->agent_supplier_id)->toBeNull()
        ->and($companyAccount->fresh()->agent_supplier_id)->toBeNull();
});
