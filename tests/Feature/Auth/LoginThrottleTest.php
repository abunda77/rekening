<?php

use App\Livewire\Agent\Auth\Login as AgentLogin;
use App\Models\Agent;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

test('web login is locked after five failed attempts', function () {
    $user = User::factory()->create();

    foreach (range(1, 5) as $attempt) {
        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrorsIn('email');
    }

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertStatus(429);

    $this->assertGuest();
});

test('web login is limited per ip even when emails are rotated', function () {
    foreach (range(1, 30) as $attempt) {
        $this->post(route('login.store'), [
            'email' => "spray{$attempt}@example.com",
            'password' => 'wrong-password',
        ]);
    }

    $this->post(route('login.store'), [
        'email' => 'another@example.com',
        'password' => 'wrong-password',
    ])->assertStatus(429);
});

test('web login without an email does not bypass the limiter', function () {
    $this->post(route('login.store'), ['password' => 'wrong-password'])
        ->assertSessionHasErrorsIn('email');
});

test('agent login is locked after five failed attempts', function () {
    $agent = Agent::factory()->create(['agent_code' => 'AGT01', 'password' => 'password123']);

    foreach (range(1, 5) as $attempt) {
        Livewire::test(AgentLogin::class)
            ->set('agent_code', $agent->agent_code)
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors('agent_code');
    }

    Livewire::test(AgentLogin::class)
        ->set('agent_code', $agent->agent_code)
        ->set('password', 'password123')
        ->call('login')
        ->assertHasErrors('agent_code');

    $this->assertGuest('agent');
});

test('agent login is limited per ip even when agent codes are rotated', function () {
    // Seed the per-IP limiter with the same key the component uses.
    foreach (range(1, 30) as $attempt) {
        RateLimiter::hit('agent-login-ip:127.0.0.1', 60);
    }

    Livewire::test(AgentLogin::class)
        ->set('agent_code', 'FRESH1')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors('agent_code');
});
