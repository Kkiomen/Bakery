{{-- Prawa kolumna - Procesy i składniki --}}
<div class="lg:col-span-2 space-y-6">
        {{-- Procesy technologiczne --}}
        @if($recipe->steps->count() > 0)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">⚙️ Procesy technologiczne ({{ $recipe->steps->count() }})</h3>
                <div class="space-y-6">
                    @foreach($recipe->steps->sortBy('kolejnosc') as $step)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-800 text-sm font-medium">
                                        {{ $step->kolejnosc + 1 }}
                                    </span>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">{{ $step->nazwa }}</h4>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $step->typ }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    @if($step->czas_min)
                                        <span>⏱️ {{ $step->czas_min }} min</span>
                                    @endif
                                    @if($step->temperatura_c)
                                        <span>🌡️ {{ $step->temperatura_c }}°C</span>
                                    @endif
                                    @if($step->wilgotnosc_proc)
                                        <span>💧 {{ $step->wilgotnosc_proc }}%</span>
                                    @endif
                                </div>
                            </div>

                            @if($step->opis)
                                <p class="text-sm text-gray-700 mb-3">{{ $step->opis }}</p>
                            @endif

                            {{-- Składniki w tym procesie --}}
                            @if($step->materials->count() > 0)
                                <div class="mt-3 bg-gray-50 rounded-lg p-3">
                                    <h5 class="text-xs font-medium text-gray-700 mb-2">🥄 Składniki w tym procesie:</h5>
                                    <div class="space-y-2">
                                        @foreach($step->materials->sortBy('pivot.kolejnosc') as $material)
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-2 text-xs">
                                                        <span class="font-medium text-gray-900">{{ $material->nazwa }}</span>
                                                        <span class="text-blue-600 font-semibold">{{ $material->pivot->ilosc }} {{ $material->pivot->jednostka }}</span>
                                                        @if($material->pivot->sposob_przygotowania)
                                                            <span class="text-gray-500">({{ $material->pivot->sposob_przygotowania }})</span>
                                                        @endif
                                                        @if($material->pivot->temperatura_c)
                                                            <span class="text-orange-600">{{ $material->pivot->temperatura_c }}°C</span>
                                                        @endif
                                                        @if($material->pivot->opcjonalny)
                                                            <span class="text-yellow-600">(opcjonalny)</span>
                                                        @endif
                                                    </div>

                                                    {{-- Zamienniki --}}
                                                    @if($material->pivot->has_substitutes && $material->pivot->substitutes)
                                                        @php
                                                            $substitutes = json_decode($material->pivot->substitutes, true) ?? [];
                                                        @endphp
                                                        @if(count($substitutes) > 0)
                                                            <div class="mt-1 pl-2 border-l-2 border-green-200">
                                                                <div class="text-xs text-green-700">
                                                                    <span class="font-medium">Zamienniki:</span>
                                                                    @foreach($substitutes as $substitute)
                                                                        <span class="ml-1">{{ $substitute['material_name'] }} ({{ $substitute['wspolczynnik_przeliczenia'] }}x)</span>@if(!$loop->last),@endif
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif

                                                    @if($material->pivot->uwagi)
                                                        <div class="text-xs text-gray-500 mt-1">{{ $material->pivot->uwagi }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Dodatkowe informacje o procesie --}}
                            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                @if($step->narzedzia)
                                    <div>
                                        <span class="font-medium text-gray-700">🔧 Narzędzia:</span>
                                        <span class="text-gray-600">{{ $step->narzedzia }}</span>
                                    </div>
                                @endif
                                @if($step->wskazowki)
                                    <div>
                                        <span class="font-medium text-gray-700">💡 Wskazówki:</span>
                                        <span class="text-gray-600">{{ $step->wskazowki }}</span>
                                    </div>
                                @endif
                                @if($step->kryteria_oceny)
                                    <div>
                                        <span class="font-medium text-gray-700">✅ Kryteria:</span>
                                        <span class="text-gray-600">{{ $step->kryteria_oceny }}</span>
                                    </div>
                                @endif
                                @if($step->czeste_bledy)
                                    <div>
                                        <span class="font-medium text-gray-700">⚠️ Częste błędy:</span>
                                        <span class="text-gray-600">{{ $step->czeste_bledy }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
</div>
