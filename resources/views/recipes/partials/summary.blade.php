{{-- Podsumowanie składników --}}
@php
    $allMaterials = [];
    foreach($recipe->steps as $step) {
        foreach($step->materials as $material) {
            $materialId = $material->id;
            if (!isset($allMaterials[$materialId])) {
                $allMaterials[$materialId] = [
                    'material' => $material,
                    'total_amount' => 0,
                    'unit' => $material->pivot->jednostka,
                    'usages' => [],
                    'substitutes' => []
                ];
            }

            $allMaterials[$materialId]['usages'][] = [
                'step_name' => $step->nazwa,
                'amount' => $material->pivot->ilosc,
                'unit' => $material->pivot->jednostka,
                'preparation' => $material->pivot->sposob_przygotowania,
            ];

            if ($allMaterials[$materialId]['unit'] === $material->pivot->jednostka) {
                $allMaterials[$materialId]['total_amount'] += $material->pivot->ilosc;
            }

            // Zbierz zamienniki
            if ($material->pivot->ma_zamienniki && $material->pivot->zamienniki) {
                $substitutes = json_decode($material->pivot->zamienniki, true) ?? [];
                $allMaterials[$materialId]['substitutes'] = array_merge($allMaterials[$materialId]['substitutes'], $substitutes);
            }
        }
    }
@endphp

@if(count($allMaterials) > 0)
    <div class="bg-white shadow rounded-lg p-6 mt-6">
        <h3 class="text-lg font-medium text-gray-900 mb-6">📊 Podsumowanie składników</h3>
        <div class="space-y-4">
            @foreach($allMaterials as $materialData)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium text-gray-900">{{ $materialData['material']->nazwa }}</h4>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                Łącznie: {{ $materialData['total_amount'] }} {{ $materialData['unit'] }}
                            </span>
                            @if(count($materialData['substitutes']) > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    {{ count($materialData['substitutes']) }} zamiennik(ów)
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="text-xs text-gray-600 mb-2">
                        <span class="font-medium">Użycie:</span>
                        @foreach($materialData['usages'] as $usage)
                            <span class="ml-1">{{ $usage['step_name'] }}: {{ $usage['amount'] }} {{ $usage['unit'] }}@if($usage['preparation']) ({{ $usage['preparation'] }})@endif</span>@if(!$loop->last),@endif
                        @endforeach
                    </div>

                    {{-- Zamienniki w podsumowaniu --}}
                    @if(count($materialData['substitutes']) > 0)
                        <div class="mt-2 pl-3 border-l-2 border-green-200">
                            <div class="text-xs text-green-700">
                                <span class="font-medium">Dostępne zamienniki:</span>
                                @foreach(array_unique($materialData['substitutes'], SORT_REGULAR) as $substitute)
                                    <div class="ml-1">
                                        {{ $substitute['material_name'] }}
                                        <span class="text-green-600">({{ $substitute['wspolczynnik_przeliczenia'] }}x)</span>
                                        @if($substitute['uwagi'])
                                            <span class="text-gray-500">- {{ $substitute['uwagi'] }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Statystyki --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Różnych składników</div>
                        <div class="text-2xl font-bold text-gray-900">{{ count($allMaterials) }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Składników z zamiennikami</div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ collect($allMaterials)->filter(function($m) { return count($m['substitutes']) > 0; })->count() }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Procesów</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $recipe->steps->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
