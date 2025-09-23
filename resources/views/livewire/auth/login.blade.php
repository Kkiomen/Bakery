<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="flex flex-col gap-6">
    <!-- Nagłówek z brandem piekarni -->
    <div class="text-center">
        <div class="mx-auto h-20 w-20 flex items-center justify-center mb-4 bg-gradient-to-br from-amber-100 to-orange-100 rounded-full border-4 border-amber-200">
            <span class="text-4xl">🍞</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Panel Administracyjny</h1>
        <h2 class="text-xl text-amber-700 dark:text-amber-300 font-semibold mb-1">Piekarnia Tradycyjna</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">Zaloguj się do systemu zarządzania piekarnią</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            label="Adres email"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="admin@piekarnia.pl"
            class="border-amber-200 focus:border-amber-400 focus:ring-amber-400"
        />

        <!-- Password -->
        <div class="relative">
            <flux:input
                wire:model="password"
                label="Hasło"
                type="password"
                required
                autocomplete="current-password"
                placeholder="Wprowadź hasło"
                viewable
                class="border-amber-200 focus:border-amber-400 focus:ring-amber-400"
            />

            @if (Route::has('password.request'))
                <flux:link class="absolute end-0 top-0 text-sm text-amber-600 hover:text-amber-700" :href="route('password.request')" wire:navigate>
                    Zapomniałeś hasła?
                </flux:link>
            @endif
        </div>

        <!-- Remember Me -->
        <flux:checkbox wire:model="remember" label="Zapamiętaj mnie" class="text-amber-700" />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 border-none text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200" data-test="login-button">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Zaloguj się do panelu
                </span>
            </flux:button>
        </div>
    </form>

    <!-- Informacje o systemie -->
    <div class="mt-6 p-4 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-lg text-center">
        <div class="flex items-center justify-center gap-2 text-amber-700 font-medium mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            System Zarządzania Piekarnią
        </div>
        <p class="text-xs text-amber-600">Zarządzaj produkcją, zamówieniami, klientami B2B i dostawami</p>
    </div>

    <!-- Konto testowe -->
    @if (app()->environment('local'))
        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <h4 class="text-sm font-medium text-blue-900 mb-2">🧪 Konto testowe administratora:</h4>
            <div class="text-xs text-blue-800 space-y-1">
                <p><strong>Email:</strong> admin@piekarnia.pl</p>
                <p><strong>Hasło:</strong> admin123</p>
            </div>
        </div>
    @endif
</div>
