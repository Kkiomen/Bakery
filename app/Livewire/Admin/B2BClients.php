<?php

namespace App\Livewire\Admin;

use App\Models\B2BClient;
use App\Models\B2BOrder;
use Livewire\Component;
use Livewire\WithPagination;

class B2BClients extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $businessTypeFilter = '';
    public $pricingTierFilter = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 15;

    public $showCreateModal = false;
    public $showEditModal = false;
    public $selectedClient = null;

    // Client form data
    public $company_name;
    public $nip;
    public $regon;
    public $email;
    public $address;
    public $postal_code;
    public $city;
    public $phone;
    public $website;
    public $contact_person;
    public $contact_phone;
    public $contact_email;
    public $business_type;
    public $business_description;
    public $status = 'active';
    public $pricing_tier = 'standard';
    public $credit_limit = 10000;
    public $current_balance = 0;
    public $notes;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'businessTypeFilter' => ['except' => ''],
        'pricingTierFilter' => ['except' => ''],
    ];

    protected function rules()
    {
        return [
            'company_name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:20|unique:b2_b_clients,nip,' . ($this->selectedClient ? $this->selectedClient->id : 'NULL'),
            'regon' => 'nullable|string|max:14',
            'email' => 'required|email|max:255|unique:b2_b_clients,email,' . ($this->selectedClient ? $this->selectedClient->id : 'NULL'),
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'business_type' => 'required|in:hotel,restaurant,cafe,shop,catering,other',
            'business_description' => 'nullable|string|max:500',
            'status' => 'required|in:pending,active,suspended,inactive',
            'pricing_tier' => 'required|in:standard,bronze,silver,gold,platinum',
            'credit_limit' => 'required|numeric|min:0',
            'current_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedBusinessTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedPricingTierFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function openEditModal($clientId)
    {
        $this->selectedClient = B2BClient::findOrFail($clientId);
        $this->loadClientData();
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedClient = null;
        $this->resetForm();
    }

    private function loadClientData()
    {
        if ($this->selectedClient) {
            $this->company_name = $this->selectedClient->company_name;
            $this->nip = $this->selectedClient->nip;
            $this->regon = $this->selectedClient->regon;
            $this->email = $this->selectedClient->email;
            $this->address = $this->selectedClient->address;
            $this->postal_code = $this->selectedClient->postal_code;
            $this->city = $this->selectedClient->city;
            $this->phone = $this->selectedClient->phone;
            $this->website = $this->selectedClient->website;
            $this->contact_person = $this->selectedClient->contact_person;
            $this->contact_phone = $this->selectedClient->contact_phone;
            $this->contact_email = $this->selectedClient->contact_email;
            $this->business_type = $this->selectedClient->business_type;
            $this->business_description = $this->selectedClient->business_description;
            $this->status = $this->selectedClient->status;
            $this->pricing_tier = $this->selectedClient->pricing_tier;
            $this->credit_limit = $this->selectedClient->credit_limit;
            $this->current_balance = $this->selectedClient->current_balance;
            $this->notes = $this->selectedClient->notes;
        }
    }

    private function resetForm()
    {
        $this->company_name = '';
        $this->nip = '';
        $this->regon = '';
        $this->email = '';
        $this->address = '';
        $this->postal_code = '';
        $this->city = '';
        $this->phone = '';
        $this->website = '';
        $this->contact_person = '';
        $this->contact_phone = '';
        $this->contact_email = '';
        $this->business_type = '';
        $this->business_description = '';
        $this->status = 'active';
        $this->pricing_tier = 'standard';
        $this->credit_limit = 10000;
        $this->current_balance = 0;
        $this->notes = '';
    }

    public function createClient()
    {
        $this->validate();

        B2BClient::create([
            'company_name' => $this->company_name,
            'nip' => $this->nip,
            'regon' => $this->regon,
            'email' => $this->email,
            'password' => \Hash::make('password123'), // Domyślne hasło
            'address' => $this->address,
            'postal_code' => $this->postal_code,
            'city' => $this->city,
            'phone' => $this->phone,
            'website' => $this->website,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'business_type' => $this->business_type,
            'business_description' => $this->business_description,
            'status' => $this->status,
            'pricing_tier' => $this->pricing_tier,
            'credit_limit' => $this->credit_limit,
            'current_balance' => $this->current_balance,
            'notes' => $this->notes,
        ]);

        session()->flash('success', 'Klient B2B został utworzony. Domyślne hasło: password123');
        $this->closeCreateModal();
    }

    public function updateClient()
    {
        $this->validate();

        $this->selectedClient->update([
            'company_name' => $this->company_name,
            'nip' => $this->nip,
            'regon' => $this->regon,
            'email' => $this->email,
            'address' => $this->address,
            'postal_code' => $this->postal_code,
            'city' => $this->city,
            'phone' => $this->phone,
            'website' => $this->website,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'business_type' => $this->business_type,
            'business_description' => $this->business_description,
            'status' => $this->status,
            'pricing_tier' => $this->pricing_tier,
            'credit_limit' => $this->credit_limit,
            'current_balance' => $this->current_balance,
            'notes' => $this->notes,
        ]);

        session()->flash('success', 'Klient B2B został zaktualizowany.');
        $this->closeEditModal();
    }

    public function deleteClient($clientId)
    {
        $client = B2BClient::findOrFail($clientId);

        // Sprawdź czy klient ma zamówienia
        if ($client->orders()->count() > 0) {
            session()->flash('error', 'Nie można usunąć klienta, który ma zamówienia.');
            return;
        }

        $client->delete();
        session()->flash('success', 'Klient B2B został usunięty.');
    }

    public function toggleStatus($clientId)
    {
        $client = B2BClient::findOrFail($clientId);
        $newStatus = $client->status === 'active' ? 'suspended' : 'active';
        $client->update(['status' => $newStatus]);

        session()->flash('success', 'Status klienta został zmieniony.');
    }

    public function resetPassword($clientId)
    {
        $client = B2BClient::findOrFail($clientId);
        $newPassword = 'password123';

        $client->update(['password' => \Hash::make($newPassword)]);

        session()->flash('success', "Hasło zostało zresetowane na: {$newPassword}");
    }

    public function getClients()
    {
        $query = B2BClient::with(['orders']);

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

        if ($this->pricingTierFilter) {
            $query->where('pricing_tier', $this->pricingTierFilter);
        }

        // Sortowanie
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function getStats()
    {
        return [
            'total' => B2BClient::count(),
            'active' => B2BClient::where('status', 'active')->count(),
            'pending' => B2BClient::where('status', 'pending')->count(),
            'suspended' => B2BClient::where('status', 'suspended')->count(),
            'total_orders' => B2BOrder::count(),
            'total_value' => B2BOrder::where('status', '!=', 'cancelled')->sum('total_amount'),
        ];
    }

    public function getBusinessTypes()
    {
        return [
            '' => 'Wszystkie',
            'hotel' => 'Hotel',
            'restaurant' => 'Restauracja',
            'cafe' => 'Kawiarnia',
            'shop' => 'Sklep',
            'catering' => 'Catering',
            'other' => 'Inne',
        ];
    }

    public function getStatuses()
    {
        return [
            '' => 'Wszystkie',
            'pending' => 'Oczekujący',
            'active' => 'Aktywny',
            'suspended' => 'Zawieszony',
            'inactive' => 'Nieaktywny',
        ];
    }

    public function getPricingTiers()
    {
        return [
            '' => 'Wszystkie',
            'standard' => 'Standard',
            'bronze' => 'Brązowy',
            'silver' => 'Srebrny',
            'gold' => 'Złoty',
            'platinum' => 'Platynowy',
        ];
    }

    public function render()
    {
        return view('livewire.admin.b2-b-clients', [
            'clients' => $this->getClients(),
            'stats' => $this->getStats(),
            'businessTypes' => $this->getBusinessTypes(),
            'statuses' => $this->getStatuses(),
            'pricingTiers' => $this->getPricingTiers(),
        ]);
    }
}
