{{-- Karty produktów --}}
@if($productCards->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
        @foreach($productCards as $productCard)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300"
                 x-data="{ showSubstitutes: false, showDetails: false }">
                {{-- Nagłówek karty --}}
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $productCard['product']->nazwa }}</h3>
                            <div class="flex items-center space-x-3">
                                <span class="text-3xl font-bold text-blue-600">{{ $productCard['remaining_quantity'] }}</span>
                                <span class="text-gray-500">szt do zrobienia</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500 mb-1">{{ $productCard['orders_count'] }} zleceń</div>
                            <div class="text-sm text-green-600 font-medium">{{ $productCard['total_produced'] }}/{{ $productCard['total_quantity'] }}</div>
                        </div>
                    </div>

                    {{-- Pasek postępu --}}
                    <div class="mb-4">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Postęp</span>
                            <span>{{ $productCard['progress_percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-600 h-3 rounded-full transition-all duration-300 w-{{ $productCard['progress_percentage'] }}"></div>
                        </div>
                    </div>

                    {{-- Aktualny krok --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            @php
                                $firstItem = collect($productCard['items'])->first();
                            @endphp
                            {{-- Nazwa procesu (tylko wyświetlanie) --}}
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($firstItem->step_color == 'blue') bg-blue-100 text-blue-800
                                @elseif($firstItem->step_color == 'yellow') bg-yellow-100 text-yellow-800
                                @elseif($firstItem->step_color == 'green') bg-green-100 text-green-800
                                @elseif($firstItem->step_color == 'red') bg-red-100 text-red-800
                                @elseif($firstItem->step_color == 'purple') bg-purple-100 text-purple-800
                                @elseif($firstItem->step_color == 'orange') bg-orange-100 text-orange-800
                                @elseif($firstItem->step_color == 'indigo') bg-indigo-100 text-indigo-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $firstItem->current_step_label }}
                            </span>
                        </div>

                            <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2">

                                {{-- Przycisk przepisu - tylko jeśli produkt ma przepis --}}
                                @if($productCard['product']->recipes && $productCard['product']->recipes->count() > 0)
                                    <div class="relative group">
                                        <button wire:click="showRecipe({{ $productCard['product']->id }}, '{{ $productCard['dominant_step'] }}')"
                                                class="flex items-center justify-center md:justify-start space-x-2 w-full md:w-auto px-4 py-3 md:p-2 text-blue-600 hover:bg-blue-50 active:bg-blue-100 rounded-lg transition-colors touch-manipulation">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="md:hidden text-sm font-medium">Przepis</span>
                                        </button>
                                        <div class="hidden md:block absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 whitespace-nowrap">
                                            📋 Zobacz pełny przepis
                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                        </div>
                                    </div>
                                @else
                                    {{-- Informacja o braku przepisu --}}
                                    <div class="relative group">
                                        <div class="flex items-center justify-center md:justify-start space-x-2 w-full md:w-auto px-4 py-3 md:p-2 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                            <span class="md:hidden text-sm font-medium">Brak przepisu</span>
                                        </div>
                                        <div class="hidden md:block absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 whitespace-nowrap">
                                            ⚠️ Przepis nie został jeszcze dodany
                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Przycisk zamienników --}}
                                <div class="relative group">
                                    <button @click="showSubstitutes = !showSubstitutes"
                                            class="flex items-center justify-center md:justify-start space-x-2 w-full md:w-auto px-4 py-3 md:p-2 text-orange-600 hover:bg-orange-50 active:bg-orange-100 rounded-lg transition-colors touch-manipulation">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                        </svg>
                                        <span class="md:hidden text-sm font-medium">Zamienniki</span>
                                    </button>
                                    <div class="hidden md:block absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 whitespace-nowrap">
                                        🔄 Zobacz zamienniki
                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                    </div>
                                </div>

                                {{-- Przycisk szczegółów procesu - tylko jeśli produkt ma przepis --}}
                                @if($productCard['product']->recipes && $productCard['product']->recipes->count() > 0)
                                    <div class="relative group">
                                        <button @click="showDetails = !showDetails"
                                                class="flex items-center justify-center md:justify-start space-x-2 w-full md:w-auto px-4 py-3 md:p-2 text-green-600 hover:bg-green-50 active:bg-green-100 rounded-lg transition-colors touch-manipulation">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="md:hidden text-sm font-medium">Przewodnik</span>
                                        </button>
                                        <div class="hidden md:block absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 whitespace-nowrap">
                                            ℹ️ Co robić w każdym kroku?
                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                    </div>
                </div>

                {{-- Szczegóły pozycji --}}
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach($productCard['step_groups'] as $stepGroup)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                {{-- Klikalna sekcja z informacjami o kroku --}}
                                <button wire:click="showStepIngredients({{ $productCard['product']->id }}, '{{ $stepGroup['step'] }}')"
                                        class="flex items-center space-x-3 flex-1 text-left hover:bg-gray-100 rounded p-2 transition-colors">
                                    @php
                                        $stepItem = collect($stepGroup['items'])->first();
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                        @if($stepItem->step_color == 'blue') bg-blue-100 text-blue-800
                                        @elseif($stepItem->step_color == 'yellow') bg-yellow-100 text-yellow-800
                                        @elseif($stepItem->step_color == 'green') bg-green-100 text-green-800
                                        @elseif($stepItem->step_color == 'red') bg-red-100 text-red-800
                                        @elseif($stepItem->step_color == 'purple') bg-purple-100 text-purple-800
                                        @elseif($stepItem->step_color == 'orange') bg-orange-100 text-orange-800
                                        @elseif($stepItem->step_color == 'indigo') bg-indigo-100 text-indigo-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $stepItem->current_step_label }}
                                    </span>
                                    <span class="text-sm text-gray-600 font-medium">{{ $stepGroup['quantity'] }} szt</span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                </button>

                                <div class="flex flex-wrap gap-3 md:gap-2">
                                    @foreach($stepGroup['items'] as $item)
                                        <div class="relative group">
                                            {{-- Przycisk następny krok --}}
                                            <button wire:click="nextStep({{ $item->id }})"
                                                    class="px-4 py-3 text-sm md:text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:bg-blue-800 transition-colors font-medium min-w-[100px] md:min-w-[80px] touch-manipulation">
                                                @php
                                                    $nextStepText = match($item->current_step) {
                                                        'waiting' => '▶️ Zacznij',
                                                        'preparing' => '🥄 Zmieszaj',
                                                        'mixing' => '⏰ Na wyrośnięcie',
                                                        'first_rise' => '👐 Formuj',
                                                        'shaping' => '⏰ Drugi wyrośnięcie',
                                                        'second_rise' => '🔥 Do pieca',
                                                        'baking' => '❄️ Studzenie',
                                                        'cooling' => '📦 Pakuj',
                                                        'packaging' => '✅ Gotowe',
                                                        default => 'Dalej'
                                                    };
                                                @endphp
                                                {{ $nextStepText }}
                                            </button>

                                            {{-- Tooltip z instrukcjami - tylko na desktop --}}
                                            <div class="hidden md:block absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 max-w-xs">
                                                <div class="font-semibold">{{ $item->productionOrder->nazwa }}</div>
                                                <div class="text-gray-300">{{ $item->ilosc }} szt</div>
                                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </div>

                                        {{-- Przycisk cofnij (tylko jeśli nie jest w waiting) --}}
                                        @if($item->current_step !== 'waiting')
                                            <div class="relative group">
                                                <button wire:click="previousStep({{ $item->id }})"
                                                        class="px-3 py-3 text-sm md:text-xs bg-gray-500 text-white rounded-lg hover:bg-gray-600 active:bg-gray-700 transition-colors touch-manipulation">
                                                    ↩️
                                                </button>

                                                <div class="hidden md:block absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 whitespace-nowrap">
                                                    Cofnij do poprzedniego kroku
                                                    <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

                                    {{-- Przycisk "Wszystkie dalej" dla tego konkretnego procesu --}}
                                    @if($stepGroup['step'] !== 'completed')
                                        <div class="relative group ml-2">
                                            <button wire:click="moveStepToNextStep({{ $productCard['product']->id }}, '{{ $stepGroup['step'] }}')"
                                                    class="px-3 py-3 text-sm md:text-xs bg-green-600 text-white rounded-lg hover:bg-green-700 active:bg-green-800 transition-colors touch-manipulation font-bold">
                                                ⏭️
                                            </button>

                                            <div class="hidden md:block absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 whitespace-nowrap">
                                                Przenieś wszystkie {{ $stepGroup['quantity'] }} szt z tego procesu na kolejny krok
                                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Szczegóły procesu - tylko jeśli produkt ma przepis --}}
                    @if($productCard['product']->recipes && $productCard['product']->recipes->count() > 0)
                        <div x-show="showDetails"
                             x-transition
                             class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
                        <h4 class="font-medium text-green-800 mb-3">🧑‍🍳 Przewodnik dla młodego piekarza:</h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-start space-x-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 min-w-[120px]">
                                    1️⃣ Oczekuje
                                </span>
                                <div class="flex-1">
                                    <div class="text-green-700 font-medium">Przygotuj wszystkie składniki</div>
                                    <div class="text-green-600 text-xs mt-1">Zważ mąkę, drożdże, sól. Sprawdź temperaturę wody (25-30°C)</div>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 min-w-[120px]">
                                    2️⃣ Przygotowanie
                                </span>
                                <div class="flex-1">
                                    <div class="text-green-700 font-medium">Rozpuść drożdże, podgrzej piekarnik</div>
                                    <div class="text-green-600 text-xs mt-1">⏱️ 10-15 min • Piekarnik na 220°C (jeśli potrzeba)</div>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-indigo-100 text-indigo-800 min-w-[120px]">
                                    3️⃣ Mieszanie
                                </span>
                                <div class="flex-1">
                                    <div class="text-green-700 font-medium">Zmieszaj i wyrabiaj ciasto</div>
                                    <div class="text-green-600 text-xs mt-1">⏱️ 10-15 min • Ciasto ma być gładkie i elastyczne</div>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800 min-w-[120px]">
                                    4️⃣ Wyrośnięcie I
                                </span>
                                <div class="flex-1">
                                    <div class="text-green-700 font-medium">Pierwsze wyrośnięcie</div>
                                    <div class="text-green-600 text-xs mt-1">⏱️ 60-90 min • Przykryj ściereczką, ciepłe miejsce (25-28°C)</div>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-pink-100 text-pink-800 min-w-[120px]">
                                    5️⃣ Formowanie
                                </span>
                                <div class="flex-1">
                                    <div class="text-green-700 font-medium">Uformuj bochenki/bułki</div>
                                    <div class="text-green-600 text-xs mt-1">⏱️ 15-20 min • Przełóż na blachę z papierem</div>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800 min-w-[120px]">
                                    6️⃣ Wyrośnięcie II
                                </span>
                                <div class="flex-1">
                                    <div class="text-green-700 font-medium">Drugie wyrośnięcie</div>
                                    <div class="text-green-600 text-xs mt-1">⏱️ 30-45 min • Na blasze, przykryj ściereczką</div>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-orange-100 text-orange-800 min-w-[120px]">
                                    7️⃣ Pieczenie
                                </span>
                                <div class="flex-1">
                                    <div class="text-green-700 font-medium">Piecz w rozgrzanym piekarniku</div>
                                    <div class="text-green-600 text-xs mt-1">⏱️ 25-35 min • Sprawdź czy brzmi głucho po opukaniu</div>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-cyan-100 text-cyan-800 min-w-[120px]">
                                    8️⃣ Studzenie
                                </span>
                                <div class="flex-1">
                                    <div class="text-green-700 font-medium">Wystudź na kratce</div>
                                    <div class="text-green-600 text-xs mt-1">⏱️ 30-60 min • Nie pakuj gorących produktów!</div>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 min-w-[120px]">
                                    9️⃣ Pakowanie
                                </span>
                                <div class="flex-1">
                                    <div class="text-green-700 font-medium">Zapakuj wystudzone produkty</div>
                                    <div class="text-green-600 text-xs mt-1">⏱️ 5-10 min • Sprawdź jakość, oznacz datę</div>
                                </div>
                            </div>
                        </div>
                        </div>
                    @endif

                    {{-- Zamienniki składników --}}
                    <div x-show="showSubstitutes"
                         x-transition
                         class="mt-4 p-4 bg-orange-50 rounded-lg border border-orange-200">
                        <h4 class="font-medium text-orange-800 mb-3">🔄 Zamienniki składników:</h4>
                        @php
                            $hasSubstitutes = false;
                            $allMaterials = collect();

                            // Zbierz wszystkie materiały z kroków przepisów produktu
                            if($productCard['product']->recipes) {
                                foreach($productCard['product']->recipes as $recipe) {
                                    if($recipe->steps) {
                                        foreach($recipe->steps as $step) {
                                            if($step->materials) {
                                                $allMaterials = $allMaterials->merge($step->materials);
                                            }
                                        }
                                    }
                                }
                            }

                            // Filtruj tylko te z zamiennikami
                            $materialsWithSubstitutes = $allMaterials->filter(function($material) {
                                return $material->pivot->has_substitutes && $material->pivot->substitutes;
                            });
                        @endphp

                        @if($materialsWithSubstitutes->count() > 0)
                            <div class="space-y-3">
                                @foreach($materialsWithSubstitutes as $material)
                                    <div class="border-l-4 border-orange-400 pl-3 py-2">
                                        <div class="font-medium text-orange-800 mb-1">
                                            {{ $material->nazwa }} ({{ $material->pivot->ilosc }} {{ $material->pivot->jednostka }})
                                        </div>
                                        <div class="space-y-1">
                                            @php
                                                $substitutes = is_string($material->pivot->substitutes)
                                                    ? json_decode($material->pivot->substitutes, true)
                                                    : $material->pivot->substitutes;
                                            @endphp
                                            @if(is_array($substitutes))
                                                @foreach($substitutes as $substitute)
                                                    @php
                                                        $substituteMaterial = \App\Models\Material::find($substitute['material_id'] ?? null);
                                                        $ratio = $substitute['wspolczynnik_przeliczenia'] ?? 1;
                                                        $newAmount = $material->pivot->ilosc * $ratio;
                                                    @endphp
                                                    @if($substituteMaterial)
                                                        <div class="flex justify-between items-center text-sm bg-white p-2 rounded">
                                                            <span class="text-orange-700">
                                                                → {{ $substituteMaterial->nazwa }}
                                                            </span>
                                                            <span class="font-medium text-orange-900">
                                                                {{ number_format($newAmount, 2) }} {{ $material->pivot->jednostka }}
                                                            </span>
                                                        </div>
                                                        @if(isset($substitute['uwagi']) && $substitute['uwagi'])
                                                            <div class="text-xs text-orange-600 italic pl-2">
                                                                {{ $substitute['uwagi'] }}
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <svg class="mx-auto h-8 w-8 text-orange-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                <p class="text-sm text-orange-600">Brak zdefiniowanych zamienników składników</p>
                                <p class="text-xs text-orange-500 mt-1">Wszystkie składniki muszą być użyte zgodnie z przepisem</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    {{-- Brak produktów --}}
    <div class="text-center py-12">
        <div class="bg-white rounded-xl shadow-lg p-12">
            <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 5l7 7-7 7"></path>
            </svg>
            <h3 class="text-2xl font-medium text-gray-900 mb-2">Brak produktów do wyprodukowania</h3>
            <p class="text-gray-500 text-lg">Na wybrany dzień nie ma żadnych zleceń produkcji.</p>
            <div class="mt-6">
                <button wire:click="changeDate('{{ now()->toDateString() }}')"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Przejdź do dzisiaj
                </button>
            </div>
        </div>
    </div>
@endif
