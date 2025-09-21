<?php

namespace App\Livewire\B2B;

use App\Models\B2BOrder;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderHistory extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        if (!Auth::guard('b2b')->check()) {
            return redirect()->route('b2b.login');
        }

        // Ustaw domyślny zakres dat - ostatnie 3 miesiące
        if (!$this->dateFrom) {
            $this->dateFrom = now()->subMonths(3)->format('Y-m-d');
        }
        if (!$this->dateTo) {
            $this->dateTo = now()->format('Y-m-d');
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
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

    public function reorder($orderId)
    {
        $order = B2BOrder::findOrFail($orderId);

        // Sprawdź czy to zamówienie należy do zalogowanego klienta
        if ($order->b2_b_client_id !== Auth::guard('b2b')->id()) {
            session()->flash('error', 'Nie można ponowić tego zamówienia.');
            return;
        }

        // Wyczyść obecny koszyk i dodaj produkty z zamówienia
        session()->forget('b2b_cart');
        $cart = [];

        foreach ($order->items as $item) {
            $cart[$item->product_id] = [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'unit_price_gross' => $item->unit_price_gross,
                'tax_rate' => $item->tax_rate,
                'discount_percent' => $item->discount_percent,
                'line_total' => $item->line_total,
                'line_total_gross' => $item->line_total_gross,
                'tax_amount' => $item->tax_amount,
            ];
        }

        session()->put('b2b_cart', $cart);
        session()->flash('success', 'Produkty z zamówienia zostały dodane do koszyka.');

        return redirect()->route('b2b.catalog');
    }

    public function exportOrders()
    {
        // TODO: Implementacja eksportu do CSV/Excel
        session()->flash('info', 'Eksport zamówień będzie dostępny wkrótce.');
    }

    public function getOrders()
    {
        $client = Auth::guard('b2b')->user();

        $query = $client->orders()->with(['items.product']);

        // Wyszukiwanie
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', '%' . $this->search . '%')
                  ->orWhere('delivery_address', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_notes', 'like', '%' . $this->search . '%');
            });
        }

        // Filtrowanie po statusie
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Filtrowanie po dacie
        if ($this->dateFrom) {
            $query->whereDate('order_date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('order_date', '<=', $this->dateTo);
        }

        // Sortowanie
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function getOrderStatuses()
    {
        return [
            '' => 'Wszystkie',
            'pending' => 'Oczekujące',
            'confirmed' => 'Potwierdzone',
            'in_production' => 'W produkcji',
            'ready' => 'Gotowe',
            'shipped' => 'Wysłane',
            'delivered' => 'Dostarczone',
            'cancelled' => 'Anulowane',
        ];
    }

    public function getStats()
    {
        $client = Auth::guard('b2b')->user();

        return [
            'total_orders' => $client->orders()->count(),
            'pending_orders' => $client->orders()->whereIn('status', ['pending', 'confirmed'])->count(),
            'delivered_orders' => $client->orders()->where('status', 'delivered')->count(),
            'total_value' => $client->orders()->where('status', '!=', 'cancelled')->sum('total_amount'),
            'this_month_orders' => $client->orders()->whereMonth('created_at', now()->month)->count(),
            'this_month_value' => $client->orders()->whereMonth('created_at', now()->month)->sum('total_amount'),
        ];
    }

    public function render()
    {
        return view('livewire.b2-b.order-history', [
            'orders' => $this->getOrders(),
            'orderStatuses' => $this->getOrderStatuses(),
            'stats' => $this->getStats(),
        ]);
    }
}
