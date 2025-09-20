<div x-data="{ activeTab: 'basic' }" class="max-w-6xl mx-auto">
    <form wire:submit="save" class="space-y-6">
        {{-- Nagłówek --}}
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $isEditing ? 'Edytuj recepturę' : 'Dodaj nową recepturę' }}
                    </h1>
                    <p class="text-gray-600">
                        {{ $isEditing ? 'Zaktualizuj przepis i procesy technologiczne' : 'Utwórz nowy przepis z procesami technologicznymi' }}
                    </p>
                </div>
                @if($isEditing)
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Kod receptury</div>
                        <div class="text-lg font-mono font-semibold">{{ $kod }}</div>
                    </div>
                @endif
            </div>

            {{-- Zakładki --}}
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button type="button"
                            @click="activeTab = 'basic'"
                            :class="activeTab === 'basic' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        📋 Dane podstawowe
                    </button>
                    <button type="button"
                            @click="activeTab = 'steps'"
                            :class="activeTab === 'steps' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        ⚙️ Procesy ({{ count($steps) }})
                    </button>
                      <button type="button"
                              @click="activeTab = 'summary'"
                              :class="activeTab === 'summary' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                              class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                          📊 Podsumowanie składników
                      </button>
                      <button type="button"
                              @click="activeTab = 'costs'"
                              :class="activeTab === 'costs' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                              class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                          💰 Analiza kosztów
                      </button>
                    <button type="button"
                            @click="activeTab = 'details'"
                            :class="activeTab === 'details' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        📝 Szczegóły
                    </button>
                </nav>
            </div>

            {{-- Zakładka: Dane podstawowe --}}
            <div x-show="activeTab === 'basic'" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Kod receptury --}}
                    <div>
                        <label for="kod" class="block text-sm font-medium text-gray-700 mb-1">
                            Kod receptury <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="kod"
                               wire:model.blur="kod"
                               placeholder="np. REC-BULKA-001"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('kod') border-red-500 @enderror">
                        @error('kod')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nazwa --}}
                    <div>
                        <label for="nazwa" class="block text-sm font-medium text-gray-700 mb-1">
                            Nazwa receptury <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="nazwa"
                               wire:model.blur="nazwa"
                               placeholder="np. Bułki pszenne klasyczne"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('nazwa') border-red-500 @enderror">
                        @error('nazwa')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kategoria --}}
                    <div>
                        <label for="kategoria" class="block text-sm font-medium text-gray-700 mb-1">
                            Kategoria <span class="text-red-500">*</span>
                        </label>
                        <select id="kategoria"
                                wire:model.blur="kategoria"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('kategoria') border-red-500 @enderror">
                            <option value="">Wybierz kategorię</option>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('kategoria')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Poziom trudności --}}
                    <div>
                        <label for="poziom_trudnosci" class="block text-sm font-medium text-gray-700 mb-1">
                            Poziom trudności <span class="text-red-500">*</span>
                        </label>
                        <select id="poziom_trudnosci"
                                wire:model.blur="poziom_trudnosci"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('poziom_trudnosci') border-red-500 @enderror">
                            @foreach($difficulties as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('poziom_trudnosci')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ilość porcji --}}
                    <div>
                        <label for="ilosc_porcji" class="block text-sm font-medium text-gray-700 mb-1">
                            Ilość porcji <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               id="ilosc_porcji"
                               wire:model.blur="ilosc_porcji"
                               min="1"
                               placeholder="20"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('ilosc_porcji') border-red-500 @enderror">
                        @error('ilosc_porcji')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Waga jednostkowa --}}
                    <div>
                        <label for="waga_jednostkowa_g" class="block text-sm font-medium text-gray-700 mb-1">
                            Waga jednostkowa (g)
                        </label>
                        <input type="number"
                               id="waga_jednostkowa_g"
                               wire:model.blur="waga_jednostkowa_g"
                               step="0.1"
                               min="0"
                               placeholder="60.0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('waga_jednostkowa_g') border-red-500 @enderror">
                        @error('waga_jednostkowa_g')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Autor --}}
                    <div>
                        <label for="autor" class="block text-sm font-medium text-gray-700 mb-1">
                            Autor
                        </label>
                        <input type="text"
                               id="autor"
                               wire:model.blur="autor"
                               placeholder="Mistrz Piekarz"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('autor') border-red-500 @enderror">
                        @error('autor')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Wersja --}}
                    <div>
                        <label for="wersja" class="block text-sm font-medium text-gray-700 mb-1">
                            Wersja <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="wersja"
                               wire:model.blur="wersja"
                               placeholder="1.0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('wersja') border-red-500 @enderror">
                        @error('wersja')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Opis --}}
                <div>
                    <label for="opis" class="block text-sm font-medium text-gray-700 mb-1">
                        Opis receptury
                    </label>
                    <textarea id="opis"
                              wire:model.blur="opis"
                              rows="3"
                              placeholder="Tradycyjne bułki pszenne na drożdżach..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('opis') border-red-500 @enderror"></textarea>
                    @error('opis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Czasy --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="czas_przygotowania_min" class="block text-sm font-medium text-gray-700 mb-1">
                            Czas przygotowania (min)
                        </label>
                        <input type="number"
                               id="czas_przygotowania_min"
                               wire:model.blur="czas_przygotowania_min"
                               min="0"
                               placeholder="30"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="czas_wypiekania_min" class="block text-sm font-medium text-gray-700 mb-1">
                            Czas wypiekania (min)
                        </label>
                        <input type="number"
                               id="czas_wypiekania_min"
                               wire:model.blur="czas_wypiekania_min"
                               min="0"
                               placeholder="15"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="czas_calkowity_min" class="block text-sm font-medium text-gray-700 mb-1">
                            Czas całkowity (min)
                        </label>
                        <input type="number"
                               id="czas_calkowity_min"
                               wire:model.blur="czas_calkowity_min"
                               min="0"
                               placeholder="180"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- Parametry wypiekania --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="temperatura_c" class="block text-sm font-medium text-gray-700 mb-1">
                            Temperatura wypiekania (°C)
                        </label>
                        <input type="number"
                               id="temperatura_c"
                               wire:model.blur="temperatura_c"
                               min="0"
                               max="300"
                               placeholder="220"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="flex items-center space-x-4 pt-6">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   wire:model="aktywny"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-900">Receptura aktywna</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox"
                                   wire:model="testowany"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-900">Przetestowana</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Zakładka: Procesy --}}
            <div x-show="activeTab === 'steps'" class="space-y-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Procesy technologiczne</h3>
                        <p class="text-sm text-gray-600">Każdy proces może mieć swoje składniki dodawane w odpowiednim momencie</p>
                    </div>
                    <button type="button"
                            wire:click="openStepModal"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Dodaj proces
                    </button>
                </div>

                @if(count($steps) > 0)
                    <div class="space-y-4">
                        @foreach($steps as $index => $step)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                                {{ $index + 1 }}
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2">
                                                    <h4 class="text-sm font-medium text-gray-900">{{ $step['name'] }}</h4>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $stepTypes[$step['type']] ?? $step['type'] }}
                                                    </span>
                                                    @if(!$step['required'])
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            opcjonalny
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="mt-1 text-sm text-gray-600">{{ $step['description'] }}</p>
                                                <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                                                    @if($step['time'])
                                                        <span>⏱️ {{ $step['time'] }} min</span>
                                                    @endif
                                                    @if($step['temperature'])
                                                        <span>🔥 {{ $step['temperature'] }}°C</span>
                                                    @endif
                                                    @if($step['humidity'])
                                                        <span>💧 {{ $step['humidity'] }}%</span>
                                                    @endif
                                                    @if($step['tools'])
                                                        <span>🔧 {{ $step['tools'] }}</span>
                                                    @endif
                                                </div>

                                                {{-- Składniki w tym procesie --}}
                                                @if(isset($step['materials']) && count($step['materials']) > 0)
                                                    <div class="mt-3 bg-gray-50 rounded-lg p-3">
                                                        <h5 class="text-xs font-medium text-gray-700 mb-2">🥄 Składniki w tym procesie:</h5>
                                                        <div class="space-y-1">
                                                            @foreach($step['materials'] as $materialIndex => $material)
                                                                <div class="flex items-start justify-between text-xs">
                                                                    <div class="flex-1">
                                                                        <div class="flex items-center space-x-2">
                                                                            <span class="font-medium text-gray-900">{{ $material['material_name'] }}</span>
                                                                            <span class="text-blue-600 font-semibold">{{ $material['amount'] }} {{ $material['unit'] }}</span>
                                                                            @if($material['preparation'])
                                                                                <span class="text-gray-500">({{ $material['preparation'] }})</span>
                                                                            @endif
                                                                            @if($material['temperature'])
                                                                                <span class="text-orange-600">{{ $material['temperature'] }}°C</span>
                                                                            @endif
                                                                            @if(isset($material['has_substitutes']) && $material['has_substitutes'])
                                                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                                                    {{ count($material['substitutes']) }} zamiennik(ów)
                                                                                </span>
                                                                            @endif
                                                                        </div>

                                                                        {{-- Pokaż zamienniki jeśli istnieją --}}
                                                                        @if(isset($material['substitutes']) && count($material['substitutes']) > 0)
                                                                            <div class="mt-1 pl-2 border-l-2 border-green-200">
                                                                                @foreach($material['substitutes'] as $substitute)
                                                                                    <div class="text-xs text-green-700">
                                                                                        <span class="font-medium">{{ $substitute['material_name'] }}</span>
                                                                                        <span class="text-green-600">({{ $substitute['wspolczynnik_przeliczenia'] }}x)</span>
                                                                                        @if($substitute['uwagi'])
                                                                                            <span class="text-gray-500">- {{ $substitute['uwagi'] }}</span>
                                                                                        @endif
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="flex items-center space-x-1">
                                                                        <button type="button"
                                                                                wire:click="openSubstituteModal({{ $index }}, {{ $materialIndex }})"
                                                                                class="text-green-400 hover:text-green-600"
                                                                                title="Zarządzaj zamiennikami">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                                                            </svg>
                                                                        </button>
                                                                        <button type="button"
                                                                                wire:click="removeStepMaterial({{ $index }}, {{ $materialIndex }})"
                                                                                class="text-red-400 hover:text-red-600">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 ml-4">
                                        {{-- Dodaj składnik do procesu --}}
                                        <button type="button"
                                                wire:click="openStepMaterialModal({{ $index }})"
                                                class="p-1 text-green-400 hover:text-green-600"
                                                title="Dodaj składnik">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </button>

                                        @if($index > 0)
                                            <button type="button"
                                                    wire:click="moveStepUp({{ $index }})"
                                                    class="p-1 text-gray-400 hover:text-gray-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        @if($index < count($steps) - 1)
                                            <button type="button"
                                                    wire:click="moveStepDown({{ $index }})"
                                                    class="p-1 text-gray-400 hover:text-gray-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        <button type="button"
                                                wire:click="editStep({{ $index }})"
                                                class="p-1 text-blue-400 hover:text-blue-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button type="button"
                                                wire:click="removeStep({{ $index }})"
                                                class="p-1 text-red-400 hover:text-red-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Brak procesów</h3>
                        <p class="mt-1 text-sm text-gray-500">Dodaj procesy technologiczne (przygotowanie, mieszanie, wyrastanie, wypiek, etc.).</p>
                        <p class="mt-1 text-xs text-gray-400">Składniki będą dodawane do konkretnych procesów w odpowiednim momencie.</p>
                    </div>
                @endif
            </div>

            {{-- Zakładka: Podsumowanie składników --}}
            <div x-show="activeTab === 'summary'" class="space-y-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Podsumowanie wszystkich składników</h3>
                        <p class="text-sm text-gray-600">Lista wszystkich surowców potrzebnych do wykonania receptury</p>
                    </div>
                </div>

                @php
                    $allMaterials = $this->getAllMaterials();
                @endphp

                @if($allMaterials->count() > 0)
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                            <h4 class="text-sm font-medium text-gray-900">Lista zakupów</h4>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @foreach($allMaterials as $material)
                                <div class="p-4">
                                              <div class="flex items-start justify-between">
                                                  <div class="flex-1">
                                                      <div class="flex items-center space-x-3">
                                                          <h5 class="text-sm font-medium text-gray-900">{{ $material['material_name'] }}</h5>
                                                          <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                              Łącznie: {{ $material['total_amount'] }} {{ $material['unit'] }}
                                                          </span>
                                                          @if(isset($material['has_substitutes']) && $material['has_substitutes'])
                                                              <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                                  {{ count($material['substitutes']) }} zamiennik(ów)
                                                              </span>
                                                          @endif
                                                      </div>

                                                      {{-- Pokaż zamienniki w podsumowaniu --}}
                                                      @if(isset($material['substitutes']) && count($material['substitutes']) > 0)
                                                          <div class="mt-2 pl-3 border-l-2 border-green-200">
                                                              <h6 class="text-xs font-medium text-green-800 mb-1">Dostępne zamienniki:</h6>
                                                              @foreach($material['substitutes'] as $substitute)
                                                                  <div class="text-xs text-green-700 mb-1">
                                                                      <span class="font-medium">{{ $substitute['material_name'] }}</span>
                                                                      <span class="text-green-600">({{ $substitute['wspolczynnik_przeliczenia'] }}x)</span>
                                                                      @if($substitute['uwagi'])
                                                                          <span class="text-gray-500">- {{ $substitute['uwagi'] }}</span>
                                                                      @endif
                                                                  </div>
                                                              @endforeach
                                                          </div>
                                                      @endif

                                            {{-- Szczegóły użycia w procesach --}}
                                            <div class="mt-2 space-y-1">
                                                @foreach($material['usages'] as $usage)
                                                    <div class="flex items-center space-x-2 text-xs text-gray-600">
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                            Proces {{ $usage['step_index'] + 1 }}
                                                        </span>
                                                        <span>{{ $usage['step_name'] }}:</span>
                                                        <span class="font-semibold text-blue-600">{{ $usage['amount'] }} {{ $usage['unit'] }}</span>
                                                        @if($usage['preparation'])
                                                            <span class="text-gray-500">({{ $usage['preparation'] }})</span>
                                                        @endif
                                                        @if($usage['temperature'])
                                                            <span class="text-orange-600">{{ $usage['temperature'] }}°C</span>
                                                        @endif
                                                        @if($usage['optional'])
                                                            <span class="text-yellow-600">(opcjonalny)</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Statystyki --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Różnych składników</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ $allMaterials->count() }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Procesów z składnikami</div>
                                    <div class="text-2xl font-bold text-gray-900">
                                        {{ collect($steps)->filter(function($step) { return isset($step['materials']) && count($step['materials']) > 0; })->count() }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-yellow-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Składników opcjonalnych</div>
                                    <div class="text-2xl font-bold text-gray-900">
                                        {{ $allMaterials->sum(function($material) { return collect($material['usages'])->where('optional', true)->count(); }) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Brak składników</h3>
                        <p class="mt-1 text-sm text-gray-500">Dodaj składniki do procesów, aby zobaczyć podsumowanie.</p>
                    </div>
                @endif
            </div>

              {{-- Zakładka: Analiza kosztów --}}
              <div x-show="activeTab === 'costs'" class="space-y-6">
                  <div class="flex justify-between items-center">
                      <div>
                          <h3 class="text-lg font-medium text-gray-900">Analiza kosztów receptury</h3>
                          <p class="text-sm text-gray-600">Szczegółowa analiza kosztów składników i propozycje oszczędności</p>
                      </div>
                  </div>

                  @php
                      $costAnalysis = $this->getCostAnalysis();
                  @endphp

                  {{-- Podsumowanie kosztów --}}
                  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                      <div class="bg-blue-50 rounded-lg p-4">
                          <div class="flex items-center">
                              <div class="flex-shrink-0">
                                  <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                  </svg>
                              </div>
                              <div class="ml-4">
                                  <div class="text-sm font-medium text-gray-500">Koszt całkowity</div>
                                  <div class="text-2xl font-bold text-gray-900">{{ number_format($costAnalysis['total_cost'], 2) }} zł</div>
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
                                  <div class="text-2xl font-bold text-gray-900">{{ number_format($costAnalysis['cost_per_portion'], 2) }} zł</div>
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
                                  <div class="text-2xl font-bold text-gray-900">{{ number_format($costAnalysis['cost_per_100g'], 2) }} zł</div>
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
                                  <div class="text-2xl font-bold text-gray-900">{{ number_format($costAnalysis['total_weight'], 0) }}g</div>
                              </div>
                          </div>
                      </div>
                  </div>

                  {{-- Szczegółowe koszty składników --}}
                  @if(count($costAnalysis['material_costs']) > 0)
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
                                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zamienniki</th>
                                      </tr>
                                  </thead>
                                  <tbody class="bg-white divide-y divide-gray-200">
                                      @foreach($costAnalysis['material_costs'] as $cost)
                                          <tr>
                                              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                  {{ $cost['material_name'] }}
                                              </td>
                                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                  {{ $cost['amount'] }} {{ $cost['unit'] }}
                                                  @if($cost['base_amount'] != $cost['amount'])
                                                      <br><span class="text-xs text-gray-400">({{ number_format($cost['base_amount'], 2) }} {{ $cost['base_unit'] }})</span>
                                                  @endif
                                              </td>
                                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                  {{ number_format($cost['price_per_unit'], 2) }} zł/{{ $cost['base_unit'] }}
                                              </td>
                                              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                  {{ number_format($cost['total_cost'], 2) }} zł
                                              </td>
                                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                  {{ number_format(($cost['total_cost'] / $costAnalysis['total_cost']) * 100, 1) }}%
                                              </td>
                                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                  @if($cost['has_substitutes'])
                                                      <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                          {{ count($cost['substitutes']) }} dostępne
                                                      </span>
                                                  @else
                                                      <span class="text-gray-400">Brak</span>
                                                  @endif
                                              </td>
                                          </tr>
                                      @endforeach
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  @endif

                  {{-- Propozycje oszczędności z zamienników --}}
                  @if(count($costAnalysis['substitute_savings']) > 0)
                      <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                          <div class="px-4 py-3 bg-green-50 border-b border-gray-200">
                              <h4 class="text-sm font-medium text-gray-900">💡 Propozycje oszczędności z zamienników</h4>
                              <p class="text-xs text-gray-600 mt-1">Potencjalne oszczędności przy użyciu zamienników</p>
                          </div>
                          <div class="divide-y divide-gray-200">
                              @foreach($costAnalysis['substitute_savings'] as $saving)
                                  <div class="p-4 {{ $saving['savings'] > 0 ? 'bg-green-50' : 'bg-red-50' }}">
                                      <div class="flex items-start justify-between">
                                          <div class="flex-1">
                                              <div class="flex items-center space-x-2">
                                                  <span class="font-medium text-gray-900">{{ $saving['original_material'] }}</span>
                                                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                                  </svg>
                                                  <span class="font-medium text-gray-900">{{ $saving['substitute_material'] }}</span>
                                              </div>
                                              <div class="mt-1 text-sm text-gray-600">
                                                  <span>Współczynnik: {{ $saving['conversion_factor'] }}x</span>
                                                  @if($saving['notes'])
                                                      <span class="ml-2">• {{ $saving['notes'] }}</span>
                                                  @endif
                                              </div>
                                              <div class="mt-2 flex items-center space-x-4 text-sm">
                                                  <span class="text-gray-600">Koszt oryginalny: <strong>{{ number_format($saving['original_cost'], 2) }} zł</strong></span>
                                                  <span class="text-gray-600">Koszt zamiennika: <strong>{{ number_format($saving['substitute_cost'], 2) }} zł</strong></span>
                                              </div>
                                          </div>
                                          <div class="ml-4 text-right">
                                              @if($saving['savings'] > 0)
                                                  <div class="text-lg font-bold text-green-600">
                                                      -{{ number_format($saving['savings'], 2) }} zł
                                                  </div>
                                                  <div class="text-sm text-green-600">
                                                      ({{ number_format($saving['savings_percent'], 1) }}% taniej)
                                                  </div>
                                              @else
                                                  <div class="text-lg font-bold text-red-600">
                                                      +{{ number_format(abs($saving['savings']), 2) }} zł
                                                  </div>
                                                  <div class="text-sm text-red-600">
                                                      ({{ number_format(abs($saving['savings_percent']), 1) }}% drożej)
                                                  </div>
                                              @endif
                                          </div>
                                      </div>
                                  </div>
                              @endforeach
                          </div>

                          {{-- Podsumowanie oszczędności --}}
                          @php
                              $totalSavings = collect($costAnalysis['substitute_savings'])->sum('savings');
                              $bestSavings = collect($costAnalysis['substitute_savings'])->where('savings', '>', 0);
                          @endphp
                          @if($bestSavings->count() > 0)
                              <div class="px-4 py-3 bg-green-100 border-t border-green-200">
                                  <div class="flex items-center justify-between">
                                      <div>
                                          <h5 class="text-sm font-medium text-green-900">Maksymalne oszczędności</h5>
                                          <p class="text-xs text-green-700">Przy użyciu najkorzystniejszych zamienników</p>
                                      </div>
                                      <div class="text-right">
                                          <div class="text-lg font-bold text-green-900">
                                              -{{ number_format($bestSavings->sum('savings'), 2) }} zł
                                          </div>
                                          <div class="text-sm text-green-700">
                                              ({{ number_format(($bestSavings->sum('savings') / $costAnalysis['total_cost']) * 100, 1) }}% całości)
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          @endif
                      </div>
                  @else
                      <div class="text-center py-12 text-gray-500">
                          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                          </svg>
                          <h3 class="mt-2 text-sm font-medium text-gray-900">Brak zamienników</h3>
                          <p class="mt-1 text-sm text-gray-500">Dodaj zamienniki do składników, aby zobaczyć propozycje oszczędności.</p>
                      </div>
                  @endif
              </div>

              {{-- Zakładka: Szczegóły --}}
              <div x-show="activeTab === 'details'" class="space-y-6">
                <div>
                    <label for="instrukcje_wypiekania" class="block text-sm font-medium text-gray-700 mb-1">
                        Instrukcje wypiekania
                    </label>
                    <textarea id="instrukcje_wypiekania"
                              wire:model.blur="instrukcje_wypiekania"
                              rows="3"
                              placeholder="Piec z parą przez pierwsze 5 minut..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <div>
                    <label for="wskazowki" class="block text-sm font-medium text-gray-700 mb-1">
                        Wskazówki technologiczne
                    </label>
                    <textarea id="wskazowki"
                              wire:model.blur="wskazowki"
                              rows="3"
                              placeholder="Ważne jest odpowiednie wyrastanie ciasta..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <div>
                    <label for="uwagi" class="block text-sm font-medium text-gray-700 mb-1">
                        Uwagi
                    </label>
                    <textarea id="uwagi"
                              wire:model.blur="uwagi"
                              rows="3"
                              placeholder="Dodatkowe uwagi o recepturze..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>

            {{-- Przyciski akcji --}}
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button"
                        wire:click="cancel"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Anuluj
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                    {{ $isEditing ? 'Zaktualizuj recepturę' : 'Dodaj recepturę' }}
                </button>
            </div>
        </div>
    </form>

    {{-- Modal składników w procesie --}}
    @if($showStepMaterialModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Overlay tło -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeStepMaterialModal"></div>

                <!-- Modal content -->
                <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-10">
                    <form wire:submit="addStepMaterial">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Dodaj składnik do procesu
                            </h3>

                            <div class="space-y-4">
                                {{-- Wybór surowca --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Surowiec <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="selectedMaterialId"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('selectedMaterialId') border-red-500 @enderror">
                                        <option value="">Wybierz surowiec</option>
                                        @foreach($materials->groupBy('typ') as $type => $typeMaterials)
                                            <optgroup label="{{ $type }}">
                                                @foreach($typeMaterials as $material)
                                                    <option value="{{ $material->id }}">
                                                        {{ $material->nazwa }} ({{ $material->jednostka_podstawowa }})
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    @error('selectedMaterialId')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Ilość --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Ilość <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number"
                                               wire:model.blur="materialAmount"
                                               step="0.001"
                                               min="0.001"
                                               placeholder="1.0"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('materialAmount') border-red-500 @enderror">
                                        @error('materialAmount')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Jednostka <span class="text-red-500">*</span>
                                        </label>
                                        <select wire:model.blur="materialUnit"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <option value="g">g</option>
                                            <option value="kg">kg</option>
                                            <option value="ml">ml</option>
                                            <option value="l">l</option>
                                            <option value="szt">szt</option>
                                            <option value="opak">opak</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Sposób przygotowania --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Sposób przygotowania
                                    </label>
                                    <input type="text"
                                           wire:model.blur="materialPreparation"
                                           placeholder="np. roztopione, przesiane, w temperaturze pokojowej"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                {{-- Temperatura --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Temperatura (°C)
                                    </label>
                                    <input type="number"
                                           wire:model.blur="materialTemperature"
                                           min="0"
                                           max="100"
                                           placeholder="37"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                {{-- Uwagi --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Uwagi
                                    </label>
                                    <textarea wire:model.blur="materialNotes"
                                              rows="2"
                                              placeholder="Dodatkowe uwagi o składniku..."
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>

                                {{-- Opcjonalny --}}
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox"
                                               wire:model="materialOptional"
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-900">Składnik opcjonalny</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                                Dodaj składnik
                            </button>
                            <button type="button"
                                    wire:click="closeStepMaterialModal"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Anuluj
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal kroków --}}
    @if($showStepModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Overlay tło -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeStepModal"></div>

                <!-- Modal content -->
                <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full z-10">
                    <form wire:submit="addStep">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                {{ $editingStepIndex !== null ? 'Edytuj proces' : 'Dodaj proces' }}
                            </h3>

                            <div class="space-y-4">
                                {{-- Typ procesu --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Typ procesu <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="stepType"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('stepType') border-red-500 @enderror">
                                        <option value="">Wybierz typ procesu</option>
                                        @foreach($stepTypes as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('stepType')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Nazwa --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Nazwa procesu <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           wire:model.blur="stepName"
                                           placeholder="np. Pierwszy wzrost ciasta"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('stepName') border-red-500 @enderror">
                                    @error('stepName')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Opis --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Opis procesu <span class="text-red-500">*</span>
                                    </label>
                                    <textarea wire:model.blur="stepDescription"
                                              rows="3"
                                              placeholder="Szczegółowy opis jak wykonać ten proces..."
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('stepDescription') border-red-500 @enderror"></textarea>
                                    @error('stepDescription')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Parametry --}}
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Czas (min)
                                        </label>
                                        <input type="number"
                                               wire:model.blur="stepTime"
                                               min="0"
                                               placeholder="60"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Temperatura (°C)
                                        </label>
                                        <input type="number"
                                               wire:model.blur="stepTemperature"
                                               min="0"
                                               max="300"
                                               placeholder="28"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Wilgotność (%)
                                        </label>
                                        <input type="number"
                                               wire:model.blur="stepHumidity"
                                               min="0"
                                               max="100"
                                               placeholder="75"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>

                                {{-- Narzędzia --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Potrzebne narzędzia
                                    </label>
                                    <input type="text"
                                           wire:model.blur="stepTools"
                                           placeholder="np. miska, ściereczka, termometr"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                {{-- Wskazówki --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Wskazówki
                                    </label>
                                    <textarea wire:model.blur="stepTips"
                                              rows="2"
                                              placeholder="Praktyczne wskazówki do wykonania procesu..."
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>

                                {{-- Kryteria oceny --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Kryteria oceny
                                    </label>
                                    <textarea wire:model.blur="stepCriteria"
                                              rows="2"
                                              placeholder="Jak ocenić czy proces wykonano poprawnie..."
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>

                                {{-- Obowiązkowy --}}
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox"
                                               wire:model="stepRequired"
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-900">Proces obowiązkowy</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ $editingStepIndex !== null ? 'Zaktualizuj proces' : 'Dodaj proces' }}
                            </button>
                            <button type="button"
                                    wire:click="closeStepModal"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Anuluj
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal zamienników --}}
    @if($showSubstituteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Overlay tło -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeSubstituteModal"></div>

                <!-- Modal content -->
                <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full z-10">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            Zarządzaj zamiennikami składnika
                        </h3>

                        {{-- Obecne zamienniki --}}
                        @if(count($materialSubstitutes) > 0)
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Obecne zamienniki:</h4>
                                <div class="space-y-2">
                                    @foreach($materialSubstitutes as $index => $substitute)
                                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2">
                                                    <span class="font-medium text-gray-900">{{ $substitute['material_name'] }}</span>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                        Współczynnik: {{ $substitute['wspolczynnik_przeliczenia'] }}x
                                                    </span>
                                                </div>
                                                @if($substitute['uwagi'])
                                                    <p class="text-sm text-gray-600 mt-1">{{ $substitute['uwagi'] }}</p>
                                                @endif
                                            </div>
                                            <button type="button"
                                                    wire:click="removeSubstitute({{ $index }})"
                                                    class="text-red-400 hover:text-red-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Formularz dodawania zamiennika --}}
                        <form wire:submit="addSubstitute">
                            <div class="space-y-4">
                                <h4 class="text-sm font-medium text-gray-700">Dodaj nowy zamiennik:</h4>

                                {{-- Wybór materiału zamiennego --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Materiał zamiennik <span class="text-red-500">*</span>
                                    </label>
                                    @if($editingStepIndexForSubstitute !== null && $editingMaterialIndex !== null)
                                        @php
                                            $currentMaterialId = $steps[$editingStepIndexForSubstitute]['materials'][$editingMaterialIndex]['material_id'];
                                            $availableSubstitutes = $this->getAvailableSubstitutesForMaterial($currentMaterialId);
                                        @endphp
                                        <select wire:model.live="selectedSubstituteMaterialId"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('selectedSubstituteMaterialId') border-red-500 @enderror">
                                            <option value="">Wybierz zamiennik</option>
                                            @foreach($availableSubstitutes as $substitute)
                                                <option value="{{ $substitute->id }}">
                                                    {{ $substitute->nazwa }} ({{ $substitute->jednostka_podstawowa }})
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                    @error('selectedSubstituteMaterialId')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Współczynnik przeliczenia --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Współczynnik przeliczenia <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number"
                                           wire:model.blur="substituteConversionFactor"
                                           step="0.1"
                                           min="0.1"
                                           placeholder="1.0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('substituteConversionFactor') border-red-500 @enderror">
                                    <p class="mt-1 text-xs text-gray-500">
                                        Np. 1.0 = zamiana 1:1, 0.3 = drożdże świeże → suche, 3.0 = drożdże suche → świeże
                                    </p>
                                    @error('substituteConversionFactor')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Uwagi --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Uwagi
                                    </label>
                                    <textarea wire:model.blur="substituteNotes"
                                              rows="2"
                                              placeholder="Dodatkowe uwagi o zamieniu..."
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end space-x-3">
                                <button type="button"
                                        wire:click="closeSubstituteModal"
                                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    Zamknij
                                </button>
                                <button type="submit"
                                        class="px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700">
                                    Dodaj zamiennik
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- JavaScript --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('recipe-saved', (message) => {
                console.log(message);
            });
        });
    </script>
</div>
