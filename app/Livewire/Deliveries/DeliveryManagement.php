<?php

namespace App\Livewire\Deliveries;

use App\Models\Delivery;
use App\Models\ProductionOrder;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class DeliveryManagement extends Component
{
    use WithPagination;

    public $selectedDate;
    public $statusFilter = '';
    public $driverFilter = '';
    public $priorityFilter = '';
    public $search = '';
    public $showCreateForm = false;
    public $testMessage = 'Kliknij przycisk';

    // Sortowanie
    public $sortBy = 'kolejnosc_dostawy';
    public $sortDirection = 'asc';

    // Batch operations
    public $selectedDeliveries = [];
    public $selectAll = false;

    protected $queryString = [
        'selectedDate' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'driverFilter' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function render()
    {
        $deliveries = $this->getDeliveries();
        $drivers = User::all();
        $stats = $this->getDeliveryStats();
        $productionOrders = ProductionOrder::byStatus('zakonczone')
            ->whereDoesntHave('deliveries')
            ->orWhereHas('deliveries', function($query) {
                $query->whereIn('status', ['anulowana']);
            })
            ->with('items.product')
            ->get();

        return view('livewire.deliveries.delivery-management', [
            'deliveries' => $deliveries,
            'drivers' => $drivers,
            'stats' => $stats,
            'productionOrders' => $productionOrders,
        ]);
    }

    public function getDeliveries()
    {
        $query = Delivery::with(['productionOrder', 'driver', 'items.product'])
            ->when($this->selectedDate, fn($q) => $q->forDate($this->selectedDate))
            ->when($this->statusFilter, fn($q) => $q->byStatus($this->statusFilter))
            ->when($this->driverFilter, fn($q) => $q->byDriver($this->driverFilter))
            ->when($this->priorityFilter, fn($q) => $q->byPriority($this->priorityFilter))
            ->when($this->search, function($q) {
                $q->where(function($query) {
                    $query->where('numer_dostawy', 'like', "%{$this->search}%")
                          ->orWhere('klient_nazwa', 'like', "%{$this->search}%")
                          ->orWhere('klient_adres', 'like', "%{$this->search}%")
                          ->orWhereHas('productionOrder', function($subQuery) {
                              $subQuery->where('nazwa', 'like', "%{$this->search}%")
                                       ->orWhere('numer_zlecenia', 'like', "%{$this->search}%");
                          });
                });
            });

        // Sortowanie
        if ($this->sortBy === 'kolejnosc_dostawy') {
            $query->orderBy('kolejnosc_dostawy', $this->sortDirection)
                  ->orderBy('godzina_planowana', 'asc');
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        return $query->paginate(20);
    }

    public function getDeliveryStats()
    {
        $baseQuery = Delivery::when($this->selectedDate, fn($q) => $q->forDate($this->selectedDate));

        return [
            'total' => $baseQuery->count(),
            'oczekujace' => $baseQuery->clone()->byStatus('oczekujaca')->count(),
            'przypisane' => $baseQuery->clone()->byStatus('przypisana')->count(),
            'w_drodze' => $baseQuery->clone()->byStatus('w_drodze')->count(),
            'dostarczone' => $baseQuery->clone()->byStatus('dostarczona')->count(),
            'problemy' => $baseQuery->clone()->byStatus('problem')->count(),
        ];
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

    public function updateDeliveryOrder($deliveryId, $newOrder)
    {
        $delivery = Delivery::findOrFail($deliveryId);
        $delivery->update(['kolejnosc_dostawy' => $newOrder]);

        $this->dispatch('delivery-order-updated');
    }

    public function assignDriver($deliveryId, $driverId)
    {
        $delivery = Delivery::findOrFail($deliveryId);

        if ($delivery->canBeAssigned()) {
            $delivery->assignToDriver($driverId);
            $this->dispatch('delivery-assigned', ['message' => 'Dostawa została przypisana kierowcy.']);
        }
    }

    public function unassignDriver($deliveryId)
    {
        $delivery = Delivery::findOrFail($deliveryId);

        if ($delivery->status === 'przypisana') {
            $delivery->update([
                'driver_id' => null,
                'status' => 'oczekujaca'
            ]);
            $this->dispatch('delivery-unassigned', ['message' => 'Kierowca został odłączony od dostawy.']);
        }
    }

    public function cancelDelivery($deliveryId, $reason = null)
    {
        $delivery = Delivery::findOrFail($deliveryId);

        if ($delivery->canBeCancelled()) {
            $delivery->cancelDelivery($reason);
            $this->dispatch('delivery-cancelled', ['message' => 'Dostawa została anulowana.']);
        }
    }

    public function duplicateDelivery($deliveryId)
    {
        $delivery = Delivery::with('items')->findOrFail($deliveryId);

        $newDelivery = $delivery->replicate();
        $newDelivery->numer_dostawy = $newDelivery->generateDeliveryNumber();
        $newDelivery->status = 'oczekujaca';
        $newDelivery->driver_id = null;
        $newDelivery->godzina_rozpoczecia = null;
        $newDelivery->godzina_zakonczenia = null;
        $newDelivery->uwagi_kierowcy = null;
        $newDelivery->save();

        // Duplikuj pozycje
        foreach ($delivery->items as $item) {
            $newItem = $item->replicate();
            $newItem->delivery_id = $newDelivery->id;
            $newItem->ilosc_dostarczona = 0;
            $newItem->status = 'oczekujacy';
            $newItem->save();
        }

        $this->dispatch('delivery-duplicated', ['message' => 'Dostawa została zduplikowana.']);
    }

    public function bulkAssignDriver($driverId)
    {
        if (empty($this->selectedDeliveries)) return;

        Delivery::whereIn('id', $this->selectedDeliveries)
               ->where('status', 'oczekujaca')
               ->each(function($delivery) use ($driverId) {
                   $delivery->assignToDriver($driverId);
               });

        $this->selectedDeliveries = [];
        $this->selectAll = false;
        $this->dispatch('bulk-assigned', ['message' => 'Wybrane dostawy zostały przypisane kierowcy.']);
    }

    public function bulkUpdatePriority($priority)
    {
        if (empty($this->selectedDeliveries)) return;

        Delivery::whereIn('id', $this->selectedDeliveries)
               ->update(['priorytet' => $priority]);

        $this->selectedDeliveries = [];
        $this->selectAll = false;
        $this->dispatch('bulk-priority-updated', ['message' => 'Priorytet wybranych dostaw został zaktualizowany.']);
    }

    public function bulkUpdateDate($date)
    {
        if (empty($this->selectedDeliveries)) return;

        Delivery::whereIn('id', $this->selectedDeliveries)
               ->update(['data_dostawy' => $date]);

        $this->selectedDeliveries = [];
        $this->selectAll = false;
        $this->dispatch('bulk-date-updated', ['message' => 'Data wybranych dostaw została zaktualizowana.']);
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedDeliveries = $this->getDeliveries()->pluck('id')->toArray();
        } else {
            $this->selectedDeliveries = [];
        }
    }

    public function resetFilters()
    {
        $this->reset(['statusFilter', 'driverFilter', 'priorityFilter', 'search']);
        $this->selectedDate = now()->format('Y-m-d');
        $this->resetPage();
    }

    public function exportDeliveries()
    {
        // TODO: Implementacja eksportu do CSV/PDF
        $this->dispatch('export-started', ['message' => 'Eksport dostaw rozpoczęty...']);
    }

    public function optimizeRoutes()
    {
        // TODO: Implementacja optymalizacji tras
        $this->dispatch('routes-optimized', ['message' => 'Trasy zostały zoptymalizowane.']);
    }

    public function openCreateModal()
    {
        $this->showCreateForm = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateForm = false;
    }

    public function updatedSelectedDate()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedDriverFilter()
    {
        $this->resetPage();
    }

    public function updatedPriorityFilter()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
