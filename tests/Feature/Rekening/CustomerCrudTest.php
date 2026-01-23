<?php

use App\Livewire\Rekening\CustomerCrud;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can access customers page when authenticated', function () {
    $this->actingAs($this->user)
        ->get(route('rekening.customers'))
        ->assertOk()
        ->assertSeeLivewire(CustomerCrud::class);
});

it('can search customers', function () {
    Customer::factory()->create(['full_name' => 'John Doe', 'nik' => '1234567890123456']);
    Customer::factory()->create(['full_name' => 'Jane Smith', 'nik' => '6543210987654321']);

    Livewire::actingAs($this->user)
        ->test(CustomerCrud::class)
        ->set('search', 'John')
        ->assertSee('John Doe')
        ->assertDontSee('Jane Smith');
});

it('can create a new customer', function () {
    Livewire::actingAs($this->user)
        ->test(CustomerCrud::class)
        ->call('openModal')
        ->set('nik', '1234567890123456')
        ->set('full_name', 'New Customer')
        ->set('mother_name', 'Mother Name')
        ->set('email', 'customer@example.com')
        ->set('phone_number', '081234567890')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('customers', [
        'nik' => '1234567890123456',
        'full_name' => 'New Customer',
    ]);
});

it('can edit an existing customer', function () {
    $customer = Customer::factory()->create(['full_name' => 'Old Name']);

    Livewire::actingAs($this->user)
        ->test(CustomerCrud::class)
        ->call('openModal', $customer->id)
        ->set('full_name', 'Updated Name')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'full_name' => 'Updated Name',
    ]);
});

it('can delete a customer', function () {
    $customer = Customer::factory()->create();

    Livewire::actingAs($this->user)
        ->test(CustomerCrud::class)
        ->call('confirmDelete', $customer->id)
        ->call('delete')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
});

it('validates NIK must be 16 digits', function () {
    Livewire::actingAs($this->user)
        ->test(CustomerCrud::class)
        ->call('openModal')
        ->set('nik', '123')
        ->set('full_name', 'Test')
        ->call('save')
        ->assertHasErrors(['nik']);
});

it('can view customer details', function () {
    $customer = Customer::factory()->create();

    Livewire::actingAs($this->user)
        ->test(CustomerCrud::class)
        ->call('view', $customer->id)
        ->assertSet('showViewModal', true)
        ->assertSet('viewingCustomer.id', $customer->id)
        ->assertSee($customer->full_name);
});

it('can upload ktp image', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('ktp.jpg');

    Livewire::actingAs($this->user)
        ->test(CustomerCrud::class)
        ->call('openModal')
        ->set('nik', '8888888888888888')
        ->set('full_name', 'Image Test')
        ->set('upload_ktp', $file)
        ->call('save')
        ->assertHasNoErrors();

    // Assert file exists
    $customer = Customer::where('nik', '8888888888888888')->first();
    expect($customer->upload_ktp)->not->toBeNull();
    Storage::disk('public')->assertExists($customer->upload_ktp);
});

it('validates upload_ktp must be an image', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->create('document.pdf', 100);

    Livewire::actingAs($this->user)
        ->test(CustomerCrud::class)
        ->call('openModal')
        ->set('nik', '9999999999999999')
        ->set('full_name', 'Invalid File Test')
        ->set('upload_ktp', $file)
        ->call('save')
        ->assertHasErrors(['upload_ktp']);
});
