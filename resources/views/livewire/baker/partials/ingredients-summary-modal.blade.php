{{-- Modal z podsumowaniem składników --}}
@if($showIngredientsSummary)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">
                        📋 Lista zakupów na {{ Carbon\Carbon::parse($selectedDate)->format('d.m.Y') }}
                    </h3>
                    <p class="text-sm text-green-600 font-medium mt-1">
                        🛒 Wszystkie składniki potrzebne na dzisiaj
                    </p>
                </div>
                <button wire:click="closeIngredientsSummary"
                        class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                @php
                    // Zbierz wszystkie produkty na wybrany dzień
                    $allItems = \App\Models\ProductionOrderItem::with(['product.recipes.steps.materials', 'productionOrder'])
                        ->whereHas('productionOrder', function ($query) {
                            $query->whereDate('data_produkcji', $this->selectedDate)
                                  ->whereNotIn('status', ['anulowane']);
                        })
                        ->whereNotIn('status', ['zakonczone'])
                        ->get();

                    // Grupuj według produktów
                    $productGroups = $allItems->groupBy('product_id');

                    // Zbierz wszystkie materiały z przeliczeniami
                    $allMaterials = collect();

                    foreach ($productGroups as $productId => $items) {
                        $product = $items->first()->product;
                        $totalQuantity = $items->sum('ilosc');

                        if ($product->recipes && $product->recipes->count() > 0) {
                            foreach ($product->recipes as $recipe) {
                                if ($recipe->steps) {
                                    foreach ($recipe->steps as $step) {
                                        if ($step->materials) {
                                            foreach ($step->materials as $material) {
                                                $recipePortion = $recipe->ilosc_porcji ?: 1;
                                                $scalingFactor = $totalQuantity / $recipePortion;
                                                $totalAmount = $material->pivot->ilosc * $scalingFactor;

                                                $allMaterials->push([
                                                    'material' => $material,
                                                    'amount' => $totalAmount,
                                                    'unit' => $material->pivot->jednostka,
                                                    'product' => $product->nazwa,
                                                    'quantity' => $totalQuantity
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // Grupuj materiały według ID i sumuj ilości
                    $groupedMaterials = $allMaterials->groupBy('material.id')->map(function($materials) {
                        $first = $materials->first();
                        $totalAmount = $materials->sum('amount');
                        $products = $materials->pluck('product')->unique()->implode(', ');

                        return [
                            'material' => $first['material'],
                            'total_amount' => $totalAmount,
                            'unit' => $first['unit'],
                            'products' => $products,
                            'details' => $materials
                        ];
                    })->sortBy('material.nazwa');
                @endphp

                @if($groupedMaterials->count() > 0)
                    {{-- Podsumowanie --}}
                    <div class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
                        <h4 class="text-lg font-medium text-green-800 mb-2">📊 Podsumowanie:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $groupedMaterials->count() }}</div>
                                <div class="text-green-700">różnych składników</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $productGroups->count() }}</div>
                                <div class="text-green-700">różnych produktów</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $allItems->sum('ilosc') }}</div>
                                <div class="text-green-700">sztuk do wyprodukowania</div>
                            </div>
                        </div>
                    </div>

                    {{-- Lista składników --}}
                    <div class="space-y-4">
                        @foreach($groupedMaterials as $item)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <h5 class="text-lg font-medium text-gray-900">{{ $item['material']->nazwa }}</h5>
                                        <p class="text-sm text-gray-600">{{ $item['material']->opis }}</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-green-600">
                                            {{ number_format($item['total_amount'], 2) }} {{ $item['unit'] }}
                                        </div>
                                        {{-- Przelicznik na gramy jeśli jednostka to kg --}}
                                        @if(strtolower($item['unit']) === 'kg')
                                            <div class="text-lg font-semibold text-orange-600 bg-orange-50 px-2 py-1 rounded mt-1">
                                                = {{ number_format($item['total_amount'] * 1000, 0) }} g
                                            </div>
                                        @endif
                                        <div class="text-sm text-gray-500">łącznie</div>
                                    </div>
                                </div>

                                {{-- Szczegóły użycia --}}
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <div class="text-sm text-gray-600 mb-2">
                                        <strong>Używane w:</strong> {{ $item['products'] }}
                                    </div>

                                    {{-- Rozwinięcie szczegółów --}}
                                    <div class="space-y-1">
                                        @foreach($item['details'] as $detail)
                                            <div class="flex justify-between text-xs text-gray-500">
                                                <span>{{ $detail['product'] }} ({{ $detail['quantity'] }} szt)</span>
                                                <span>{{ number_format($detail['amount'], 2) }} {{ $detail['unit'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Stan magazynowy --}}
                                @if($item['material']->stan_aktualny !== null)
                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-600">Stan magazynowy:</span>
                                            <span class="font-medium {{ $item['material']->stan_aktualny >= $item['total_amount'] ? 'text-green-600' : 'text-red-600' }}">
                                                {{ number_format($item['material']->stan_aktualny, 2) }} {{ $item['unit'] }}
                                                @if($item['material']->stan_aktualny < $item['total_amount'])
                                                    <span class="text-red-600 ml-2">
                                                        (brakuje {{ number_format($item['total_amount'] - $item['material']->stan_aktualny, 2) }} {{ $item['unit'] }})
                                                    </span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Brak składników</h3>
                        <p class="text-gray-500">Na wybrany dzień nie ma produktów do wyprodukowania.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
