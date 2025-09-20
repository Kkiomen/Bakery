{{-- Analiza kosztów --}}
@php
    // Oblicz koszty receptury
    $totalCost = 0;
    $materialCosts = [];

    foreach($recipe->steps as $step) {
        foreach($step->materials as $material) {
            if ($material->cena_zakupu_gr) {
                // Konwersja jednostek do podstawowej jednostki materiału
                $baseAmount = $material->pivot->ilosc;
                if ($material->pivot->jednostka !== $material->jednostka_podstawowa) {
                    // Podstawowe konwersje
                    if ($material->pivot->jednostka === 'g' && $material->jednostka_podstawowa === 'kg') {
                        $baseAmount = $material->pivot->ilosc * 0.001;
                    } elseif ($material->pivot->jednostka === 'ml' && $material->jednostka_podstawowa === 'l') {
                        $baseAmount = $material->pivot->ilosc * 0.001;
                    }
                }

                $materialCost = ($material->cena_zakupu_gr / 100) * $baseAmount;
                $totalCost += $materialCost;

                $materialId = $material->id;
                if (!isset($materialCosts[$materialId])) {
                    $materialCosts[$materialId] = [
                        'material' => $material,
                        'total_cost' => 0,
                        'total_amount' => 0,
                        'unit' => $material->pivot->jednostka
                    ];
                }
                $materialCosts[$materialId]['total_cost'] += $materialCost;
                $materialCosts[$materialId]['total_amount'] += $material->pivot->ilosc;
            }
        }
    }

    $costPerPortion = $recipe->ilosc_porcji > 0 ? $totalCost / $recipe->ilosc_porcji : 0;
    $costPer100g = $recipe->waga_jednostkowa_g > 0 ? ($totalCost / ($recipe->ilosc_porcji * $recipe->waga_jednostkowa_g)) * 100 : 0;
@endphp

@if($totalCost > 0)
    <div class="bg-white shadow rounded-lg p-6 mt-6">
        <h3 class="text-lg font-medium text-gray-900 mb-6">💰 Analiza kosztów</h3>

        {{-- Podsumowanie kosztów --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Koszt całkowity</div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($totalCost, 2) }} zł</div>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Koszt za porcję</div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($costPerPortion, 2) }} zł</div>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Koszt za 100g</div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($costPer100g, 2) }} zł</div>
                    </div>
                </div>
            </div>

            <div class="bg-purple-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Całkowita waga</div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($recipe->ilosc_porcji * $recipe->waga_jednostkowa_g, 0) }}g</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Szczegółowe koszty składników --}}
        @if(count($materialCosts) > 0)
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900">Szczegółowe koszty składników</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Składnik</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ilość</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cena za jednostkę</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Koszt całkowity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% całości</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($materialCosts as $cost)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $cost['material']->nazwa }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $cost['total_amount'] }} {{ $cost['unit'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($cost['material']->cena_zakupu_gr / 100, 2) }} zł/{{ $cost['material']->jednostka_podstawowa }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        {{ number_format($cost['total_cost'], 2) }} zł
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format(($cost['total_cost'] / $totalCost) * 100, 1) }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endif
