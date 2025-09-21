<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderItem;

class DebugProductionOrders extends Command
{
    protected $signature = 'debug:production-orders {date?}';
    protected $description = 'Debug production orders for a specific date';

    public function handle()
    {
        // Najpierw pokaż statystyki
        $totalOrders = ProductionOrder::count();
        $totalItems = ProductionOrderItem::count();
        $this->info("Statystyki bazy danych:");
        $this->line("- Łączna liczba zleceń: {$totalOrders}");
        $this->line("- Łączna liczba pozycji: {$totalItems}");
        $this->newLine();

        // Pokaż wszystkie dostępne daty
        $this->info("Wszystkie dostępne daty produkcji:");
        $availableDates = ProductionOrder::select('data_produkcji')
            ->distinct()
            ->orderBy('data_produkcji')
            ->get()
            ->pluck('data_produkcji')
            ->map(fn($date) => $date->format('Y-m-d'));

        foreach ($availableDates as $availableDate) {
            $ordersCount = ProductionOrder::where('data_produkcji', $availableDate)->count();
            $this->line("- {$availableDate} ({$ordersCount} zleceń)");
        }
        $this->newLine();

        // Sprawdź pierwsze zlecenie
        $firstOrder = ProductionOrder::first();
        if ($firstOrder) {
            $this->info("Pierwsze zlecenie w bazie:");
            $this->line("- ID: {$firstOrder->id}");
            $this->line("- Nazwa: {$firstOrder->nazwa}");
            $this->line("- Data produkcji (raw): " . $firstOrder->getRawOriginal('data_produkcji'));
            $this->line("- Data produkcji (formatted): " . $firstOrder->data_produkcji);
            $this->line("- Data produkcji (toDateString): " . $firstOrder->data_produkcji->toDateString());
        }
        $this->newLine();

        $date = $this->argument('date') ?? now()->toDateString();

        $this->info("Debugowanie zleceń na datę: {$date}");
        $this->newLine();

        // Sprawdź zlecenia
        $orders = ProductionOrder::whereDate('data_produkcji', $date)->get();
        $this->info("Znalezione zlecenia: " . $orders->count());

        foreach ($orders as $order) {
            $this->line("- {$order->nazwa} (ID: {$order->id}, Status: {$order->status})");
        }

        $this->newLine();

        // Sprawdź pozycje
        $items = ProductionOrderItem::with(['product', 'productionOrder'])
            ->whereHas('productionOrder', function ($query) use ($date) {
                $query->whereDate('data_produkcji', $date);
            })
            ->get();

        $this->info("Znalezione pozycje: " . $items->count());

        foreach ($items as $item) {
            $this->line("- {$item->product->nazwa}: {$item->ilosc} szt (Status pozycji: {$item->status}, Status zlecenia: {$item->productionOrder->status}, Krok: {$item->current_step})");
        }

        $this->newLine();

        // Sprawdź filtrowanie jak w panelu piekarzy
        $filteredItems = ProductionOrderItem::with(['product', 'productionOrder'])
            ->whereHas('productionOrder', function ($query) use ($date) {
                $query->whereDate('data_produkcji', $date)
                      ->whereNotIn('status', ['anulowane']);
            })
            ->whereNotIn('status', ['zakonczone'])
            ->get();

        $this->info("Pozycje po filtrowaniu (jak w panelu piekarzy): " . $filteredItems->count());

        foreach ($filteredItems as $item) {
            $this->line("- {$item->product->nazwa}: {$item->ilosc} szt (Status pozycji: {$item->status}, Status zlecenia: {$item->productionOrder->status})");
        }
    }
}
