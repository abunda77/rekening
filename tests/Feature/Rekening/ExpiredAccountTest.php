<?php

use App\Livewire\Rekening\ExpiredAccount;
use App\Models\Account;
use App\Models\CompanyAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('shows the expired accounts page when authenticated', function () {
    $this->actingAs($this->user)
        ->get(route('rekening.expired-accounts'))
        ->assertOk()
        ->assertSee('Rekening Expired')
        ->assertSeeLivewire(ExpiredAccount::class);
});

it('lists bank and company accounts expiring this month by default', function () {
    $bankThisMonth = Account::factory()->create([
        'account_number' => '1111000011',
        'expired_on' => now()->startOfMonth()->addDays(2),
    ]);
    $bankNextMonth = Account::factory()->create([
        'account_number' => '2222000022',
        'expired_on' => now()->startOfMonth()->addMonth()->addDays(2),
    ]);
    $companyThisMonth = CompanyAccount::factory()->create([
        'company_name' => 'PT Bulan Ini',
        'expired_on' => now()->startOfMonth()->addDays(3),
    ]);

    Livewire::actingAs($this->user)
        ->test(ExpiredAccount::class)
        ->assertViewHas('accounts', function ($accounts) use ($bankThisMonth, $bankNextMonth, $companyThisMonth) {
            $ids = $accounts->pluck('id');

            return $ids->contains($bankThisMonth->id)
                && $ids->contains($companyThisMonth->id)
                && ! $ids->contains($bankNextMonth->id);
        })
        ->assertViewHas('totals', fn (array $totals) => $totals['bank_this_month'] === 1
            && $totals['bank_next_month'] === 1
            && $totals['pt_this_month'] === 1
            && $totals['pt_next_month'] === 0);
});

it('filters rows by account type', function () {
    $bank = Account::factory()->create([
        'account_number' => '3333000033',
        'expired_on' => now()->startOfMonth(),
    ]);
    $company = CompanyAccount::factory()->create([
        'company_name' => 'PT Filter Jenis',
        'expired_on' => now()->startOfMonth(),
    ]);

    Livewire::actingAs($this->user)
        ->test(ExpiredAccount::class)
        ->set('filterType', 'bank')
        ->assertViewHas('accounts', fn ($accounts) => $accounts->pluck('id')->contains($bank->id)
            && ! $accounts->pluck('id')->contains($company->id))
        ->assertSee('3333000033')
        ->assertDontSee('PT Filter Jenis');

    Livewire::actingAs($this->user)
        ->test(ExpiredAccount::class)
        ->set('filterType', 'pt')
        ->assertViewHas('accounts', fn ($accounts) => $accounts->pluck('id')->contains($company->id)
            && ! $accounts->pluck('id')->contains($bank->id))
        ->assertSee('PT Filter Jenis')
        ->assertDontSee('3333000033');
});

it('switches between this month and next month', function () {
    $thisMonth = Account::factory()->create([
        'account_number' => '4444000044',
        'expired_on' => now()->startOfMonth(),
    ]);
    $nextMonth = Account::factory()->create([
        'account_number' => '5555000055',
        'expired_on' => now()->startOfMonth()->addMonth(),
    ]);

    Livewire::actingAs($this->user)
        ->test(ExpiredAccount::class)
        ->assertSee('4444000044')
        ->assertDontSee('5555000055')
        ->set('filterPeriod', 'next-month')
        ->assertSee('5555000055')
        ->assertDontSee('4444000044');
});

