<?php

use App\Livewire\Rekening\AccountCrud;
use App\Models\Account;
use App\Models\Agent;
use App\Models\AgentSupplier;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->customer = Customer::factory()->create();
    $this->agent = Agent::factory()->create();
});

it('can access accounts page when authenticated', function () {
    $this->actingAs($this->user)
        ->get(route('rekening.accounts'))
        ->assertOk()
        ->assertSeeLivewire(AccountCrud::class);
});

it('can search accounts', function () {
    Account::factory()->create([
        'account_number' => '1234567890',
        'bank_name' => 'BCA',
        'customer_id' => $this->customer->id,
    ]);
    Account::factory()->create([
        'account_number' => '0987654321',
        'bank_name' => 'MANDIRI',
        'customer_id' => $this->customer->id,
    ]);

    Livewire::actingAs($this->user)
        ->test(AccountCrud::class)
        ->set('search', '1234567890')
        ->assertSee('1234567890')
        ->assertDontSee('0987654321');
});

it('can create a new account', function () {
    Livewire::actingAs($this->user)
        ->test(AccountCrud::class)
        ->call('openModal')
        ->set('customer_id', $this->customer->id)
        ->set('agent_id', $this->agent->id)
        ->set('bank_name', 'BRI')
        ->set('branch', 'Jakarta')
        ->set('account_number', '9999888877')
        ->set('status', 'aktif')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('accounts', [
        'account_number' => '9999888877',
        'bank_name' => 'BRI',
    ]);
});

it('can search accounts by agent supplier name and contact number', function () {
    $supplier = AgentSupplier::factory()->create([
        'name' => 'CV Anugerah Rejeki',
        'contact_number' => '081277665544',
    ]);

    Account::factory()->create([
        'account_number' => '4444555566',
        'customer_id' => $this->customer->id,
        'agent_supplier_id' => $supplier->id,
    ]);
    Account::factory()->create([
        'account_number' => '7777888899',
        'customer_id' => $this->customer->id,
        'agent_supplier_id' => null,
    ]);

    Livewire::actingAs($this->user)
        ->test(AccountCrud::class)
        ->set('search', 'Anugerah')
        ->assertSee('4444555566')
        ->assertDontSee('7777888899')
        ->set('search', '081277665544')
        ->assertSee('4444555566')
        ->assertDontSee('7777888899');
});

it('can search accounts by mobile banking keyword', function () {
    Account::factory()->create([
        'account_number' => '1212121212',
        'customer_id' => $this->customer->id,
        'mobile_banking' => "User : nasabah_bca\nPassword : rahasia123\nPIN : 654321",
    ]);
    Account::factory()->create([
        'account_number' => '3434343434',
        'customer_id' => $this->customer->id,
        'mobile_banking' => "User : nasabah_mandiri\nPIN : 123456",
    ]);

    Livewire::actingAs($this->user)
        ->test(AccountCrud::class)
        ->set('search', 'nasabah_bca')
        ->assertSee('1212121212')
        ->assertDontSee('3434343434');
});

it('does not search mobile banking with a numeric keyword', function () {
    Account::factory()->create([
        'account_number' => '1212121212',
        'customer_id' => $this->customer->id,
        'mobile_banking' => "User : nasabah_bca\nPassword : rahasia123\nPIN : 654321",
    ]);
    Account::factory()->create([
        'account_number' => '3434343434',
        'customer_id' => $this->customer->id,
        'mobile_banking' => "User : nasabah_mandiri\nPIN : 123456",
    ]);

    Livewire::actingAs($this->user)
        ->test(AccountCrud::class)
        ->set('search', '654321')
        ->assertDontSee('1212121212')
        ->assertDontSee('3434343434');
});

it('can search accounts by note', function () {
    Account::factory()->create([
        'account_number' => '3333444455',
        'bank_name' => 'BCA',
        'note' => 'rekening khusus payroll',
        'customer_id' => $this->customer->id,
    ]);
    Account::factory()->create([
        'account_number' => '6666777788',
        'bank_name' => 'MANDIRI',
        'note' => 'rekening operasional harian',
        'customer_id' => $this->customer->id,
    ]);

    Livewire::actingAs($this->user)
        ->test(AccountCrud::class)
        ->set('search', 'payroll')
        ->assertSee('3333444455')
        ->assertDontSee('6666777788');
});

it('can filter accounts by status', function () {
    Account::factory()->create([
        'account_number' => '1111111111',
        'status' => 'aktif',
        'customer_id' => $this->customer->id,
    ]);
    Account::factory()->create([
        'account_number' => '2222222222',
        'status' => 'bermasalah',
        'customer_id' => $this->customer->id,
    ]);

    Livewire::actingAs($this->user)
        ->test(AccountCrud::class)
        ->set('filterStatus', 'aktif')
        ->assertSee('1111111111')
        ->assertDontSee('2222222222');
});

it('can delete an account', function () {
    $account = Account::factory()->create(['customer_id' => $this->customer->id]);

    Livewire::actingAs($this->user)
        ->test(AccountCrud::class)
        ->call('confirmDelete', $account->id)
        ->call('delete')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('accounts', ['id' => $account->id]);
});

it('can upload cover book image', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('cover.jpg');

    Livewire::actingAs($this->user)
        ->test(AccountCrud::class)
        ->call('openModal')
        ->set('customer_id', $this->customer->id)
        ->set('agent_id', $this->agent->id)
        ->set('bank_name', 'BCA')
        ->set('account_number', '1231231234')
        ->set('status', 'aktif')
        ->set('cover_buku', $file)
        ->call('save')
        ->assertHasNoErrors();

    $account = Account::where('account_number', '1231231234')->first();

    expect($account->cover_buku)->not->toBeNull();
    Storage::disk('public')->assertExists($account->cover_buku);
});

it('validates cover book image', function () {
    // Storage::fake('public');

    $file = UploadedFile::fake()->create('document.pdf', 100);

    Livewire::actingAs($this->user)
        ->test(AccountCrud::class)
        ->call('openModal')
        ->set('cover_buku', $file)
        ->call('save')
        ->assertHasErrors(['cover_buku']);
});
