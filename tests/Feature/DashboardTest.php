<?php

use App\Models\Account;
use App\Models\Agent;
use App\Models\Card;
use App\Models\CompanyAccount;
use App\Models\Customer;
use App\Models\User;

test('dashboard page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/dashboard');

    $response->assertOk();
});

test('dashboard displays correct statistics', function () {
    $user = User::factory()->create();

    $initialAgents = Agent::count();
    $initialCustomers = Customer::count();
    $initialAccounts = Account::count();
    $initialCompanyAccounts = CompanyAccount::count();
    $initialAtms = Card::count();

    // Create 1 Agent
    $agent = Agent::factory()->create();

    // Create 2 Customers
    $customers = Customer::factory()->count(2)->create();

    // Create 3 Accounts (reusing Agent and Customer to avoid side effects)
    $accounts = Account::factory()->count(3)->create([
        'agent_id' => $agent->id,
        'customer_id' => $customers[0]->id,
    ]);

    // Create 4 Cards (reusing Account to avoid side effects)
    Card::factory()->count(4)->create([
        'account_id' => $accounts[0]->id,
    ]);

    CompanyAccount::factory()->count(2)->create();

    $response = $this
        ->actingAs($user)
        ->get('/dashboard');

    $response->assertOk();
    $response->assertViewHas('totalAgents', $initialAgents + 1);
    $response->assertViewHas('totalCustomers', $initialCustomers + 2);
    $response->assertViewHas('totalAccounts', $initialAccounts + 3);
    $response->assertViewHas('totalCompanyAccounts', $initialCompanyAccounts + 2);
    $response->assertViewHas('totalAtms', $initialAtms + 4);
});

test('dashboard displays expiring accounts', function () {
    $user = User::factory()->create();

    // Create an account expiring this month
    $expiringAccount = Account::factory()->create([
        'expired_on' => now(),
    ]);

    // Create an account expiring next month
    $futureAccount = Account::factory()->create([
        'expired_on' => now()->addMonth(),
    ]);

    $response = $this
        ->actingAs($user)
        ->get('/dashboard');

    $response->assertOk();
    $response->assertViewHas('expiringAccounts', function ($accounts) use ($expiringAccount, $futureAccount) {
        return $accounts->contains($expiringAccount) && ! $accounts->contains($futureAccount);
    });
});

