<?php

namespace App\Livewire\B2B;

use App\Models\B2BOrder;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class OrderDetails extends Component
{
    public B2BOrder $order;

    public function mount(B2BOrder $order)
    {
        // Sprawdź czy użytkownik jest zalogowany
        if (!Auth::guard('b2b')->check()) {
            return redirect()->route('b2b.login');
        }

        // Sprawdź czy zamówienie należy do zalogowanego klienta
        if ($order->b2_b_client_id !== Auth::guard('b2b')->id()) {
            abort(403, 'Nie masz uprawnień do przeglądania tego zamówienia.');
        }

        $this->order = $order->load(['items.product', 'client']);
    }

    public function reorderItems()
    {
        // Wyczyść obecny koszyk i dodaj produkty z zamówienia
        session()->forget('b2b_cart');
        $cart = [];

        foreach ($this->order->items as $item) {
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

    public function downloadInvoice()
    {
        // TODO: Implementacja generowania faktury PDF
        session()->flash('info', 'Generowanie faktur będzie dostępne wkrótce.');
    }

    public function printOrder()
    {
        // JavaScript print funkcja
        $this->dispatch('print-order');
    }

    public function cancelOrder()
    {
        if ($this->order->status !== 'pending') {
            session()->flash('error', 'Można anulować tylko zamówienia o statusie "Oczekujące".');
            return;
        }

        $this->order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => 'Anulowane przez klienta',
        ]);

        session()->flash('success', 'Zamówienie zostało anulowane.');

        return redirect()->route('b2b.orders.index');
    }

    public function render()
    {
        return view('livewire.b2-b.order-details');
    }
}
