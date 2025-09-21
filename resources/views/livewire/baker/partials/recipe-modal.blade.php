{{-- Modal z przepisem --}}
@if($showRecipeModal && $selectedProduct)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">
                    📋 Przepis: {{ $selectedProduct->nazwa }}
                </h3>
                <button wire:click="closeRecipeModal"
                        class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                @if($selectedProduct->recipes && $selectedProduct->recipes->count() > 0)
                    @foreach($selectedProduct->recipes as $recipe)
                        <div class="mb-8">
                            <h4 class="text-xl font-semibold text-gray-900 mb-4">{{ $recipe->nazwa }}</h4>

                            {{-- Składniki --}}
                            <div class="mb-6">
                                <h5 class="text-lg font-medium text-gray-800 mb-3">🥄 Składniki:</h5>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    @php
                                        // Zbierz wszystkie materiały z kroków przepisu
                                        $allMaterials = collect();
                                        if($recipe->steps) {
                                            foreach($recipe->steps as $step) {
                                                if($step->materials) {
                                                    foreach($step->materials as $material) {
                                                        $allMaterials->push($material);
                                                    }
                                                }
                                            }
                                        }

                                        // Grupuj materiały według ID (żeby nie duplikować)
                                        $groupedMaterials = $allMaterials->groupBy('id')->map(function($materials) {
                                            $first = $materials->first();
                                            // Sumuj ilości jeśli materiał występuje w wielu krokach
                                            $totalAmount = $materials->sum('pivot.ilosc');
                                            $first->total_amount = $totalAmount;
                                            return $first;
                                        });
                                    @endphp

                                    @if($groupedMaterials->count() > 0)
                                        <div class="space-y-4">
                                            @foreach($groupedMaterials as $material)
                                                <div class="border-l-4 border-blue-500 pl-4 py-2">
                                                    <div class="flex justify-between items-center mb-2">
                                                        <span class="text-gray-700 font-medium">{{ $material->nazwa }}</span>
                                                        <span class="font-bold text-gray-900 bg-blue-100 px-2 py-1 rounded">
                                                            {{ $material->total_amount }} {{ $material->pivot->jednostka }}
                                                        </span>
                                                    </div>

                                                    {{-- Zamienniki składnika --}}
                                                    @if($material->pivot->has_substitutes && $material->pivot->substitutes)
                                                        <div class="mt-2 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                                            <div class="text-sm font-medium text-orange-800 mb-2">
                                                                🔄 Możliwe zamienniki:
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
                                                                            $newAmount = $material->total_amount * $ratio;
                                                                        @endphp
                                                                        @if($substituteMaterial)
                                                                            <div class="flex justify-between items-center text-sm">
                                                                                <span class="text-orange-700">
                                                                                    {{ $substituteMaterial->nazwa }}
                                                                                </span>
                                                                                <span class="font-medium text-orange-900">
                                                                                    {{ number_format($newAmount, 2) }} {{ $material->pivot->jednostka }}
                                                                                </span>
                                                                            </div>
                                                                            @if(isset($substitute['uwagi']) && $substitute['uwagi'])
                                                                                <div class="text-xs text-orange-600 italic">
                                                                                    {{ $substitute['uwagi'] }}
                                                                                </div>
                                                                            @endif
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-gray-500">Brak zdefiniowanych składników</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Kroki --}}
                            <div class="mb-6">
                                <h5 class="text-lg font-medium text-gray-800 mb-3">👨‍🍳 Kroki wykonania:</h5>
                                @if($recipe->steps && $recipe->steps->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($recipe->steps->sortBy('kolejnosc') as $step)
                                            <div class="flex space-x-4 p-4 bg-gray-50 rounded-lg">
                                                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                                    {{ $step->kolejnosc }}
                                                </div>
                                                <div class="flex-1">
                                                    <h6 class="font-medium text-gray-900 mb-1">{{ $step->nazwa }}</h6>
                                                    <p class="text-gray-700 text-sm">{{ $step->opis }}</p>
                                                    @if($step->czas_min)
                                                        <div class="mt-2 text-xs text-blue-600">
                                                            ⏱️ Czas: {{ $step->czas_min }} min
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500">Brak zdefiniowanych kroków</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Brak przepisu</h3>
                        <p class="text-gray-500">Dla tego produktu nie został jeszcze zdefiniowany przepis.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
