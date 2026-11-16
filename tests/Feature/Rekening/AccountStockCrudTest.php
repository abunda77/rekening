<?php

use App\Livewire\Rekening\AccountStockCrud;
use App\Models\AccountStock;
use App\Models\Agent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

function accountStockCsvUpload(string $content, string $name = 'stok-rekening.csv'): UploadedFile
{
    return UploadedFile::fake()->createWithContent($name, $content);
}

it('can access the account stock page when authenticated', function () {
    $this->actingAs($this->user)
        ->get(route('rekening.account-stocks'))
        ->assertOk()
        ->assertSee('Stok Rekening')
        ->assertSeeLivewire(AccountStockCrud::class);
});

it('creates an account stock', function () {
    $agent = Agent::factory()->create();

    Livewire::actingAs($this->user)
        ->test(AccountStockCrud::class)
        ->call('openModal')
        ->set('account_number', '1234567890')
        ->set('bank_name', 'BCA')
        ->set('agent_id', $agent->id)
        ->set('userName', 'agent-satu')
        ->set('is_in_stock', true)
        ->set('is_vip', true)
        ->call('save')
        ->assertHasNoErrors();

    $stock = AccountStock::query()->where('account_number', '1234567890')->firstOrFail();

    expect($stock->bank_name)->toBe('BCA')
        ->and($stock->agent->is($agent))->toBeTrue()
        ->and($stock->user)->toBe('agent-satu')
        ->and($stock->is_in_stock)->toBeTrue()
        ->and($stock->is_vip)->toBeTrue();
});

it('requires account number and bank when saving', function () {
    Livewire::actingAs($this->user)
        ->test(AccountStockCrud::class)
        ->call('openModal')
        ->set('account_number', '')
        ->set('bank_name', '')
        ->call('save')
        ->assertHasErrors(['account_number', 'bank_name']);
});

it('rejects duplicate account numbers', function () {
    AccountStock::factory()->create(['account_number' => '555000111']);

    Livewire::actingAs($this->user)
        ->test(AccountStockCrud::class)
        ->call('openModal')
        ->set('account_number', '555000111')
        ->set('bank_name', 'BRI')
        ->call('save')
        ->assertHasErrors(['account_number']);
});

it('updates an existing account stock', function () {
    $stock = AccountStock::factory()->create([
        'account_number' => '777888999',
        'bank_name' => 'BNI',
        'user' => 'user-lama',
        'is_vip' => false,
    ]);

    Livewire::actingAs($this->user)
        ->test(AccountStockCrud::class)
        ->call('openModal', $stock->id)
        ->assertSet('userName', 'user-lama')
        ->set('userName', 'user-baru')
        ->set('is_vip', true)
        ->call('save')
        ->assertHasNoErrors();

    expect($stock->refresh()->user)->toBe('user-baru')
        ->and($stock->is_vip)->toBeTrue();
});

it('deletes an account stock', function () {
    $stock = AccountStock::factory()->create();

    Livewire::actingAs($this->user)
        ->test(AccountStockCrud::class)
        ->call('confirmDelete', $stock->id)
        ->call('delete');

    expect(AccountStock::query()->find($stock->id))->toBeNull();
});

it('filters account stocks by in stock flag', function () {
    AccountStock::factory()->create(['account_number' => '111', 'is_in_stock' => true]);
    AccountStock::factory()->create(['account_number' => '222', 'is_in_stock' => false]);

    Livewire::actingAs($this->user)
        ->test(AccountStockCrud::class)
        ->set('filterInStock', 'yes')
        ->assertSee('111')
        ->assertDontSee('222');
});

it('bulk imports account stock data from a csv file', function () {
    $agent = Agent::factory()->create(['agent_code' => 'ABCDE', 'agent_name' => 'Agent Kode']);

    $csv = <<<'CSV'
No. Rek,Bank,Agent,In Stock,User,VIP
9012345678,BCA,ABCDE,Yes,user-satu,Yes
9012345679,BRI,Agent Kode,No,user-dua,No
CSV;

    Livewire::actingAs($this->user)
        ->test(AccountStockCrud::class)
        ->call('openBulkUploadModal')
        ->set('bulkFile', accountStockCsvUpload($csv))
        ->call('bulkUpload')
        ->assertHasNoErrors();

    $first = AccountStock::query()->where('account_number', '9012345678')->firstOrFail();
    $second = AccountStock::query()->where('account_number', '9012345679')->firstOrFail();

    expect($first->bank_name)->toBe('BCA')
        ->and($first->agent->is($agent))->toBeTrue()
        ->and($first->is_in_stock)->toBeTrue()
        ->and($first->user)->toBe('user-satu')
        ->and($first->is_vip)->toBeTrue()
        ->and($second->bank_name)->toBe('BRI')
        ->and($second->agent->is($agent))->toBeTrue()
        ->and($second->is_in_stock)->toBeFalse()
        ->and($second->is_vip)->toBeFalse();
});

it('attaches bulk uploaded images to matching account numbers', function () {
    Storage::fake('public');

    $stock = AccountStock::factory()->create(['account_number' => '123123123']);

    Livewire::actingAs($this->user)
        ->test(AccountStockCrud::class)
        ->call('openBulkUploadModal')
        ->set('bulkImages', [UploadedFile::fake()->image('123123123.jpg')])
        ->call('bulkUpload')
        ->assertHasNoErrors();

    $stock->refresh();

    expect($stock->image)->not->toBeNull();
    Storage::disk('public')->assertExists($stock->image);
});
