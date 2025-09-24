<?php

namespace App\Livewire\B2B;

use App\Models\B2BOrder;
use App\Models\B2BOrderItem;
use App\Models\ProductionOrder;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreateOrder extends Component
{
    public $cart = [];
    public $deliveryDate = '';
    public $deliveryTimeFrom = '';
    public $deliveryTimeTo = '';
    public $deliveryAddress = '';
    public $deliveryPostalCode = '';
    public $deliveryCity = '';
    public $deliveryNotes = '';
    public $customerNotes = '';
    public $paymentMethod = 'transfer';
    public $orderType = 'one_time';
    public $recurringSettings = [];

    public $step = 1; // 1 = przegląd koszyka, 2 = szczegóły dostawy, 3 = podsumowanie

    public function mount()
    {
        if (!Auth::guard('b2b')->check()) {
            return redirect()->route('b2b.login');
        }

        $this->cart = session()->get('b2b_cart', []);

        if (empty($this->cart)) {
            session()->flash('error', 'Koszyk jest pusty. Dodaj produkty przed złożeniem zamówienia.');
            return redirect()->route('b2b.catalog');
        }

        // Ustaw domyślne wartości
        $client = Auth::guard('b2b')->user();
        $this->deliveryAddress = $client->address;
        $this->deliveryPostalCode = $client->postal_code;
        $this->deliveryCity = $client->city;
        $this->deliveryDate = now()->addDay()->format('Y-m-d');
    }

    protected function rules()
    {
        return [
            'deliveryDate' => 'required|date|after:today',
            'deliveryAddress' => 'required|string|max:255',
            'deliveryPostalCode' => 'required|string|max:10',
            'deliveryCity' => 'required|string|max:255',
            'paymentMethod' => 'required|in:transfer,card,cash,credit',
            'orderType' => 'required|in:one_time,recurring,standing',
        ];
    }

    public function nextStep()
    {
        if ($this->step == 1) {
            if (empty($this->cart)) {
                session()->flash('error', 'Koszyk jest pusty.');
                return;
            }
        } elseif ($this->step == 2) {
            $this->validate();
        }

        $this->step++;
    }

    public function previousStep()
    {
        $this->step--;
    }

    public function updateCartQuantity($cartKey, $quantity)
    {
        if ($quantity <= 0) {
            unset($this->cart[$cartKey]);
        } else {
            $this->cart[$cartKey]['quantity'] = $quantity;
            $this->recalculateCartItem($cartKey);
        }

        session()->put('b2b_cart', $this->cart);
    }

    public function removeFromCart($cartKey)
    {
        unset($this->cart[$cartKey]);
        session()->put('b2b_cart', $this->cart);

        if (empty($this->cart)) {
            session()->flash('error', 'Koszyk jest pusty.');
            return redirect()->route('b2b.catalog');
        }
    }

    private function recalculateCartItem($cartKey)
    {
        if (!isset($this->cart[$cartKey])) return;

        $item = &$this->cart[$cartKey];
        $quantity = $item['quantity'];

        // Przelicz ceny
        $item['line_total'] = $quantity * $item['unit_price'];
        $item['line_total_gross'] = $quantity * $item['unit_price_gross'];
        $item['tax_amount'] = $item['line_total_gross'] - $item['line_total'];
    }

    public function placeOrder()
    {
        $this->validate();

        if (empty($this->cart)) {
            session()->flash('error', 'Koszyk jest pusty.');
            return;
        }

        $client = Auth::guard('b2b')->user();

        // Sprawdź limit kredytowy
        $orderTotal = $this->getCartTotalGross();
        if ($client->current_balance + $orderTotal > $client->credit_limit) {
            session()->flash('error', 'Przekroczenie limitu kredytowego. Skontaktuj się z nami.');
            return;
        }

        DB::transaction(function () use ($client, $orderTotal) {
            // Utwórz zamówienie
            $order = B2BOrder::create([
                'order_number' => $this->generateOrderNumber(),
                'b2_b_client_id' => $client->id,
                'order_date' => now()->toDateString(),
                'delivery_date' => $this->deliveryDate,
                'delivery_time_from' => $this->deliveryTimeFrom ?: null,
                'delivery_time_to' => $this->deliveryTimeTo ?: null,
                'status' => 'pending',
                'order_type' => $this->orderType,
                'delivery_address' => $this->deliveryAddress,
                'delivery_postal_code' => $this->deliveryPostalCode,
                'delivery_city' => $this->deliveryCity,
                'delivery_notes' => $this->deliveryNotes,
                'customer_notes' => $this->customerNotes,
                'payment_method' => $this->paymentMethod,
                'payment_due_date' => now()->addDays(14),
                'subtotal' => $this->getCartTotal(),
                'tax_amount' => $this->getCartTotalGross() - $this->getCartTotal(),
                'total_amount' => $orderTotal,
            ]);

            // Dodaj pozycje zamówienia
            foreach ($this->cart as $item) {
                B2BOrderItem::create([
                    'b2_b_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'unit_price_gross' => $item['unit_price_gross'],
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'line_total' => $item['line_total'],
                    'line_total_gross' => $item['line_total_gross'],
                    'tax_rate' => $item['tax_rate'] ?? 23,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                ]);
            }

            // Automatycznie utwórz zlecenie produkcyjne
            $this->createProductionOrder($order);

            // Wyczyść koszyk
            session()->forget('b2b_cart');

            // Przekieruj do potwierdzenia
            session()->flash('success', 'Zamówienie zostało złożone pomyślnie! Numer zamówienia: ' . $order->order_number);
            return redirect()->route('b2b.orders.show', $order);
        });
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'B2B';
        $date = now()->format('Ymd');
        $sequence = str_pad(B2BOrder::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$date}-{$sequence}";
    }

    private function createProductionOrder(B2BOrder $order)
    {
        // Utwórz zlecenie produkcyjne
        $productionOrder = ProductionOrder::create([
            'b2b_order_id' => $order->id,
            'numer_zlecenia' => 'ZL-' . now()->format('Ymd') . '-' . str_pad(ProductionOrder::count() + 1, 4, '0', STR_PAD_LEFT),
            'nazwa' => 'Zamówienie B2B - ' . $order->client->company_name,
            'klient' => $order->client->company_name,
            'data_produkcji' => Carbon::parse($order->delivery_date)->subDay(), // Dzień przed dostawą
            'status' => 'oczekujace',
            'priorytet' => 'normalny',
            'typ_zlecenia' => 'b2b',
            'user_id' => User::first()->id ?? 1, // Pierwszy dostępny użytkownik
            'uwagi' => 'Zlecenie utworzone automatycznie z zamówienia B2B #' . $order->order_number,
        ]);

        // Dodaj pozycje zlecenia produkcyjnego
        foreach ($order->items as $item) {
            $productionOrder->items()->create([
                'product_id' => $item->product_id,
                'ilosc' => $item->quantity,
                'status' => 'oczekujace',
            ]);
        }

        // Zaktualizuj status zamówienia
        $order->update(['status' => 'w_produkcji']);

        return $productionOrder;
    }

    public function getCartTotal()
    {
        return array_sum(array_column($this->cart, 'line_total'));
    }

    public function getCartTotalGross()
    {
        return array_sum(array_column($this->cart, 'line_total_gross'));
    }

    public function getCartTaxAmount()
    {
        return $this->getCartTotalGross() - $this->getCartTotal();
    }

    public function getCartItemsCount()
    {
        return array_sum(array_column($this->cart, 'quantity'));
    }

    public function render()
    {
        return view('livewire.b2-b.create-order');
    }
}
