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
        ->assertSee('Company Accounts Expiring This Month')
        ->assertSee('PT Expiring This Month')
        ->assertDontSee('PT Expiring Next Month');
});
