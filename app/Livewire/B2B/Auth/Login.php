<?php

namespace App\Livewire\B2B\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    protected $messages = [
        'email.required' => 'Email jest wymagany.',
        'email.email' => 'Podaj prawidłowy adres email.',
        'password.required' => 'Hasło jest wymagane.',
        'password.min' => 'Hasło musi mieć minimum 6 znaków.',
    ];

    public function mount()
    {
        // Jeśli użytkownik jest już zalogowany, przekieruj go
        if (Auth::guard('b2b')->check()) {
            return redirect()->route('b2b.dashboard');
        }
    }

    public function login()
    {
        $this->validate();

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (Auth::guard('b2b')->attempt($credentials, $this->remember)) {
            $client = Auth::guard('b2b')->user();

            // Sprawdź status konta
            if ($client->status !== 'active') {
                Auth::guard('b2b')->logout();

                $message = match($client->status) {
                    'pending' => 'Twoje konto oczekuje na aktywację. Skontaktuj się z nami.',
                    'suspended' => 'Twoje konto zostało zawieszone. Skontaktuj się z nami.',
                    'inactive' => 'Twoje konto jest nieaktywne. Skontaktuj się z nami.',
                    default => 'Problem z kontem. Skontaktuj się z nami.',
                };

                session()->flash('error', $message);
                return;
            }

            session()->regenerate();
            session()->flash('success', 'Witaj, ' . $client->company_name . '!');

            return redirect()->intended(route('b2b.dashboard'));
        }

        $this->addError('email', 'Nieprawidłowe dane logowania.');
    }

    public function render()
    {
        return view('livewire.b2-b.auth.login')
                   ->layout('components.layouts.b2b-guest');
    }
}
