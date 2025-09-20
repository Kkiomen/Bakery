<?php

namespace App\Livewire\Materials;

use App\Models\Material;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class MaterialIndex extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $typeFilter = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public $stockFilter = '';

    #[Url]
    public $sortBy = 'nazwa';

    #[Url]
    public $sortDirection = 'asc';

    public $selectedMaterials = [];
    public $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'stockFilter' => ['except' => ''],
        'sortBy' => ['except' => 'nazwa'],
        'sortDirection' => ['except' => 'asc'],
    ];

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

    public function updatedStockFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedMaterials = $this->getMaterials()->pluck('id')->toArray();
        } else {
            $this->selectedMaterials = [];
        }
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

    public function clearFilters()
    {
        $this->reset(['search', 'typeFilter', 'statusFilter', 'stockFilter']);
        $this->resetPage();
    }

    public function toggleMaterialStatus($materialId)
    {
        $material = Material::find($materialId);
        if ($material) {
            $material->update(['aktywny' => !$material->aktywny]);
            $this->dispatch('material-updated', 'Status surowca został zmieniony');
        }
    }

    public function addStock($materialId, $quantity)
    {
        $material = Material::find($materialId);
        if ($material && $quantity > 0) {
            $material->addStock($quantity, 'Ręczne dodanie przez użytkownika');
            $this->dispatch('material-updated', 'Stan magazynowy został zaktualizowany');
        }
    }

    public function getMaterials()
    {
        $query = Material::query()
            ->when($this->search, function ($query) {
                $query->search($this->search);
            })
            ->when($this->typeFilter, function ($query) {
                $query->byType($this->typeFilter);
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('aktywny', $this->statusFilter === '1');
            })
            ->when($this->stockFilter === 'low', function ($query) {
                $query->lowStock();
            })
            ->when($this->stockFilter === 'out', function ($query) {
                $query->outOfStock();
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(20);
    }

    public function getStats()
    {
        return [
            'total' => Material::active()->count(),
            'low_stock' => Material::active()->lowStock()->count(),
            'out_of_stock' => Material::active()->outOfStock()->count(),
            'total_value' => Material::active()->get()->sum('wartosc_magazynu'),
        ];
    }

    public function render()
    {
        $materials = $this->getMaterials();
        $stats = $this->getStats();
        $types = Material::getAvailableTypes();

        return view('livewire.materials.material-index', [
            'materials' => $materials,
            'stats' => $stats,
            'types' => $types,
        ]);
    }
}
