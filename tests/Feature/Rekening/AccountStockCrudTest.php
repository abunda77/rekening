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
        ->set('customer_name', 'PT Sentosa Jaya')
        ->set('bank_name', 'BCA')
        ->set('account_type', AccountStock::TYPE_PT)
        ->set('agent_id', $agent->id)
        ->set('userName', 'agent-satu')
        ->set('is_in_stock', true)
        ->set('is_vip', true)
        ->call('save')
        ->assertHasNoErrors();

    $stock = AccountStock::query()->where('account_number', '1234567890')->firstOrFail();

    expect($stock->bank_name)->toBe('BCA')
        ->and($stock->customer_name)->toBe('PT Sentosa Jaya')
        ->and($stock->account_type)->toBe(AccountStock::TYPE_PT)
        ->and($stock->agent->is($agent))->toBeTrue()
        ->and($stock->user)->toBe('agent-satu')
        ->and($stock->is_in_stock)->toBeTrue()
        ->and($stock->is_vip)->toBeTrue();
});

it('saves and reloads the note of an account stock', function () {
    Livewire::actingAs($this->user)
        ->test(AccountStockCrud::class)
        ->call('openModal')
        ->set('account_number', '4444555566')
        ->set('bank_name', 'BCA')
        ->set('note', 'rekening cadangan untuk agen baru')
        ->call('save')
        ->assertHasNoErrors();

    $stock = AccountStock::query()->where('account_number', '4444555566')->firstOrFail();

    expect($stock->note)->toBe('rekening cadangan untuk agen baru');

    Livewire::actingAs($this->user)
        ->test(AccountStockCrud::class)
        ->call('openModal', $stock->id)
        ->assertSet('note', 'rekening cadangan untuk agen baru');
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

it('defaults the account type to personal and reloads it when editing', function () {
    Livewire::actingAs($this->user)
        ->test(AccountStockCrud::class)
        ->call('openModal')
        ->assertSet('account_type', AccountStock::TYPE_PERSONAL)
        ->set('account_number', '666777888')
        ->set('bank_name', 'BTN')
        ->call('save')
        ->assertHasNoErrors();

    $stock = AccountStock::query()->where('account_number', '666777888')->firstOrFail();

    expect($stock->account_type)->toBe(AccountStock::TYPE_PERSONAL);

    $stock->update(['account_type' => AccountStock::TYPE_PT]);

    Livewire::actingAs($this->user)
        ->test(AccountStockCrud::class)
        ->call('openModal', $stock->id)
        ->assertSet('account_type', AccountStock::TYPE_PT);
});

it('rejects an unknown account type', function () {
    Livewire::actingAs($this->user)
        ->test(AccountStockCrud::class)
        ->call('openModal')
        ->set('account_number', '999888777')
        ->set('bank_name', 'BCA')
        ->set('account_type', 'lainnya')
        ->call('save')
        ->assertHasErrors(['account_type']);
});

it('filters account stocks by account type', function () {
    AccountStock::factory()->create(['account_number' => '3111', 'account_type' => AccountStock::TYPE_PERSONAL]);
    AccountStock::factory()->create(['account_number' => '3222', 'account_type' => AccountStock::TYPE_PT]);

    Livewire::actingAs($this->user)
        ->test(AccountStockCrud::class)
        ->set('filterType', AccountStock::TYPE_PT)
        ->assertSee('3222')
        ->assertDontSee('3111');
});

it('shows the customer name column and the swapped user/agent headers', function () {
    AccountStock::factory()->create([
        'account_number' => '4321',
        'customer_name' => 'Budi Santoso',
    ]);

    Livewire::actingAs($this->user)
        ->test(AccountStockCrud::class)
        ->assertSeeInOrder(['Tipe', 'User', 'In Stock', 'Agent'])
        ->assertSee('Nama Nasabah/PT')
        ->assertSee('Budi Santoso');
});

it('searches account stocks by note keyword', function () {
    AccountStock::factory()->create(['account_number' => '5111', 'note' => 'rekening cadangan agen baru']);
    AccountStock::factory()->create(['account_number' => '5222', 'note' => 'rekening utama']);

    Livewire::actingAs($this->user)
        ->test(AccountStockCrud::class)
        ->set('search', 'cadangan')
        ->assertSee('5111')
        ->assertDontSee('5222');
});

it('bulk imports account stock data from a csv file', function () {
    $agent = Agent::factory()->create(['agent_code' => 'ABCDE', 'agent_name' => 'Agent Kode']);

    $csv = <<<'CSV'
No. Rek,Nama Nasabah,Bank,Tipe,Agent,In Stock,User,VIP
9012345678,PT Sentosa Jaya,BCA,PT,ABCDE,Yes,user-satu,Yes
9012345679,Budi Santoso,BRI,Personal,Agent Kode,No,user-dua,No
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
        ->and($first->customer_name)->toBe('PT Sentosa Jaya')
        ->and($first->account_type)->toBe(AccountStock::TYPE_PT)
        ->and($first->agent->is($agent))->toBeTrue()
        ->and($first->is_in_stock)->toBeTrue()
        ->and($first->user)->toBe('user-satu')
        ->and($first->is_vip)->toBeTrue()
        ->and($second->bank_name)->toBe('BRI')
        ->and($second->customer_name)->toBe('Budi Santoso')
        ->and($second->account_type)->toBe(AccountStock::TYPE_PERSONAL)
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
