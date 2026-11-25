<?php

use App\Livewire\Rekening\CardCrud;
use App\Models\Account;
use App\Models\Card;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;

uses(RefreshDatabase::class);

/**
 * Read the text layer of a dompdf document (the streams are Flate-compressed).
 */
function cardPdfText($response): string
{
    preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $response->streamedContent(), $matches);

    return collect($matches[1])
        ->map(fn (string $chunk) => gzuncompress($chunk) ?: $chunk)
        ->implode("\n");
}

/**
 * @return array<int, array<int, mixed>>
 */
function cardXlsxRows($response): array
{
    $path = $response->baseResponse->getFile()->getPathname();

    return IOFactory::load($path)->getActiveSheet()->toArray();
}

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->customer = Customer::factory()->create(['full_name' => 'Budi Santoso']);
    $this->account = Account::factory()->create([
        'account_number' => '1234500000',
        'bank_name' => 'BANK MANDIRI',
        'customer_id' => $this->customer->id,
    ]);
});

it('downloads the cards pdf filtered by the search box', function () {
    $matched = Card::factory()->create([
        'card_number' => '6000111122223333',
        'account_id' => $this->account->id,
    ]);
    $other = Card::factory()->create([
        'card_number' => '6000999988887777',
        'account_id' => Account::factory()->create(['customer_id' => $this->customer->id])->id,
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('rekening.cards.export.pdf', ['search' => $matched->card_number]))
        ->assertOk()
        ->assertDownload('cards.pdf');

    expect($response->headers->get('content-type'))->toContain('application/pdf');

    $text = cardPdfText($response);

    expect($text)->toContain($matched->card_number)
        ->toContain('Budi Santoso')
        ->not->toContain($other->card_number);
});

it('downloads the cards xlsx filtered by the search box', function () {
    $matched = Card::factory()->create(['card_number' => '6000111122223333', 'account_id' => $this->account->id]);
    $other = Card::factory()->create(['card_number' => '6000999988887777', 'account_id' => $this->account->id]);

    $response = $this->actingAs($this->user)
        ->get(route('rekening.cards.export.xlsx', ['search' => '111122']))
        ->assertOk()
        ->assertDownload('cards.xlsx');

    $rows = cardXlsxRows($response);

    expect($rows[0])->toContain('Card Number')
        ->and(collect($rows)->pluck(0))->toContain($matched->card_number)
        ->not->toContain($other->card_number);
});

it('requires authentication to export cards', function (string $format) {
    $this->get(route('rekening.cards.export.'.$format))->assertRedirect('/login');
})->with(['pdf', 'xlsx']);

it('links both card export buttons to their routes with the active search', function () {
    Livewire::actingAs($this->user)
        ->test(CardCrud::class)
        ->set('search', 'mandiri')
        ->assertSee('export/pdf', false)
        ->assertSee('export/xlsx', false)
        ->assertSee('search=mandiri', false);
});
