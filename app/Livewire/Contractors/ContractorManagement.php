<?php

namespace App\Livewire\Contractors;

use App\Models\Contractor;
use Livewire\Component;
use Livewire\WithPagination;

class ContractorManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';
    public $statusFilter = '';
    public $sortBy = 'nazwa';
    public $sortDirection = 'asc';
    public $showCreateModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function openCreateModal()
    {
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }


    public function deleteContractor($contractorId)
    {
        $contractor = Contractor::findOrFail($contractorId);

        // Sprawdź czy kontrahent ma powiązane dostawy
        if ($contractor->deliveries()->count() > 0) {
            session()->flash('error', 'Nie można usunąć kontrahenta, który ma powiązane dostawy.');
            return;
        }

        $contractor->delete();
        session()->flash('success', 'Kontrahent został usunięty.');
    }

    public function toggleStatus($contractorId)
    {
        $contractor = Contractor::findOrFail($contractorId);
        $contractor->update(['aktywny' => !$contractor->aktywny]);

        session()->flash('success', 'Status kontrahenta został zaktualizowany.');
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

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function getContractors()
    {
        $query = Contractor::query();

        // Wyszukiwanie
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nazwa', 'like', '%' . $this->search . '%')
                  ->orWhere('nip', 'like', '%' . $this->search . '%')
                  ->orWhere('adres', 'like', '%' . $this->search . '%')
                  ->orWhere('miasto', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Filtrowanie po typie
        if ($this->typeFilter) {
            $query->byType($this->typeFilter);
        }

        // Filtrowanie po statusie
        if ($this->statusFilter === 'active') {
            $query->active();
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('aktywny', false);
        }

        // Sortowanie
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(15);
    }

    public function getStats()
    {
        return [
            'total' => Contractor::count(),
            'active' => Contractor::active()->count(),
            'clients' => Contractor::clients()->count(),
            'suppliers' => Contractor::suppliers()->count(),
        ];
    }

    public function render()
    {
        return view('livewire.contractors.contractor-management', [
            'contractors' => $this->getContractors(),
            'stats' => $this->getStats(),
        ]);
    }
}
