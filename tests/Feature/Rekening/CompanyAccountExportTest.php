<?php

use App\Livewire\Rekening\CompanyAccountCrud;
use App\Models\Agent;
use App\Models\AgentSupplier;
use App\Models\CompanyAccount;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;

uses(RefreshDatabase::class);

/**
 * Read the text layer of a dompdf document (the streams are Flate-compressed).
 */
function companyAccountPdfText($response): string
{
    preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $response->streamedContent(), $matches);

    return collect($matches[1])
        ->map(fn (string $chunk) => gzuncompress($chunk) ?: $chunk)
        ->implode("\n");
}

/**
 * @return array<int, array<int, mixed>>
 */
function companyAccountXlsxRows($response): array
{
    $path = $response->baseResponse->getFile()->getPathname();

    return IOFactory::load($path)->getActiveSheet()->toArray();
}

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->customer = Customer::factory()->create(['full_name' => 'Budi Santoso']);
});

it('downloads the company accounts pdf filtered by the search box', function () {
    CompanyAccount::factory()->create([
        'company_name' => 'PT Sinar Jaya',
        'account_number' => '1111222233',
        'customer_id' => $this->customer->id,
    ]);
    CompanyAccount::factory()->create([
        'company_name' => 'PT Bukan Target',
        'account_number' => '4444555566',
        'customer_id' => $this->customer->id,
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('rekening.company-accounts.export.pdf', ['search' => 'Sinar']))
        ->assertOk()
        ->assertDownload('company-accounts.pdf');

    expect($response->headers->get('content-type'))->toContain('application/pdf');

    $text = companyAccountPdfText($response);

    expect($text)->toContain('PT Sinar Jaya')
        ->toContain('1111222233')
        ->not->toContain('PT Bukan Target');
});

it('exports only the rows matching the status, user and agent supplier filters', function () {
    $agent = Agent::factory()->create();
    $supplier = AgentSupplier::factory()->create();

    CompanyAccount::factory()->create([
        'company_name' => 'PT Bermasalah',
        'status' => 'bermasalah',
        'agent_id' => $agent->id,
        'agent_supplier_id' => $supplier->id,
        'customer_id' => $this->customer->id,
    ]);
    CompanyAccount::factory()->create([
        'company_name' => 'PT Sehat',
        'status' => 'aktif',
        'customer_id' => $this->customer->id,
    ]);

    $statusText = companyAccountPdfText($this->actingAs($this->user)
        ->get(route('rekening.company-accounts.export.pdf', ['status' => 'bermasalah']))
        ->assertOk());

    expect($statusText)->toContain('PT Bermasalah')->not->toContain('PT Sehat');

    $agentText = companyAccountPdfText($this->actingAs($this->user)
        ->get(route('rekening.company-accounts.export.pdf', ['agent' => $agent->id]))
        ->assertOk());

    expect($agentText)->toContain('PT Bermasalah')->not->toContain('PT Sehat');

    $supplierText = companyAccountPdfText($this->actingAs($this->user)
        ->get(route('rekening.company-accounts.export.pdf', ['agent_supplier' => $supplier->id]))
        ->assertOk());

    expect($supplierText)->toContain('PT Bermasalah')->not->toContain('PT Sehat');
});

it('rejects an unknown status filter', function () {
    $this->actingAs($this->user)
        ->get(route('rekening.company-accounts.export.pdf', ['status' => 'unknown']))
        ->assertNotFound();
});

it('downloads the company accounts xlsx filtered by the search box', function () {
    CompanyAccount::factory()->create(['company_name' => 'PT Sinar Jaya', 'customer_id' => $this->customer->id]);
    CompanyAccount::factory()->create(['company_name' => 'PT Bukan Target', 'customer_id' => $this->customer->id]);

    $response = $this->actingAs($this->user)
        ->get(route('rekening.company-accounts.export.xlsx', ['search' => 'Sinar']))
        ->assertOk()
        ->assertDownload('company-accounts.xlsx');

    $rows = companyAccountXlsxRows($response);

    expect($rows[0])->toContain('Company Name')
        ->and(collect($rows)->pluck(0))->toContain('PT Sinar Jaya')
        ->not->toContain('PT Bukan Target');
});

it('requires authentication to export company accounts', function (string $format) {
    $this->get(route('rekening.company-accounts.export.'.$format))->assertRedirect('/login');
})->with(['pdf', 'xlsx']);

it('links both export buttons to their routes with the active filters', function () {
    $agent = Agent::factory()->create();

    Livewire::actingAs($this->user)
        ->test(CompanyAccountCrud::class)
        ->set('search', 'sinar')
        ->set('filterStatus', 'aktif')
        ->set('filterAgent', $agent->id)
        ->assertSee('export/pdf', false)
        ->assertSee('export/xlsx', false)
        ->assertSee('search=sinar', false)
        ->assertSee('status=aktif', false)
        ->assertSee('agent='.$agent->id, false);
});
