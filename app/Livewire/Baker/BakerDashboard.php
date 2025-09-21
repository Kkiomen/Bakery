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
    public $selectedProductTotalQuantity = 0;
    public $showIngredientsSummary = false;
    public $showStepIngredientsModal = false;
    public $stepIngredientsData = [];

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

        // Oblicz całkowitą ilość produktu do wyprodukowania na wybrany dzień
        $totalQuantityNeeded = ProductionOrderItem::with('productionOrder')
            ->where('product_id', $productId)
            ->whereHas('productionOrder', function ($query) {
                $query->whereDate('data_produkcji', $this->selectedDate)
                      ->whereNotIn('status', ['anulowane']);
            })
            ->whereNotIn('status', ['zakonczone'])
            ->sum('ilosc');

        $this->selectedProductTotalQuantity = $totalQuantityNeeded;
        $this->showRecipeModal = true;
    }

    public function closeRecipeModal()
    {
        $this->showRecipeModal = false;
        $this->selectedProduct = null;
        $this->selectedStep = null;
        $this->selectedProductTotalQuantity = 0;
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

    public function moveAllToNextStep($productId)
    {
        // Pobierz wszystkie pozycje dla danego produktu na wybrany dzień
        $items = ProductionOrderItem::with('productionOrder')
            ->where('product_id', $productId)
            ->whereHas('productionOrder', function ($query) {
                $query->whereDate('data_produkcji', $this->selectedDate)
                      ->whereNotIn('status', ['anulowane']);
            })
            ->whereNotIn('status', ['zakonczone'])
            ->get();

        $movedCount = 0;
        foreach ($items as $item) {
            if ($item->current_step !== 'completed') {
                $item->moveToNextStep();
                $movedCount++;
            }
        }

        if ($movedCount > 0) {
            session()->flash('success', "Przeniesiono {$movedCount} pozycji na kolejny krok.");
        } else {
            session()->flash('info', 'Wszystkie pozycje są już zakończone.');
        }
    }

    public function showIngredientsSummary()
    {
        $this->showIngredientsSummary = true;
    }

    public function closeIngredientsSummary()
    {
        $this->showIngredientsSummary = false;
    }

    public function showStepIngredients($productId, $step)
    {
        $product = Product::with([
            'recipes.steps.materials'
        ])->findOrFail($productId);

        // Oblicz całkowitą ilość produktu w danym kroku
        $totalQuantityInStep = ProductionOrderItem::with('productionOrder')
            ->where('product_id', $productId)
            ->where('current_step', $step)
            ->whereHas('productionOrder', function ($query) {
                $query->whereDate('data_produkcji', $this->selectedDate)
                      ->whereNotIn('status', ['anulowane']);
            })
            ->whereNotIn('status', ['zakonczone'])
            ->sum('ilosc');

        if ($totalQuantityInStep == 0) {
            session()->flash('info', 'Brak produktów w tym kroku.');
            return;
        }

        // Sprawdź czy produkt ma przepis
        if (!$product->recipes || $product->recipes->count() == 0) {
            session()->flash('info', 'Ten produkt nie ma przypisanego przepisu.');
            return;
        }

        $recipe = $product->recipes->first();

        // Mapowanie kroków produkcji na kroki przepisu
        $stepMapping = [
            'preparing' => ['przygotowanie'],
            'mixing' => ['mieszanie', 'przygotowanie'],
            'first_rise' => ['wyrastanie'],
            'shaping' => ['formowanie'],
            'second_rise' => ['wyrastanie'],
            'baking' => ['pieczenie'],
            'cooling' => [],
            'packaging' => [],
        ];

        $recipeStepTypes = $stepMapping[$step] ?? ['przygotowanie'];

        // Jeśli krok nie ma przypisanych typów kroków (jak cooling, packaging), sprawdź czy ma składniki
        if (empty($recipeStepTypes)) {
            session()->flash('info', 'Ten krok produkcji nie wymaga dodawania składników.');
            return;
        }

        // Znajdź składniki dla danego kroku
        $stepIngredients = collect();

        // Pobierz składniki z odpowiednich kroków przepisu
        foreach ($recipe->steps as $recipeStep) {
            if (in_array($recipeStep->typ, $recipeStepTypes)) {
                foreach ($recipeStep->materials as $material) {
                    $stepIngredients->push([
                            'material' => $material,
                            'recipe_amount' => $material->pivot->ilosc,
                            'unit' => $material->pivot->jednostka,
                            'step_name' => $recipeStep->nazwa,
                            'step_type' => $recipeStep->typ,
                        ]);
                    }
                }
            }

        // Jeśli nie znaleziono składników dla tego kroku, znaczy że nie wymaga składników
        if ($stepIngredients->isEmpty()) {
            session()->flash('info', 'Ten krok produkcji nie wymaga dodawania składników.');
            return;
        }

        // Przelicz składniki na całkowitą ilość
        $recipePortion = $recipe->ilosc_porcji ?: 1;
        $scalingFactor = $totalQuantityInStep / $recipePortion;

        $stepIngredients = $stepIngredients->map(function ($ingredient) use ($scalingFactor) {
            $ingredient['total_amount'] = $ingredient['recipe_amount'] * $scalingFactor;
            $ingredient['scaling_factor'] = $scalingFactor;
            return $ingredient;
        });

        $this->stepIngredientsData = [
            'product' => $product,
            'step' => $step,
            'step_label' => $this->getStepLabel($step),
            'step_description' => $this->getStepDescription($step),
            'total_quantity' => $totalQuantityInStep,
            'ingredients' => $stepIngredients,
        ];

        $this->showStepIngredientsModal = true;
    }

    public function closeStepIngredients()
    {
        $this->showStepIngredientsModal = false;
        $this->stepIngredientsData = [];
    }

    private function getStepLabel($step)
    {
        $labels = [
            'waiting' => 'Oczekuje',
            'preparing' => 'Przygotowanie składników',
            'mixing' => 'Mieszanie',
            'first_rise' => 'Pierwsze wyrastanie',
            'shaping' => 'Formowanie',
            'second_rise' => 'Drugie wyrastanie',
            'baking' => 'Pieczenie',
            'cooling' => 'Studzenie',
            'packaging' => 'Pakowanie',
            'completed' => 'Zakończone',
        ];

        return $labels[$step] ?? ucfirst($step);
    }

    private function getStepDescription($step)
    {
        $descriptions = [
            'waiting' => 'Produkt oczekuje na rozpoczęcie produkcji. Sprawdź dostępność składników i przygotuj stanowisko pracy.',
            'preparing' => 'Przygotuj wszystkie składniki zgodnie z recepturą. Zważ dokładnie każdy składnik i ustaw w odpowiedniej kolejności.',
            'mixing' => 'Wymieszaj składniki zgodnie z instrukcją. Zwróć uwagę na kolejność dodawania i czas mieszania.',
            'first_rise' => 'Ciasto wyrasta po raz pierwszy. Utrzymuj odpowiednią temperaturę i wilgotność. Nie przeszkadzaj w procesie.',
            'shaping' => 'Formuj ciasto w pożądane kształty. Pracuj delikatnie, aby nie uszkodzić struktury ciasta.',
            'second_rise' => 'Drugie wyrastanie - ostateczne podnoszenie przed pieczeniem. Sprawdź czy ciasto jest gotowe do pieca.',
            'baking' => 'Piecz w odpowiedniej temperaturze. Monitoruj proces i sprawdzaj gotowość. Nie otwieraj piekarnika zbyt często.',
            'cooling' => 'Ostudź wypieczone produkty na kratce. Nie pakuj zanim nie ostygną całkowicie.',
            'packaging' => 'Zapakuj gotowe produkty zgodnie ze standardami. Sprawdź jakość przed pakowaniem.',
            'completed' => 'Produkt został ukończony i jest gotowy do sprzedaży lub dostawy.',
        ];

        return $descriptions[$step] ?? 'Wykonaj czynności związane z tym etapem produkcji.';
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

    public function moveStepToNextStep($productId, $currentStep)
    {
        $items = ProductionOrderItem::with('productionOrder')
            ->where('product_id', $productId)
            ->where('current_step', $currentStep)
            ->whereHas('productionOrder', function ($query) {
                $query->whereDate('data_produkcji', $this->selectedDate)
                      ->whereNotIn('status', ['anulowane']);
            })
            ->whereNotIn('status', ['zakonczone'])
            ->get();

        $movedCount = 0;
        foreach ($items as $item) {
            if ($item->current_step !== 'completed') {
                $item->moveToNextStep();
                $movedCount++;
            }
        }

        if ($movedCount > 0) {
            $stepLabel = $this->getStepLabel($currentStep);
            session()->flash('success', "Przeniesiono {$movedCount} pozycji z procesu '{$stepLabel}' na kolejny krok.");
        } else {
            session()->flash('info', 'Wszystkie pozycje z tego procesu są już zakończone.');
        }
    }
}
