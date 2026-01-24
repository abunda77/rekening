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
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'agent_code' => 'The provided credentials do not match our records.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        session()->regenerate();

        return redirect()->intended(route('agent.dashboard'));
    }

    protected function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'agent_code' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey()
    {
        return Str::transliterate(Str::lower($this->agent_code).'|'.request()->ip());
    }

    public function render()
    {
        return view('livewire.agent.auth.login');
    }
}
