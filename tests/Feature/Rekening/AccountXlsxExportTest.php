<?php

use App\Livewire\Rekening\AccountCrud;
use App\Models\Account;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;

uses(RefreshDatabase::class);

/**
 * Read the downloaded workbook so the assertions check real cell values.
 *
 * @return array<int, array<int, mixed>>
 */
function accountsXlsxRows($response): array
{
    $path = $response->baseResponse->getFile()->getPathname();

    return IOFactory::load($path)->getActiveSheet()->toArray();
}

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->customer = Customer::factory()->create(['full_name' => 'Budi Santoso']);
});

it('downloads the accounts xlsx from its own route', function () {
    Account::factory()->create([
        'account_number' => '5555444433',
        'bank_name' => 'BANK MANDIRI',
        'customer_id' => $this->customer->id,
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('rekening.accounts.export.xlsx'))
        ->assertOk()
        ->assertDownload('accounts.xlsx');

    $rows = accountsXlsxRows($response);

    expect($rows[0])->toContain('Account Number')
        ->and(collect($rows)->pluck(0))->toContain('5555444433');
});

it('exports only the rows matching the search box and filters', function () {
    Account::factory()->create([
        'account_number' => '7777000011',
        'status' => 'aktif',
        'customer_id' => $this->customer->id,
    ]);
    Account::factory()->create([
        'account_number' => '8888000022',
        'status' => 'bermasalah',
        'customer_id' => $this->customer->id,
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('rekening.accounts.export.xlsx', ['search' => '7777']))
        ->assertOk();

    expect(collect(accountsXlsxRows($response))->pluck(0))
        ->toContain('7777000011')
        ->not->toContain('8888000022');

    $statusResponse = $this->actingAs($this->user)
        ->get(route('rekening.accounts.export.xlsx', ['status' => 'bermasalah']))
        ->assertOk();

    expect(collect(accountsXlsxRows($statusResponse))->pluck(0))
        ->toContain('8888000022')
        ->not->toContain('7777000011');
});

it('rejects an unknown status filter', function () {
    $this->actingAs($this->user)
        ->get(route('rekening.accounts.export.xlsx', ['status' => 'unknown']))
        ->assertNotFound();
});

it('requires authentication to export the accounts xlsx', function () {
    $this->get(route('rekening.accounts.export.xlsx'))->assertRedirect('/login');
});

it('links both export buttons to their routes with the active filters', function () {
    Livewire::actingAs($this->user)
        ->test(AccountCrud::class)
        ->set('search', 'mandiri')
        ->set('filterStatus', 'aktif')
        ->assertSee('export/pdf', false)
        ->assertSee('export/xlsx', false)
        ->assertSee('search=mandiri', false)
        ->assertSee('status=aktif', false);
});
