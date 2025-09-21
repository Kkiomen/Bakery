{{-- Modal składników dla kroku --}}
@if($showStepIngredientsModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
         wire:click.self="closeStepIngredients">
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            {{-- Nagłówek --}}
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold">
                            🥄 {{ $stepIngredientsData['step_label'] ?? '' }}
                        </h3>
                        <p class="text-purple-100 mt-1">
                            📦 {{ $stepIngredientsData['product']->nazwa ?? '' }} -
                            <span class="font-semibold">{{ $stepIngredientsData['total_quantity'] ?? 0 }} szt</span>
                            w tym kroku
                        </p>

                        {{-- Opis procesu --}}
                        @if(isset($stepIngredientsData['step_description']))
                            <div class="mt-3 p-3 bg-purple-600 bg-opacity-50 rounded-lg">
                                <p class="text-sm text-purple-100">
                                    {{ $stepIngredientsData['step_description'] }}
                                </p>
                            </div>
                        @endif
                    </div>
                    <button wire:click="closeStepIngredients"
                            class="text-white hover:text-purple-200 transition-colors p-2 rounded-full hover:bg-purple-600 ml-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Zawartość --}}
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                @if(isset($stepIngredientsData['ingredients']) && $stepIngredientsData['ingredients']->count() > 0)
                    {{-- Lista składników --}}
                    <div class="space-y-4">
                        @foreach($stepIngredientsData['ingredients'] as $ingredient)
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                                        <h4 class="text-lg font-semibold text-gray-900">
                                            {{ $ingredient['material']->nazwa }}
                                        </h4>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-purple-600">
                                            {{ number_format($ingredient['total_amount'], 2) }} {{ $ingredient['unit'] }}
                                        </div>
                                        {{-- Przelicznik na gramy jeśli jednostka to kg --}}
                                        @if(strtolower($ingredient['unit']) === 'kg')
                                            <div class="text-lg font-semibold text-orange-600 bg-orange-50 px-2 py-1 rounded mt-1">
                                                = {{ number_format($ingredient['total_amount'] * 1000, 0) }} g
                                            </div>
                                        @endif
                                        <div class="text-sm text-gray-500">
                                            do dodania teraz
                                        </div>
                                    </div>
                                </div>

                                {{-- Szczegóły przeliczenia --}}
                                <div class="bg-white rounded-lg p-3 border border-gray-100">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                        <div class="text-center">
                                            <div class="text-gray-600">Przepis (na {{ $stepIngredientsData['product']->recipes->first()->ilosc_porcji ?? 1 }} szt)</div>
                                            <div class="font-semibold text-gray-900">
                                                {{ number_format($ingredient['recipe_amount'], 2) }} {{ $ingredient['unit'] }}
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-gray-600">Współczynnik</div>
                                            <div class="font-semibold text-blue-600">
                                                ×{{ number_format($ingredient['scaling_factor'], 1) }}
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-gray-600">Razem potrzeba</div>
                                            <div class="font-semibold text-purple-600">
                                                {{ number_format($ingredient['total_amount'], 2) }} {{ $ingredient['unit'] }}
                                            </div>
                                            {{-- Przelicznik na gramy --}}
                                            @if(strtolower($ingredient['unit']) === 'kg')
                                                <div class="text-sm text-orange-600 font-medium">
                                                    ({{ number_format($ingredient['total_amount'] * 1000, 0) }} g)
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Informacje o kroku --}}
                                @if(isset($ingredient['step_name']))
                                    <div class="mt-3 text-sm text-gray-600">
                                        📋 Krok przepisu: <span class="font-medium">{{ $ingredient['step_name'] }}</span>
                                        @if(isset($ingredient['step_type']))
                                            <span class="ml-2 px-2 py-1 bg-gray-200 rounded text-xs">{{ $ingredient['step_type'] }}</span>
                                        @endif
                                    </div>
                                @endif

                                {{-- Stan magazynowy --}}
                                <div class="mt-3 flex items-center justify-between text-sm">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-gray-600">Stan magazynowy:</span>
                                        <span class="font-medium {{ $ingredient['material']->stan_aktualny >= $ingredient['total_amount'] ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($ingredient['material']->stan_aktualny, 2) }} {{ $ingredient['material']->jednostka_podstawowa }}
                                        </span>
                                    </div>
                                    @if($ingredient['material']->stan_aktualny < $ingredient['total_amount'])
                                        <span class="text-red-600 font-medium">
                                            ⚠️ Brakuje {{ number_format($ingredient['total_amount'] - $ingredient['material']->stan_aktualny, 2) }} {{ $ingredient['unit'] }}
                                        </span>
                                    @else
                                        <span class="text-green-600 font-medium">
                                            ✅ Wystarczy
                                        </span>
                                    @endif
                                </div>

                                {{-- Zamienniki --}}
                                @php
                                    // Znajdź zamienniki w recipe_step_materials pivot table
                                    $substitutes = [];
                                    if(isset($stepIngredientsData['product']) && $stepIngredientsData['product']->recipes->count() > 0) {
                                        $recipe = $stepIngredientsData['product']->recipes->first();
                                        foreach($recipe->steps as $step) {
                                            foreach($step->materials as $stepMaterial) {
                                                if($stepMaterial->id == $ingredient['material']->id && $stepMaterial->pivot->has_substitutes) {
                                                    $substitutes = json_decode($stepMaterial->pivot->substitutes, true) ?? [];
                                                    break 2;
                                                }
                                            }
                                        }
                                    }
                                @endphp
                                @if(!empty($substitutes))
                                        <div class="mt-3 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                            <h5 class="text-sm font-medium text-orange-800 mb-2">🔄 Dostępne zamienniki:</h5>
                                            <div class="space-y-2">
                                                @foreach($substitutes as $substitute)
                                                    @php
                                                        $substituteMaterial = \App\Models\Material::find($substitute['material_id']);
                                                        $substituteAmount = $ingredient['total_amount'] * $substitute['wspolczynnik_przeliczenia'];
                                                    @endphp
                                                    @if($substituteMaterial)
                                                        <div class="flex items-center justify-between text-sm">
                                                            <div>
                                                                <span class="font-medium">{{ $substituteMaterial->nazwa }}</span>
                                                                @if(isset($substitute['uwagi']))
                                                                    <span class="text-orange-600 ml-2">({{ $substitute['uwagi'] }})</span>
                                                                @endif
                                                            </div>
                                                            <div class="font-semibold text-orange-700">
                                                                {{ number_format($substituteAmount, 2) }} {{ $ingredient['unit'] }}
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Wskazówki dla procesu --}}
                    @php
                        $stepTips = [
                            'preparing' => [
                                '⚖️ Zważ składniki dokładnie - dokładność jest kluczowa',
                                '🌡️ Sprawdź temperaturę składników (masło, jajka)',
                                '📋 Przygotuj wszystkie narzędzia przed rozpoczęciem',
                                '🧹 Utrzymuj porządek na stanowisku pracy'
                            ],
                            'mixing' => [
                                '🥄 Mieszaj zgodnie z recepturą - kolejność ma znaczenie',
                                '⏱️ Przestrzegaj czasu mieszania',
                                '👀 Obserwuj konsystencję ciasta',
                                '🌡️ Kontroluj temperaturę ciasta'
                            ],
                            'first_rise' => [
                                '🌡️ Temperatura 24-28°C jest optymalna',
                                '💧 Utrzymuj wilgotność - przykryj ściereczką',
                                '⏰ Czas wyrastania: 45-90 minut',
                                '📏 Ciasto powinno podwoić objętość'
                            ],
                            'shaping' => [
                                '👐 Pracuj delikatnie - nie niszcz struktury',
                                '🌾 Posyp mąką jeśli ciasto klei',
                                '📐 Zachowaj jednolite kształty',
                                '⚡ Pracuj szybko - ciasto nie lubi czekać'
                            ],
                            'second_rise' => [
                                '⏰ Krótszy czas niż pierwsze wyrastanie',
                                '🌡️ Temperatura podobna do pierwszego wyrastania',
                                '🔍 Test palcem - delikatne naciśnięcie',
                                '🚫 Nie przefermentuj - może opaść'
                            ],
                            'baking' => [
                                '🌡️ Nagrzej piekarnik z wyprzedzeniem',
                                '💨 Para na początku dla lepszej skórki',
                                '👀 Nie otwieraj piekarnika bez potrzeby',
                                '🎯 Sprawdzaj gotowość patyczkiem lub termometrem'
                            ],
                            'cooling' => [
                                '🌬️ Chłodź na kratce - cyrkulacja powietrza',
                                '⏰ Poczekaj minimum 30 minut przed krojeniem',
                                '🚫 Nie pakuj ciepłych produktów',
                                '🌡️ Temperatura pokojowa przed pakowaniem'
                            ],
                            'packaging' => [
                                '🔍 Sprawdź jakość przed pakowaniem',
                                '📦 Używaj odpowiednich opakowań',
                                '🏷️ Etykietuj z datą produkcji',
                                '❄️ Przechowuj w odpowiednich warunkach'
                            ]
                        ];

                        $currentTips = $stepTips[$stepIngredientsData['step'] ?? ''] ?? [];
                    @endphp

                    @if(!empty($currentTips))
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                            <h4 class="text-lg font-semibold text-blue-800 mb-3">💡 Wskazówki dla tego kroku</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($currentTips as $tip)
                                    <div class="flex items-start space-x-2 text-sm text-blue-700">
                                        <div class="w-2 h-2 bg-blue-400 rounded-full mt-2 flex-shrink-0"></div>
                                        <span>{{ $tip }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Podsumowanie --}}
                    <div class="mt-6 p-4 bg-purple-50 border border-purple-200 rounded-xl">
                        <h4 class="text-lg font-semibold text-purple-800 mb-3">📊 Podsumowanie kroku</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-purple-600">{{ $stepIngredientsData['total_quantity'] ?? 0 }}</div>
                                <div class="text-purple-700">sztuk w kroku "{{ $stepIngredientsData['step_label'] ?? '' }}"</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-purple-600">{{ $stepIngredientsData['ingredients']->count() ?? 0 }}</div>
                                <div class="text-purple-700">składników do przygotowania</div>
                            </div>
                        </div>
                        <div class="mt-3 text-center text-sm text-purple-600">
                            💡 Wszystkie ilości są przeliczone na aktualną liczbę produktów w tym kroku
                        </div>
                    </div>
                @else
                    {{-- Brak składników --}}
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">🤷‍♂️</div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Brak składników dla tego kroku</h3>
                        <p class="text-gray-500">
                            Nie znaleziono składników przypisanych do kroku "{{ $stepIngredientsData['step_label'] ?? '' }}"
                            lub produkt nie ma przypisanego przepisu.
                        </p>
                    </div>
                @endif
            </div>

            {{-- Stopka --}}
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        🕒 Aktualizowane na bieżąco
                    </div>
                    <button wire:click="closeStepIngredients"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        Zamknij
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
