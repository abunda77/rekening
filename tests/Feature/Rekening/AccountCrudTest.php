<?php

use App\Livewire\Rekening\AccountCrud;
use App\Models\Account;
use App\Models\Agent;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

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
