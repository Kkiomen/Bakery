<?php

namespace App\Livewire\B2B;

use App\Models\RecurringOrder;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class RecurringOrders extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $frequencyFilter = '';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $selectedOrder = null;

    // Form data
    public $name;
    public $description;
    public $frequency = 'weekly';
    public $schedule_config = [];
    public $start_date;
    public $end_date;
    public $order_items = [];
    public $delivery_address;
    public $delivery_postal_code;
    public $delivery_city;
    public $delivery_notes;
    public $preferred_delivery_time_from;
    public $preferred_delivery_time_to;
    public $auto_confirm = false;
    public $days_before_notification = 1;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'frequencyFilter' => ['except' => ''],
    ];

    public function mount()
    {
        if (!Auth::guard('b2b')->check()) {
            return redirect()->route('b2b.login');
        }

        $client = Auth::guard('b2b')->user();
        $this->delivery_address = $client->address;
        $this->delivery_postal_code = $client->postal_code;
        $this->delivery_city = $client->city;
        $this->start_date = now()->addDay()->format('Y-m-d');

        // Domyślna konfiguracja tygodniowa
        $this->schedule_config = [
            'interval' => 1,
            'weekdays' => [1], // Poniedziałek
            'delivery_days_ahead' => 1,
        ];
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'frequency' => 'required|in:daily,weekly,monthly,custom',
            'schedule_config' => 'required|array',
            'start_date' => 'required|date|after:today',
            'end_date' => 'nullable|date|after:start_date',
            'order_items' => 'required|array|min:1',
            'delivery_address' => 'required|string|max:255',
            'delivery_postal_code' => 'required|string|max:10',
            'delivery_city' => 'required|string|max:255',
            'delivery_notes' => 'nullable|string',
            'preferred_delivery_time_from' => 'nullable|date_format:H:i',
            'preferred_delivery_time_to' => 'nullable|date_format:H:i|after:preferred_delivery_time_from',
            'auto_confirm' => 'boolean',
            'days_before_notification' => 'required|integer|min:0|max:7',
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

    public function updatedFrequencyFilter()
    {
        $this->resetPage();
    }

    public function updatedFrequency()
    {
        // Resetuj konfigurację przy zmianie częstotliwości
        $this->schedule_config = match($this->frequency) {
            'daily' => ['interval' => 1, 'delivery_days_ahead' => 1],
            'weekly' => ['interval' => 1, 'weekdays' => [1], 'delivery_days_ahead' => 1],
            'monthly' => ['interval' => 1, 'day_of_month' => 1, 'delivery_days_ahead' => 1],
            'custom' => ['dates' => [], 'delivery_days_ahead' => 1],
            default => [],
        };
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

    public function openEditModal($orderId)
    {
        $this->selectedOrder = RecurringOrder::with('client')->findOrFail($orderId);
        $this->loadOrderData();
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedOrder = null;
        $this->resetForm();
    }

    private function loadOrderData()
    {
        if ($this->selectedOrder) {
            $this->name = $this->selectedOrder->name;
            $this->description = $this->selectedOrder->description;
            $this->frequency = $this->selectedOrder->frequency;
            $this->schedule_config = $this->selectedOrder->schedule_config;
            $this->start_date = $this->selectedOrder->start_date->format('Y-m-d');
            $this->end_date = $this->selectedOrder->end_date?->format('Y-m-d');
            $this->order_items = $this->selectedOrder->order_items;
            $this->delivery_address = $this->selectedOrder->delivery_address;
            $this->delivery_postal_code = $this->selectedOrder->delivery_postal_code;
            $this->delivery_city = $this->selectedOrder->delivery_city;
            $this->delivery_notes = $this->selectedOrder->delivery_notes;
            $this->preferred_delivery_time_from = $this->selectedOrder->preferred_delivery_time_from?->format('H:i');
            $this->preferred_delivery_time_to = $this->selectedOrder->preferred_delivery_time_to?->format('H:i');
            $this->auto_confirm = $this->selectedOrder->auto_confirm;
            $this->days_before_notification = $this->selectedOrder->days_before_notification;
        }
    }

    private function resetForm()
    {
        $client = Auth::guard('b2b')->user();

        $this->name = '';
        $this->description = '';
        $this->frequency = 'weekly';
        $this->schedule_config = [
            'interval' => 1,
            'weekdays' => [1],
            'delivery_days_ahead' => 1,
        ];
        $this->start_date = now()->addDay()->format('Y-m-d');
        $this->end_date = '';
        $this->order_items = [];
        $this->delivery_address = $client->address;
        $this->delivery_postal_code = $client->postal_code;
        $this->delivery_city = $client->city;
        $this->delivery_notes = '';
        $this->preferred_delivery_time_from = '';
        $this->preferred_delivery_time_to = '';
        $this->auto_confirm = false;
        $this->days_before_notification = 1;
    }

    public function addProduct($productId)
    {
        $product = Product::findOrFail($productId);
        $client = Auth::guard('b2b')->user();
        $pricing = $client->getPriceForProduct($product, 1);

        if (!$pricing) {
            session()->flash('error', 'Brak dostępnych cen dla tego produktu.');
            return;
        }

        $this->order_items[] = [
            'product_id' => $product->id,
            'product_name' => $product->nazwa,
            'quantity' => 1,
            'unit_price' => $pricing->price_net,
            'unit_price_gross' => $pricing->price_gross,
            'tax_rate' => $pricing->tax_rate ?? 23,
            'discount_percent' => $pricing->discount_percent ?? 0,
        ];
    }

    public function updateQuantity($index, $quantity)
    {
        if ($quantity <= 0) {
            unset($this->order_items[$index]);
            $this->order_items = array_values($this->order_items);
        } else {
            $this->order_items[$index]['quantity'] = $quantity;
        }
    }

    public function removeProduct($index)
    {
        unset($this->order_items[$index]);
        $this->order_items = array_values($this->order_items);
    }

    public function createRecurringOrder()
    {
        $this->validate();

        $client = Auth::guard('b2b')->user();
        $estimatedTotal = collect($this->order_items)->sum(fn($item) =>
            $item['quantity'] * $item['unit_price_gross']
        );

        $recurringOrder = RecurringOrder::create([
            'b2_b_client_id' => $client->id,
            'name' => $this->name,
            'description' => $this->description,
            'frequency' => $this->frequency,
            'schedule_config' => $this->schedule_config,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date ?: null,
            'order_items' => $this->order_items,
            'estimated_total' => $estimatedTotal,
            'delivery_address' => $this->delivery_address,
            'delivery_postal_code' => $this->delivery_postal_code,
            'delivery_city' => $this->delivery_city,
            'delivery_notes' => $this->delivery_notes,
            'preferred_delivery_time_from' => $this->preferred_delivery_time_from ?: null,
            'preferred_delivery_time_to' => $this->preferred_delivery_time_to ?: null,
            'auto_confirm' => $this->auto_confirm,
            'days_before_notification' => $this->days_before_notification,
            'is_active' => true,
        ]);

        // Oblicz następną datę generowania
        $recurringOrder->update([
            'next_generation_at' => $recurringOrder->calculateNextGenerationDate(),
        ]);

        session()->flash('success', 'Zamówienie cykliczne zostało utworzone.');
        $this->closeCreateModal();
    }

    public function updateRecurringOrder()
    {
        $this->validate();

        $estimatedTotal = collect($this->order_items)->sum(fn($item) =>
            $item['quantity'] * $item['unit_price_gross']
        );

        $this->selectedOrder->update([
            'name' => $this->name,
            'description' => $this->description,
            'frequency' => $this->frequency,
            'schedule_config' => $this->schedule_config,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date ?: null,
            'order_items' => $this->order_items,
            'estimated_total' => $estimatedTotal,
            'delivery_address' => $this->delivery_address,
            'delivery_postal_code' => $this->delivery_postal_code,
            'delivery_city' => $this->delivery_city,
            'delivery_notes' => $this->delivery_notes,
            'preferred_delivery_time_from' => $this->preferred_delivery_time_from ?: null,
            'preferred_delivery_time_to' => $this->preferred_delivery_time_to ?: null,
            'auto_confirm' => $this->auto_confirm,
            'days_before_notification' => $this->days_before_notification,
            'next_generation_at' => $this->selectedOrder->calculateNextGenerationDate(),
        ]);

        session()->flash('success', 'Zamówienie cykliczne zostało zaktualizowane.');
        $this->closeEditModal();
    }

    public function toggleStatus($orderId)
    {
        $order = RecurringOrder::findOrFail($orderId);

        if ($order->is_active) {
            $order->pause();
            session()->flash('success', 'Zamówienie cykliczne zostało wstrzymane.');
        } else {
            $order->resume();
            session()->flash('success', 'Zamówienie cykliczne zostało wznowione.');
        }
    }

    public function deleteRecurringOrder($orderId)
    {
        $order = RecurringOrder::findOrFail($orderId);
        $order->delete();
        session()->flash('success', 'Zamówienie cykliczne zostało usunięte.');
    }

    public function generateNow($orderId)
    {
        $order = RecurringOrder::findOrFail($orderId);
        $generatedOrder = $order->generateOrder();

        if ($generatedOrder) {
            session()->flash('success', 'Zamówienie zostało wygenerowane: ' . $generatedOrder->order_number);
            // Przekieruj do szczegółów wygenerowanego zamówienia
            return redirect()->route('b2b.orders.show', $generatedOrder);
        } else {
            session()->flash('error', 'Nie udało się wygenerować zamówienia.');
        }
    }

    public function getRecurringOrders()
    {
        $client = Auth::guard('b2b')->user();
        $query = $client->recurringOrders()->with(['generatedOrders']);

        // Wyszukiwanie
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Filtry
        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        if ($this->frequencyFilter) {
            $query->where('frequency', $this->frequencyFilter);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getAvailableProducts()
    {
        return Product::with(['category'])
                     ->orderBy('nazwa')
                     ->limit(20)
                     ->get();
    }

    public function render()
    {
        return view('livewire.b2-b.recurring-orders', [
            'recurringOrders' => $this->getRecurringOrders(),
            'availableProducts' => $this->getAvailableProducts(),
        ]);
    }
}
