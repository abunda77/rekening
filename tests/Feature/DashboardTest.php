<?php

use App\Models\Account;
use App\Models\Agent;
use App\Models\Card;
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

    $response = $this
        ->actingAs($user)
        ->get('/dashboard');

    $response->assertOk();
    $response->assertViewHas('totalAgents', $initialAgents + 1);
    $response->assertViewHas('totalCustomers', $initialCustomers + 2);
    $response->assertViewHas('totalAccounts', $initialAccounts + 3);
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
        return $accounts->contains($expiringAccount) && !$accounts->contains($futureAccount);
    });
});