test('dashboard displays company accounts expiring this month', function () {
    $user = User::factory()->create();

    $expiringCompanyAccount = CompanyAccount::factory()->create([
        'company_name' => 'PT Expiring This Month',
        'expired_on' => now()->startOfMonth()->addDays(2),
    ]);
    $futureCompanyAccount = CompanyAccount::factory()->create([
        'company_name' => 'PT Expiring Next Month',
        'expired_on' => now()->addMonth()->startOfMonth(),
    ]);
    CompanyAccount::factory()->create([
        'expired_on' => null,
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertViewHas('companyAccountsExpiringThisMonth', 1)
        ->assertViewHas('expiringCompanyAccounts', function ($accounts) use ($expiringCompanyAccount, $futureCompanyAccount) {
            return $accounts->contains($expiringCompanyAccount) && ! $accounts->contains($futureCompanyAccount);
        })
        ->assertSee('Rekening PT — Expired Bulan Ini')
        ->assertSee('PT Expiring This Month');
});

test('dashboard displays accounts expiring next month', function () {
    $user = User::factory()->create();

    $nextMonthAccount = Account::factory()->create([
        'account_number' => '1111222233',
        'expired_on' => now()->startOfMonth()->addMonth()->addDays(3),
    ]);
    $thisMonthAccount = Account::factory()->create([
        'account_number' => '9999888877',
        'expired_on' => now()->startOfMonth(),
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertViewHas('nextMonthAccounts', function ($accounts) use ($nextMonthAccount, $thisMonthAccount) {
            return $accounts->contains($nextMonthAccount) && ! $accounts->contains($thisMonthAccount);
        })
        ->assertSee('Rekening Bank — Expired Bulan Depan')
        ->assertSee('1111222233');
});

test('dashboard displays company accounts expiring next month', function () {
    $user = User::factory()->create();

    $nextMonthCompanyAccount = CompanyAccount::factory()->create([
        'company_name' => 'PT Expired Bulan Depan',
        'expired_on' => now()->startOfMonth()->addMonth()->addDays(3),
    ]);
    $thisMonthCompanyAccount = CompanyAccount::factory()->create([
        'company_name' => 'PT Expired Bulan Ini Saja',
        'expired_on' => now()->startOfMonth(),
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertViewHas('nextMonthCompanyAccounts', function ($accounts) use ($nextMonthCompanyAccount, $thisMonthCompanyAccount) {
            return $accounts->contains($nextMonthCompanyAccount) && ! $accounts->contains($thisMonthCompanyAccount);
        })
        ->assertSee('Rekening PT — Expired Bulan Depan')
        ->assertSee('PT Expired Bulan Depan');
});

test('dashboard displays account status label in indonesian', function () {
    $user = User::factory()->create();

    Account::factory()->create([
        'status' => 'aktif',
        'expired_on' => now()->startOfMonth(),
    ]);
    Account::factory()->create([
        'status' => 'bermasalah',
        'expired_on' => now()->startOfMonth(),
    ]);
    Account::factory()->create([
        'status' => 'nonaktif',
        'expired_on' => now()->startOfMonth(),
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Aktif')
        ->assertSee('Bermasalah')
        ->assertSee('Non-Aktif');
});

test('dashboard exports each expired table to pdf', function (string $type, string $filename) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get("/dashboard/export/{$type}");

    $response->assertOk()->assertDownload($filename);

    expect($response->headers->get('content-type'))->toContain('application/pdf');
})->with([
    ['bank-this-month', 'rekening-bank-expired-bulan-ini.pdf'],
    ['bank-next-month', 'rekening-bank-expired-bulan-depan.pdf'],
    ['pt-this-month', 'rekening-pt-expired-bulan-ini.pdf'],
    ['pt-next-month', 'rekening-pt-expired-bulan-depan.pdf'],
]);

test('dashboard pdf export matches the selected table and period', function () {
    $user = User::factory()->create();

    // The text layer of a dompdf document is Flate-compressed, so decode it before searching.
    $pdfText = function (string $type) use ($user): string {
        $pdf = $this->actingAs($user)
            ->get("/dashboard/export/{$type}")
            ->assertOk()
            ->streamedContent();

        preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $pdf, $matches);

        return collect($matches[1])
            ->map(fn (string $chunk) => gzuncompress($chunk) ?: $chunk)
            ->implode("\n");
    };

    Account::factory()->create([
        'account_number' => '5555444433',
        'expired_on' => now()->startOfMonth()->addMonth()->addDays(3),
    ]);
    Account::factory()->create([
        'account_number' => '7777666655',
        'expired_on' => now()->startOfMonth(),
    ]);
    CompanyAccount::factory()->create([
        'company_name' => 'PT Uji Export',
        'expired_on' => now()->startOfMonth(),
    ]);

    $bankNextMonth = $pdfText('bank-next-month');

    expect($bankNextMonth)
        ->toContain('5555444433')
        ->not->toContain('7777666655')
        ->not->toContain('Perusahaan');

    $ptThisMonth = $pdfText('pt-this-month');

    expect($ptThisMonth)
        ->toContain('Perusahaan')
        ->toContain('PT Uji Export')
        ->not->toContain('PT Uji Export Tidak Ada');
});

test('dashboard pdf export rejects unknown table type', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard/export/unknown-type')
        ->assertNotFound();
});

test('dashboard pdf export requires authentication', function () {
    $this->get('/dashboard/export/bank-this-month')
        ->assertRedirect('/login');
});
