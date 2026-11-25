<?php

use App\Livewire\Rekening\AccountCrud;
use App\Models\Account;
use App\Models\Agent;
use App\Models\AgentSupplier;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Process\Process;

uses(RefreshDatabase::class);

/**
 * Read the text layer of a dompdf document. The streams are Flate-compressed, so they
 * are decoded before searching.
 */
function accountsPdfText($response): string
{
    preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $response->streamedContent(), $matches);

    return collect($matches[1])
        ->map(fn (string $chunk) => gzuncompress($chunk) ?: $chunk)
        ->implode("\n");
}

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->customer = Customer::factory()->create([
        'full_name' => 'Budi Santoso',
        'nik' => '3273010101010001',
    ]);
});

it('downloads the accounts pdf from its own route', function () {
    Account::factory()->create([
        'account_number' => '5555444433',
        'bank_name' => 'BANK MANDIRI',
        'customer_id' => $this->customer->id,
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('rekening.accounts.export.pdf'))
        ->assertOk()
        ->assertDownload('accounts.pdf');

    expect($response->headers->get('content-type'))->toContain('application/pdf');
    expect(accountsPdfText($response))->toContain('5555444433')
        ->toContain('BANK MANDIRI')
        ->toContain('Budi Santoso');
});

it('exports only the rows matching the search box', function () {
    Account::factory()->create(['account_number' => '7777000011', 'customer_id' => $this->customer->id]);
    Account::factory()->create(['account_number' => '8888000022', 'customer_id' => $this->customer->id]);

    $response = $this->actingAs($this->user)
        ->get(route('rekening.accounts.export.pdf', ['search' => '7777']))
        ->assertOk();

    expect(accountsPdfText($response))
        ->toContain('7777000011')
        ->not->toContain('8888000022');
});

it('exports only the rows matching the status, user and agent supplier filters', function () {
    $agent = Agent::factory()->create();
    $supplier = AgentSupplier::factory()->create();
    $otherAgent = Agent::factory()->create();

    Account::factory()->create([
        'account_number' => '9000000001',
        'status' => 'bermasalah',
        'agent_id' => $agent->id,
        'agent_supplier_id' => $supplier->id,
        'customer_id' => $this->customer->id,
    ]);
    Account::factory()->create([
        'account_number' => '9000000002',
        'status' => 'aktif',
        'agent_id' => $otherAgent->id,
        'customer_id' => $this->customer->id,
    ]);

    $statusText = accountsPdfText($this->actingAs($this->user)
        ->get(route('rekening.accounts.export.pdf', ['status' => 'bermasalah']))
        ->assertOk());

    expect($statusText)->toContain('9000000001')->not->toContain('9000000002');

    $agentText = accountsPdfText($this->actingAs($this->user)
        ->get(route('rekening.accounts.export.pdf', ['agent' => $otherAgent->id]))
        ->assertOk());

    expect($agentText)->toContain('9000000002')->not->toContain('9000000001');

    $supplierText = accountsPdfText($this->actingAs($this->user)
        ->get(route('rekening.accounts.export.pdf', ['agent_supplier' => $supplier->id]))
        ->assertOk());

    expect($supplierText)->toContain('9000000001')->not->toContain('9000000002');
});

it('rejects an unknown status filter', function () {
    $this->actingAs($this->user)
        ->get(route('rekening.accounts.export.pdf', ['status' => 'unknown']))
        ->assertNotFound();
});

it('links the accounts page pdf button to the export route with the active filters', function () {
    $this->actingAs($this->user)
        ->get(route('rekening.accounts'))
        ->assertOk()
        ->assertSee(route('rekening.accounts.export.pdf'), false);
});

it('follows the filters typed on the accounts page when building the pdf link', function () {
    $agent = Agent::factory()->create();

    Livewire\Livewire::actingAs($this->user)
        ->test(AccountCrud::class)
        ->set('search', 'mandiri')
        ->set('filterStatus', 'aktif')
        ->set('filterAgent', $agent->id)
        ->assertSee('search=mandiri', false)
        ->assertSee('status=aktif', false)
        ->assertSee('agent='.$agent->id, false);
});

it('requires authentication to export the accounts pdf', function () {
    $this->get(route('rekening.accounts.export.pdf'))->assertRedirect('/login');
});

it('renders a large accounts list within a bounded memory budget', function () {
    $process = new Process([
        PHP_BINARY,
        '-d', 'memory_limit=128M',
        '-d', 'max_execution_time=60',
        base_path('tests/Fixtures/accounts_pdf_probe.php'),
        '600',
    ]);

    $process->run();

    expect($process->isSuccessful())
        ->toBeTrue($process->getErrorOutput().$process->getOutput());

    expect($process->getOutput())->toContain('rows=600');
});
