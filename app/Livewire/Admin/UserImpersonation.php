<?php

namespace App\Livewire\Admin;

use App\Models\B2BClient;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class UserImpersonation extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $businessTypeFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'businessTypeFilter' => ['except' => ''],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function impersonateUser($clientId)
    {
        $client = B2BClient::findOrFail($clientId);

        // Zapisz informacje o oryginalnym użytkowniku
        session()->put('impersonating', [
            'original_user_id' => Auth::id(),
            'original_guard' => 'web',
            'impersonated_user_id' => $client->id,
            'impersonated_guard' => 'b2b',
            'started_at' => now(),
        ]);

        // Zaloguj jako klient B2B
        Auth::guard('b2b')->login($client);

        session()->flash('success', "Przełączono na konto: {$client->company_name}");

        return redirect()->route('b2b.dashboard');
    }

    public function stopImpersonation()
    {
        $impersonation = session()->get('impersonating');

        if ($impersonation) {
            // Wyloguj z konta B2B
            Auth::guard('b2b')->logout();

            // Przywróć oryginalne logowanie
            $originalUser = \App\Models\User::find($impersonation['original_user_id']);
            if ($originalUser) {
                Auth::guard('web')->login($originalUser);
            }

            session()->forget('impersonating');
            session()->flash('success', 'Powrócono do oryginalnego konta administratora.');
        }

        return redirect()->route('admin.impersonate');
    }

    public function getClients()
    {
        $query = B2BClient::query();

        // Wyszukiwanie
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('company_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('nip', 'like', '%' . $this->search . '%')
                  ->orWhere('contact_person', 'like', '%' . $this->search . '%');
            });
        }

        // Filtry
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->businessTypeFilter) {
            $query->where('business_type', $this->businessTypeFilter);
        }

        return $query->orderBy('company_name')->paginate(15);
    }

    public function render()
    {
        return view('livewire.admin.user-impersonation', [
            'clients' => $this->getClients(),
        ]);
    }
}
