<?php

namespace App\Livewire\Agent\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.agent')]
#[Title('Agent Login')]
class Login extends Component
{
    /**
     * Maximum failed login attempts allowed per agent code + IP combination.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Maximum failed login attempts allowed per IP address, regardless of agent code.
     *
     * Guards against password spraying that rotates agent codes from one host.
     */
    private const MAX_ATTEMPTS_PER_IP = 30;

    /**
     * Lockout window in seconds once the limit is reached.
     */
    private const DECAY_SECONDS = 60;

    public string $agent_code = '';

    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        $this->validate([
            'agent_code' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $this->ensureIsNotRateLimited();

        if (! Auth::guard('agent')->attempt(['agent_code' => $this->agent_code, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey(), self::DECAY_SECONDS);
            RateLimiter::hit($this->ipThrottleKey(), self::DECAY_SECONDS);

            throw ValidationException::withMessages([
                'agent_code' => 'The provided credentials do not match our records.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        session()->regenerate();

        return redirect()->intended(route('agent.dashboard'));
    }

    protected function ensureIsNotRateLimited(): void
    {
        foreach ([$this->throttleKey() => self::MAX_ATTEMPTS, $this->ipThrottleKey() => self::MAX_ATTEMPTS_PER_IP] as $key => $maxAttempts) {
            if (! RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                continue;
            }

            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'agent_code' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }
    }

    /**
     * Throttle key for the submitted agent code combined with the client IP.
     */
    protected function throttleKey(): string
    {
        return 'agent-login:'.Str::transliterate(Str::lower($this->agent_code).'|'.request()->ip());
    }

    /**
     * Throttle key covering every agent code attempted from the client IP.
     */
    protected function ipThrottleKey(): string
    {
        return 'agent-login-ip:'.request()->ip();
    }

    public function render()
    {
        return view('livewire.agent.auth.login');
    }
}
