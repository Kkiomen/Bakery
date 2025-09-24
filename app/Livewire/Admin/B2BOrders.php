<?php

namespace App\Livewire\Admin;

use App\Models\B2BOrder;
use App\Models\B2BClient;
use App\Models\ProductionOrder;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class B2BOrders extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $clientFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 15;

    public $showDetailsModal = false;
    public $selectedOrder = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'clientFilter' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedClientFilter()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
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

    public function viewOrderDetails($orderId)
    {
        $this->selectedOrder = B2BOrder::with(['client', 'items.product', 'productionOrders'])
            ->findOrFail($orderId);
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedOrder = null;
    }

    public function goToProductionOrder($productionOrderId)
    {
        return redirect()->route('production.orders.show', $productionOrderId);
    }

    public function createProductionOrder($orderId)
    {
        $order = B2BOrder::with(['client', 'items.product'])->findOrFail($orderId);

        // Create production order
        $productionOrder = ProductionOrder::create([
            'b2b_order_id' => $order->id,
            'numer_zlecenia' => 'ZL-' . now()->format('Ymd') . '-' . str_pad(ProductionOrder::count() + 1, 4, '0', STR_PAD_LEFT),
            'nazwa' => 'Zamówienie B2B - ' . $order->client->company_name,
            'klient' => $order->client->company_name,
            'data_produkcji' => Carbon::parse($order->delivery_date)->subDay(), // Day before delivery
            'status' => 'oczekujace',
            'priorytet' => 'normalny',
            'typ_zlecenia' => 'b2b',
            'user_id' => User::first()->id ?? 1, // First available user
            'uwagi' => 'Zlecenie utworzone automatycznie z zamówienia B2B #' . $order->order_number,
        ]);

        // Create production order items
        foreach ($order->items as $item) {
            $productionOrder->items()->create([
                'product_id' => $item->product_id,
                'ilosc' => $item->ilosc,
                'status' => 'oczekujace',
            ]);
        }

        // Update order status
        $order->update(['status' => 'w_produkcji']);

        session()->flash('success', 'Zlecenie produkcyjne zostało utworzone: ' . $productionOrder->numer_zlecenia);
        $this->closeDetailsModal();
    }

    public function impersonateClient($clientId)
    {
        $client = B2BClient::findOrFail($clientId);
        session()->put('impersonated_user', $client->id);
        session()->put('impersonated_user_type', 'b2b_client');

        return redirect()->route('b2b.dashboard');
    }

    private function getOrders()
    {
        $query = B2BOrder::with(['client', 'items', 'productionOrders'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('order_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('client', function ($clientQuery) {
                          $clientQuery->where('company_name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->clientFilter, function ($query) {
                $query->where('client_id', $this->clientFilter);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    private function getStats()
    {
        $totalOrders = B2BOrder::count();
        $pendingOrders = B2BOrder::where('status', 'oczekujace')->count();
        $inProductionOrders = B2BOrder::where('status', 'w_produkcji')->count();
        $completedOrders = B2BOrder::where('status', 'zrealizowane')->count();
        $totalValue = B2BOrder::sum('wartosc_brutto');

        return [
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'in_production_orders' => $inProductionOrders,
            'completed_orders' => $completedOrders,
            'total_value' => $totalValue,
        ];
    }

    public function getClients()
    {
        return B2BClient::orderBy('company_name')->get();
    }

    public function getStatuses()
    {
        return [
            '' => 'Wszystkie',
            'oczekujace' => 'Oczekujące',
            'potwierdzone' => 'Potwierdzone',
            'w_produkcji' => 'W produkcji',
            'gotowe_do_dostawy' => 'Gotowe do dostawy',
            'w_dostawie' => 'W dostawie',
            'zrealizowane' => 'Zrealizowane',
            'anulowane' => 'Anulowane',
        ];
    }

    public function render()
    {
        return view('livewire.admin.b2-b-orders', [
            'orders' => $this->getOrders(),
            'stats' => $this->getStats(),
            'clients' => $this->getClients(),
            'statuses' => $this->getStatuses(),
        ]);
    }
}
