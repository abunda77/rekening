<?php

use App\Livewire\Rekening\CustomerCrud;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;

uses(RefreshDatabase::class);

/**
 * Read the text layer of a dompdf document (the streams are Flate-compressed).
 */
function customerPdfText($response): string
{
    preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $response->streamedContent(), $matches);

    return collect($matches[1])
        ->map(fn (string $chunk) => gzuncompress($chunk) ?: $chunk)
        ->implode("\n");
}

/**
 * @return array<int, array<int, mixed>>
 */
function customerXlsxRows($response): array
{
    $path = $response->baseResponse->getFile()->getPathname();

    return IOFactory::load($path)->getActiveSheet()->toArray();
}

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('downloads the customers pdf filtered by the search box', function () {
    Customer::factory()->create(['nik' => '3273010101010001', 'full_name' => 'Budi Santoso']);
    Customer::factory()->create(['nik' => '3273010101010002', 'full_name' => 'Siti Aminah']);

    $response = $this->actingAs($this->user)
        ->get(route('rekening.customers.export.pdf', ['search' => 'Budi']))
        ->assertOk()
        ->assertDownload('customers.pdf');

    expect($response->headers->get('content-type'))->toContain('application/pdf');

    $text = customerPdfText($response);

    expect($text)->toContain('Budi Santoso')
        ->not->toContain('Siti Aminah');
});

it('downloads the customers xlsx filtered by the search box', function () {
    Customer::factory()->create(['nik' => '3273010101010001', 'full_name' => 'Budi Santoso']);
    Customer::factory()->create(['nik' => '3273010101010002', 'full_name' => 'Siti Aminah']);

    $response = $this->actingAs($this->user)
        ->get(route('rekening.customers.export.xlsx', ['search' => '3273010101010002']))
        ->assertOk()
        ->assertDownload('customers.xlsx');

    $names = collect(customerXlsxRows($response))->map(fn (array $row) => $row[1]);

    expect($names)->toContain('Siti Aminah')->not->toContain('Budi Santoso');
});

it('requires authentication to export customers', function (string $format) {
    $this->get(route('rekening.customers.export.'.$format))->assertRedirect('/login');
})->with(['pdf', 'xlsx']);

it('links both customer export buttons to their routes with the active search', function () {
    Livewire::actingAs($this->user)
        ->test(CustomerCrud::class)
        ->set('search', 'budi')
        ->assertSee('export/pdf', false)
        ->assertSee('export/xlsx', false)
        ->assertSee('search=budi', false);
});