it('searches by account number, customer name and company name', function () {
    $match = Account::factory()->create([
        'account_number' => '6666000066',
        'expired_on' => now()->startOfMonth(),
    ]);
    $companyMatch = CompanyAccount::factory()->create([
        'company_name' => 'PT Cahaya Sejahtera',
        'expired_on' => now()->startOfMonth(),
    ]);
    $other = Account::factory()->create([
        'account_number' => '7777000077',
        'expired_on' => now()->startOfMonth(),
    ]);

    Livewire::actingAs($this->user)
        ->test(ExpiredAccount::class)
        ->set('search', '6666')
        ->assertViewHas('accounts', fn ($accounts) => $accounts->pluck('id')->contains($match->id)
            && ! $accounts->pluck('id')->contains($other->id)
            && ! $accounts->pluck('id')->contains($companyMatch->id));

    Livewire::actingAs($this->user)
        ->test(ExpiredAccount::class)
        ->set('search', 'Cahaya')
        ->assertViewHas('accounts', fn ($accounts) => $accounts->pluck('id')->contains($companyMatch->id)
            && ! $accounts->pluck('id')->contains($match->id));
});

it('filters rows by status', function () {
    $active = Account::factory()->create([
        'account_number' => '8888000088',
        'status' => 'aktif',
        'expired_on' => now()->startOfMonth(),
    ]);
    $problem = Account::factory()->create([
        'account_number' => '9999000099',
        'status' => 'bermasalah',
        'expired_on' => now()->startOfMonth(),
    ]);

    Livewire::actingAs($this->user)
        ->test(ExpiredAccount::class)
        ->set('filterStatus', 'bermasalah')
        ->assertViewHas('accounts', fn ($accounts) => $accounts->pluck('id')->contains($problem->id)
            && ! $accounts->pluck('id')->contains($active->id));
});

it('sorts rows by the requested column', function () {
    $later = Account::factory()->create([
        'account_number' => '1010101010',
        'expired_on' => now()->startOfMonth()->addDays(10),
    ]);
    $sooner = Account::factory()->create([
        'account_number' => '2020202020',
        'expired_on' => now()->startOfMonth()->addDays(1),
    ]);

    Livewire::actingAs($this->user)
        ->test(ExpiredAccount::class)
        ->call('sortBy', 'expired_on')
        ->assertSet('sortDirection', 'desc')
        ->assertViewHas('accounts', fn ($accounts) => $accounts->pluck('id')->first() === $later->id);

    Livewire::actingAs($this->user)
        ->test(ExpiredAccount::class)
        ->assertSet('sortDirection', 'asc')
        ->assertViewHas('accounts', fn ($accounts) => $accounts->pluck('id')->first() === $sooner->id);
});

it('exports the filtered rows to pdf', function () {
    $filtered = Account::factory()->create([
        'account_number' => '3131313131',
        'expired_on' => now()->startOfMonth(),
    ]);
    $notFiltered = Account::factory()->create([
        'account_number' => '4141414141',
        'expired_on' => now()->startOfMonth(),
    ]);
    CompanyAccount::factory()->create([
        'company_name' => 'PT Bukan Unicorn',
        'expired_on' => now()->startOfMonth(),
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('rekening.expired-accounts.export', [
            'type' => 'bank',
            'period' => 'this-month',
            'status' => '',
            'search' => '3131',
        ]))
        ->assertOk()
        ->assertDownload('rekening-expired-bank-this-month.pdf');

    expect($response->headers->get('content-type'))->toContain('application/pdf');

    // The text layer of a dompdf document is Flate-compressed, so decode it before searching.
    preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $response->streamedContent(), $matches);

    $text = collect($matches[1])
        ->map(fn (string $chunk) => gzuncompress($chunk) ?: $chunk)
        ->implode("\n");

    expect($text)->toContain('3131313131')
        ->not->toContain('4141414141')
        ->not->toContain('PT Bukan Unicorn');
});

it('rejects an unknown export filter', function () {
    $this->actingAs($this->user)
        ->get(route('rekening.expired-accounts.export', ['type' => 'unknown']))
        ->assertNotFound();
});

it('requires authentication for the page and its export', function () {
    $this->get(route('rekening.expired-accounts'))->assertRedirect('/login');
    $this->get(route('rekening.expired-accounts.export'))->assertRedirect('/login');
});
