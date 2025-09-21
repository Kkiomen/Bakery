<?php

namespace App\Livewire\Baker;

use App\Models\ProductionOrderItem;
use App\Models\Product;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BakerDashboard extends Component
{
    public $selectedDate;
    public $showRecipeModal = false;
    public $selectedProduct = null;
    public $selectedStep = null;

    public function mount()
    {
        $this->selectedDate = now()->toDateString();
    }

    public function changeDate($date)
    {
        $this->selectedDate = $date;
    }

    public function showRecipe($productId, $step)
    {
        $this->selectedProduct = Product::with([
            'recipes.materials',
            'recipes.steps.materials'
        ])->findOrFail($productId);
        $this->selectedStep = $step;
        $this->showRecipeModal = true;
    }

    public function closeRecipeModal()
    {
        $this->showRecipeModal = false;
        $this->selectedProduct = null;
        $this->selectedStep = null;
    }

    public function updateItemStep($itemId, $step)
    {
        $item = ProductionOrderItem::findOrFail($itemId);
        $item->moveToStep($step);

        session()->flash('success', 'Status został zaktualizowany.');
    }

    public function nextStep($itemId)
    {
        $item = ProductionOrderItem::findOrFail($itemId);
        $item->moveToNextStep();

        session()->flash('success', 'Przeszedł do kolejnego kroku.');
    }

    public function previousStep($itemId)
    {
        $item = ProductionOrderItem::findOrFail($itemId);
        $item->moveToPreviousStep();

        session()->flash('success', 'Cofnięto do poprzedniego kroku.');
    }

    public function render()
    {
        // Pobierz wszystkie pozycje na wybrany dzień
        $items = ProductionOrderItem::with(['product.recipes.steps.materials', 'productionOrder'])
            ->whereHas('productionOrder', function ($query) {
                $query->whereDate('data_produkcji', $this->selectedDate)
                      ->whereNotIn('status', ['anulowane']); // Wyklucz tylko anulowane zlecenia
            })
            ->whereNotIn('status', ['zakonczone']) // Wyklucz tylko zakończone pozycje
            ->get();

        // Grupuj pozycje według produktu i zsumuj ilości
        $productCards = $items->groupBy('product_id')->map(function ($productItems, $productId) {
            $product = $productItems->first()->product;
            $totalQuantity = $productItems->sum('ilosc');
            $totalProduced = $productItems->sum('ilosc_wyprodukowana');

            // Grupuj według kroków
            $stepGroups = $productItems->groupBy('current_step')->map(function ($stepItems, $step) {
                return [
                    'step' => $step,
                    'count' => $stepItems->count(),
                    'quantity' => $stepItems->sum('ilosc'),
                    'items' => $stepItems,
                ];
            });

            // Znajdź najczęstszy krok (dominujący)
            $dominantStep = $stepGroups->sortByDesc('quantity')->first();

            return [
                'product' => $product,
                'total_quantity' => $totalQuantity,
                'total_produced' => $totalProduced,
                'remaining_quantity' => $totalQuantity - $totalProduced,
                'progress_percentage' => $totalQuantity > 0 ? round(($totalProduced / $totalQuantity) * 100) : 0,
                'dominant_step' => $dominantStep['step'] ?? 'waiting',
                'step_groups' => $stepGroups,
                'items' => $productItems,
                'orders_count' => $productItems->pluck('production_order_id')->unique()->count(),
            ];
        })->sortBy(function ($card) {
            // Sortuj według priorytetu kroków (wcześniejsze kroki na górze)
            $stepPriority = [
                'waiting' => 1,
                'preparing' => 2,
                'mixing' => 3,
                'first_rise' => 4,
                'shaping' => 5,
                'second_rise' => 6,
                'baking' => 7,
                'cooling' => 8,
                'packaging' => 9,
                'completed' => 10,
            ];

            return $stepPriority[$card['dominant_step']] ?? 999;
        });

        // Statystyki dla nagłówka
        $stats = [
            'total_products' => $productCards->count(),
            'total_items' => $items->count(),
            'total_quantity' => $items->sum('ilosc'),
            'completed_quantity' => $items->sum('ilosc_wyprodukowana'),
            'in_progress' => $items->where('current_step', '!=', 'waiting')->count(),
            'waiting' => $items->where('current_step', 'waiting')->count(),
        ];

        return view('livewire.baker.baker-dashboard', [
            'productCards' => $productCards,
            'stats' => $stats,
            'availableDates' => $this->getAvailableDates(),
        ]);
    }

    private function getAvailableDates(): Collection
    {
        // Pobierz daty z aktywnymi zleceniami (ostatnie 7 dni i następne 14 dni)
        $startDate = now()->subDays(7);
        $endDate = now()->addDays(14);

        return collect(range(0, $startDate->diffInDays($endDate)))
            ->map(function ($days) use ($startDate) {
                $date = $startDate->copy()->addDays($days);

                $ordersCount = ProductionOrderItem::whereHas('productionOrder', function ($query) use ($date) {
                    $query->whereDate('data_produkcji', $date->toDateString())
                          ->whereNotIn('status', ['anulowane']);
                })->whereNotIn('status', ['zakonczone'])->count();

                return [
                    'date' => $date->toDateString(),
                    'label' => $date->format('d.m'),
                    'day_name' => $this->getPolishDayName($date),
                    'is_today' => $date->isToday(),
                    'is_past' => $date->isPast() && !$date->isToday(),
                    'orders_count' => $ordersCount,
                ];
            })
            ->filter(function ($dateInfo) {
                return $dateInfo['orders_count'] > 0 || $dateInfo['is_today'] || $dateInfo['date'] >= now()->toDateString();
            });
    }

    private function getPolishDayName(Carbon $date): string
    {
        $days = [
            'Monday' => 'Pon',
            'Tuesday' => 'Wt',
            'Wednesday' => 'Śr',
            'Thursday' => 'Czw',
            'Friday' => 'Pt',
            'Saturday' => 'Sob',
            'Sunday' => 'Nie',
        ];

        return $days[$date->format('l')] ?? $date->format('D');
    }
}
