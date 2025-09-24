<?php

namespace App\Livewire\Production;

use App\Models\ProductionOrder;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class ProductionOrdersList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $priorityFilter = '';
    public $typeFilter = '';
    public $userFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $sortBy = 'data_produkcji';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'userFilter' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'sortBy' => ['except' => 'data_produkcji'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount()
    {
        // Domyślnie pokaż zlecenia na dziś i przyszłość
        if (empty($this->dateFrom)) {
            $this->dateFrom = now()->toDateString();
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingUserFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
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

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->priorityFilter = '';
        $this->typeFilter = '';
        $this->userFilter = '';
        $this->dateFrom = now()->toDateString();
        $this->dateTo = '';
        $this->sortBy = 'data_produkcji';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    public function duplicateOrder($orderId)
    {
        $order = ProductionOrder::findOrFail($orderId);

        // Duplikuj zlecenie z nową datą (następny dzień)
        $newOrder = $order->duplicate([
            'data_produkcji' => $order->data_produkcji->copy()->addDay(),
            'nazwa' => $order->nazwa . ' (kopia)',
        ]);

        session()->flash('success', 'Zlecenie zostało zduplikowane pomyślnie.');

        return redirect()->route('production.orders.edit', $newOrder);
    }

    public function changeStatus($orderId, $status)
    {
        $order = ProductionOrder::findOrFail($orderId);

        switch ($status) {
            case 'w_produkcji':
                if ($order->canBeStarted()) {
                    $order->startProduction();
                    session()->flash('success', 'Produkcja została rozpoczęta.');
                }
                break;
            case 'zakonczone':
                if ($order->canBeCompleted()) {
                    $order->completeProduction();
                    session()->flash('success', 'Zlecenie zostało zakończone.');
                }
                break;
            case 'anulowane':
                if ($order->canBeCancelled()) {
                    $order->cancelOrder();
                    session()->flash('success', 'Zlecenie zostało anulowane.');
                }
                break;
        }
    }

    public function deleteOrder($orderId)
    {
        $order = ProductionOrder::findOrFail($orderId);

        if ($order->canBeCancelled()) {
            $order->delete();
            session()->flash('success', 'Zlecenie zostało usunięte.');
        } else {
            session()->flash('error', 'Nie można usunąć zlecenia w tym statusie.');
        }
    }

    public function render()
    {
        $query = ProductionOrder::with(['user', 'items.product', 'b2bOrder.client'])
            ->when($this->search, function ($q) {
                $q->search($this->search);
            })
            ->when($this->statusFilter, function ($q) {
                $q->byStatus($this->statusFilter);
            })
            ->when($this->priorityFilter, function ($q) {
                $q->byPriority($this->priorityFilter);
            })
            ->when($this->typeFilter, function ($q) {
                $q->byType($this->typeFilter);
            })
            ->when($this->userFilter, function ($q) {
                $q->byUser($this->userFilter);
            })
            ->when($this->dateFrom, function ($q) {
                $q->where('data_produkcji', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($q) {
                $q->where('data_produkcji', '<=', $this->dateTo);
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        $orders = $query->paginate(15);

        $users = User::orderBy('name')->get();

        $statusOptions = [
            'oczekujace' => 'Oczekujące',
            'w_produkcji' => 'W produkcji',
            'zakonczone' => 'Zakończone',
            'anulowane' => 'Anulowane',
        ];

        $priorityOptions = [
            'niski' => 'Niski',
            'normalny' => 'Normalny',
            'wysoki' => 'Wysoki',
            'pilny' => 'Pilny',
        ];

        $typeOptions = [
            'wewnetrzne' => 'Wewnętrzne',
            'sklep' => 'Sklep',
            'b2b' => 'B2B',
            'hotel' => 'Hotel',
            'inne' => 'Inne',
        ];

        return view('livewire.production.production-orders-list', [
            'orders' => $orders,
            'users' => $users,
            'statusOptions' => $statusOptions,
            'priorityOptions' => $priorityOptions,
            'typeOptions' => $typeOptions,
        ]);
    }
}
