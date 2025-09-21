<?php

namespace App\Livewire\Production;

use App\Models\ProductionOrder;
use Livewire\Component;
use Carbon\Carbon;

class ProductionCalendar extends Component
{
    public $currentMonth;
    public $currentYear;
    public $selectedDate = null;
    public $showOrdersModal = false;
    public $ordersForDate = [];

    public function mount()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    public function previousMonth()
    {
        if ($this->currentMonth == 1) {
            $this->currentMonth = 12;
            $this->currentYear--;
        } else {
            $this->currentMonth--;
        }
    }

    public function nextMonth()
    {
        if ($this->currentMonth == 12) {
            $this->currentMonth = 1;
            $this->currentYear++;
        } else {
            $this->currentMonth++;
        }
    }

    public function goToToday()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    public function showOrdersForDate($date)
    {
        $this->selectedDate = $date;
        $this->ordersForDate = ProductionOrder::with(['user', 'items.product'])
            ->forDate($date)
            ->orderBy('priorytet', 'desc')
            ->orderBy('created_at')
            ->get();
        $this->showOrdersModal = true;
    }

    public function closeModal()
    {
        $this->showOrdersModal = false;
        $this->selectedDate = null;
        $this->ordersForDate = [];
    }

    public function duplicateOrder($orderId)
    {
        $order = ProductionOrder::findOrFail($orderId);

        $newOrder = $order->duplicate([
            'data_produkcji' => $this->selectedDate,
            'nazwa' => $order->nazwa . ' (kopia)',
        ]);

        $this->ordersForDate = ProductionOrder::with(['user', 'items.product'])
            ->forDate($this->selectedDate)
            ->orderBy('priorytet', 'desc')
            ->orderBy('created_at')
            ->get();

        session()->flash('success', 'Zlecenie zostało zduplikowane.');
    }

    public function render()
    {
        $startOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Pobierz wszystkie zlecenia dla aktualnego miesiąca
        $orders = ProductionOrder::with(['items'])
            ->whereBetween('data_produkcji', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy(function ($order) {
                return $order->data_produkcji->format('Y-m-d');
            });

        // Stwórz kalendarz
        $calendar = [];
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $current = $startOfCalendar->copy();
        while ($current <= $endOfCalendar) {
            $dateString = $current->toDateString();
            $dayOrders = $orders->get($dateString, collect());

            $calendar[] = [
                'date' => $current->copy(),
                'isCurrentMonth' => $current->month == $this->currentMonth,
                'isToday' => $current->isToday(),
                'orders' => $dayOrders,
                'orderCount' => $dayOrders->count(),
                'hasOverdue' => $dayOrders->filter(function ($order) {
                    return $order->isOverdue();
                })->count() > 0,
                'hasUrgent' => $dayOrders->filter(function ($order) {
                    return $order->priorytet === 'pilny';
                })->count() > 0,
            ];

            $current->addDay();
        }

        return view('livewire.production.production-calendar', [
            'calendar' => array_chunk($calendar, 7), // Podziel na tygodnie
            'monthName' => $startOfMonth->format('F Y'),
            'monthNamePl' => $this->getPolishMonthName($startOfMonth),
        ]);
    }

    private function getPolishMonthName(Carbon $date)
    {
        $months = [
            1 => 'Styczeń',
            2 => 'Luty',
            3 => 'Marzec',
            4 => 'Kwiecień',
            5 => 'Maj',
            6 => 'Czerwiec',
            7 => 'Lipiec',
            8 => 'Sierpień',
            9 => 'Wrzesień',
            10 => 'Październik',
            11 => 'Listopad',
            12 => 'Grudzień',
        ];

        return $months[$date->month] . ' ' . $date->year;
    }
}
