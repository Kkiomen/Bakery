<div x-data="{ activeTab: 'basic' }" class="max-w-6xl mx-auto">
    <form wire:submit="save" class="space-y-6">
        {{-- Nagłówek --}}
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Dodaj nową recepturę</h1>
                    <p class="text-gray-600">Utwórz nowy przepis z procesami technologicznymi</p>
                </div>
                <div class="flex space-x-3">
                    <button type="button"
                            wire:click="cancel"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Anuluj
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                        Zapisz recepturę
                    </button>
                </div>
            </div>

            {{-- Komunikaty --}}
            @if (session()->has('success'))
                <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Zakładki --}}
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button type="button"
                            @click="activeTab = 'basic'"
                            :class="activeTab === 'basic' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Dane podstawowe
                    </button>
                    <button type="button"
                            @click="activeTab = 'timing'"
                            :class="activeTab === 'timing' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Czasy i parametry
                    </button>
                    <button type="button"
                            @click="activeTab = 'steps'"
                            :class="activeTab === 'steps' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Procesy technologiczne
                    </button>
                    <button type="button"
                            @click="activeTab = 'notes'"
                            :class="activeTab === 'notes' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Uwagi i wskazówki
                    </button>
                </nav>
            </div>
        </div>

        {{-- Dane podstawowe --}}
        <div x-show="activeTab === 'basic'" class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Dane podstawowe</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kod --}}
                <div>
                    <label for="kod" class="block text-sm font-medium text-gray-700 mb-1">
                        Kod receptury <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="kod"
                           wire:model="kod"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('kod') border-red-300 @enderror"
                           placeholder="np. REC-CHLEB-001">
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
                           wire:model="nazwa"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('nazwa') border-red-300 @enderror"
                           placeholder="np. Chleb żytni tradycyjny">
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
                            wire:model="kategoria"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('kategoria') border-red-300 @enderror">
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
                            wire:model="poziom_trudnosci"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('poziom_trudnosci') border-red-300 @enderror">
                        @foreach($difficulties as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('poziom_trudnosci')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Produkt docelowy --}}
                <div>
                    <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Produkt docelowy</label>
                    <select id="product_id"
                            wire:model="product_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('product_id') border-red-300 @enderror">
                        <option value="">Wybierz produkt (opcjonalne)</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->nazwa }} ({{ $product->sku }})</option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Autor --}}
                <div>
                    <label for="autor" class="block text-sm font-medium text-gray-700 mb-1">Autor</label>
                    <input type="text"
                           id="autor"
                           wire:model="autor"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('autor') border-red-300 @enderror"
                           placeholder="Imię i nazwisko autora">
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
                           wire:model="wersja"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('wersja') border-red-300 @enderror"
                           placeholder="np. 1.0">
                    @error('wersja')
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
                           wire:model="ilosc_porcji"
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('ilosc_porcji') border-red-300 @enderror"
                           placeholder="1">
                    @error('ilosc_porcji')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Waga jednostkowa --}}
                <div>
                    <label for="waga_jednostkowa_g" class="block text-sm font-medium text-gray-700 mb-1">Waga jednostkowa (g)</label>
                    <input type="number"
                           id="waga_jednostkowa_g"
                           wire:model="waga_jednostkowa_g"
                           min="0"
                           step="0.1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('waga_jednostkowa_g') border-red-300 @enderror"
                           placeholder="500">
                    @error('waga_jednostkowa_g')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Opis --}}
                <div class="md:col-span-2">
                    <label for="opis" class="block text-sm font-medium text-gray-700 mb-1">Opis</label>
                    <textarea id="opis"
                              wire:model="opis"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('opis') border-red-300 @enderror"
                              placeholder="Opis receptury..."></textarea>
                    @error('opis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Czasy i parametry --}}
        <div x-show="activeTab === 'timing'" class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Czasy i parametry wypiekania</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Czas przygotowania --}}
                <div>
                    <label for="czas_przygotowania_min" class="block text-sm font-medium text-gray-700 mb-1">Czas przygotowania (min)</label>
                    <input type="number"
                           id="czas_przygotowania_min"
                           wire:model="czas_przygotowania_min"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('czas_przygotowania_min') border-red-300 @enderror"
                           placeholder="30">
                    @error('czas_przygotowania_min')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Czas wypiekania --}}
                <div>
                    <label for="czas_wypiekania_min" class="block text-sm font-medium text-gray-700 mb-1">Czas wypiekania (min)</label>
                    <input type="number"
                           id="czas_wypiekania_min"
                           wire:model="czas_wypiekania_min"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('czas_wypiekania_min') border-red-300 @enderror"
                           placeholder="45">
                    @error('czas_wypiekania_min')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Czas całkowity --}}
                <div>
                    <label for="czas_calkowity_min" class="block text-sm font-medium text-gray-700 mb-1">Czas całkowity (min)</label>
                    <input type="number"
                           id="czas_calkowity_min"
                           wire:model="czas_calkowity_min"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('czas_calkowity_min') border-red-300 @enderror"
                           placeholder="180">
                    @error('czas_calkowity_min')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Temperatura --}}
                <div>
                    <label for="temperatura_c" class="block text-sm font-medium text-gray-700 mb-1">Temperatura (°C)</label>
                    <input type="number"
                           id="temperatura_c"
                           wire:model="temperatura_c"
                           min="0"
                           max="300"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('temperatura_c') border-red-300 @enderror"
                           placeholder="220">
                    @error('temperatura_c')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Instrukcje wypiekania --}}
                <div class="md:col-span-2">
                    <label for="instrukcje_wypiekania" class="block text-sm font-medium text-gray-700 mb-1">Instrukcje wypiekania</label>
                    <textarea id="instrukcje_wypiekania"
                              wire:model="instrukcje_wypiekania"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('instrukcje_wypiekania') border-red-300 @enderror"
                              placeholder="Szczegółowe instrukcje dotyczące wypiekania..."></textarea>
                    @error('instrukcje_wypiekania')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Procesy technologiczne --}}
        <div x-show="activeTab === 'steps'" class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-medium text-gray-900">Procesy technologiczne</h2>
                <button type="button"
                        wire:click="openStepModal"
                        class="px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700">
                    Dodaj proces
                </button>
            </div>

            @if(count($steps) > 0)
                <div class="space-y-4">
                    @foreach($steps as $index => $step)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $index + 1 }}
                                        </span>
                                        <h3 class="text-sm font-medium text-gray-900">{{ $step['name'] }}</h3>
                                        @if($step['type'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $stepTypes[$step['type']] ?? $step['type'] }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">{{ $step['description'] }}</p>

                                    @if(isset($step['materials']) && count($step['materials']) > 0)
                                        <div class="mt-3">
                                            <h4 class="text-xs font-medium text-gray-700 mb-2">Składniki:</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                @foreach($step['materials'] as $material)
                                                    <div class="flex items-center justify-between text-xs bg-gray-50 rounded px-2 py-1">
                                                        <span>{{ $material['material_name'] }}</span>
                                                        <span class="font-medium">{{ $material['amount'] }} {{ $material['unit'] }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center space-x-2 ml-4">
                                    <button type="button"
                                            wire:click="openStepMaterialModal({{ $index }})"
                                            class="text-green-600 hover:text-green-900 text-sm">
                                        Składniki
                                    </button>
                                    <button type="button"
                                            wire:click="editStep({{ $index }})"
                                            class="text-blue-600 hover:text-blue-900 text-sm">
                                        Edytuj
                                    </button>
                                    <button type="button"
                                            wire:click="removeStep({{ $index }})"
                                            class="text-red-600 hover:text-red-900 text-sm">
                                        Usuń
                                    </button>
                                    @if($index > 0)
                                        <button type="button"
                                                wire:click="moveStepUp({{ $index }})"
                                                class="text-gray-600 hover:text-gray-900 text-sm">
                                            ↑
                                        </button>
                                    @endif
                                    @if($index < count($steps) - 1)
                                        <button type="button"
                                                wire:click="moveStepDown({{ $index }})"
                                                class="text-gray-600 hover:text-gray-900 text-sm">
                                            ↓
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p>Brak procesów technologicznych. Dodaj pierwszy proces, aby rozpocząć.</p>
                </div>
            @endif
        </div>

        {{-- Uwagi i wskazówki --}}
        <div x-show="activeTab === 'notes'" class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Uwagi i wskazówki</h2>

            <div class="space-y-6">
                {{-- Uwagi --}}
                <div>
                    <label for="uwagi" class="block text-sm font-medium text-gray-700 mb-1">Uwagi</label>
                    <textarea id="uwagi"
                              wire:model="uwagi"
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('uwagi') border-red-300 @enderror"
                              placeholder="Dodatkowe uwagi dotyczące receptury..."></textarea>
                    @error('uwagi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Wskazówki --}}
                <div>
                    <label for="wskazowki" class="block text-sm font-medium text-gray-700 mb-1">Wskazówki</label>
                    <textarea id="wskazowki"
                              wire:model="wskazowki"
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('wskazowki') border-red-300 @enderror"
                              placeholder="Praktyczne wskazówki i porady..."></textarea>
                    @error('wskazowki')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox"
                                   wire:model="aktywny"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Receptura aktywna</span>
                        </label>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox"
                                   wire:model="testowany"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Receptura przetestowana</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Przyciski --}}
        <div class="flex justify-end space-x-3">
            <button type="button"
                    wire:click="cancel"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Anuluj
            </button>
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                Zapisz recepturę
            </button>
        </div>
    </form>

    {{-- Modal dodawania/edycji procesu --}}
    @if($showStepModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $editingStepIndex !== null ? 'Edytuj proces' : 'Dodaj nowy proces' }}
                    </h3>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Typ procesu</label>
                                <select wire:model="stepType" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Wybierz typ</option>
                                    @foreach($stepTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nazwa procesu</label>
                                <input type="text" wire:model="stepName" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Opis procesu</label>
                            <textarea wire:model="stepDescription" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Czas (min)</label>
                                <input type="number" wire:model="stepTime" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Temperatura (°C)</label>
                                <input type="number" wire:model="stepTemperature" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Wilgotność (%)</label>
                                <input type="number" wire:model="stepHumidity" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Narzędzia</label>
                            <input type="text" wire:model="stepTools" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="stepRequired" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Proces obowiązkowy</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" wire:click="closeStepModal" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Anuluj
                        </button>
                        <button type="button" wire:click="addStep" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                            {{ $editingStepIndex !== null ? 'Zaktualizuj' : 'Dodaj' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal dodawania składników do procesu --}}
    @if($showStepMaterialModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Dodaj składnik do procesu</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Składnik</label>
                            <select wire:model="selectedMaterialId" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Wybierz składnik</option>
                                @foreach($materials as $material)
                                    <option value="{{ $material->id }}">{{ $material->nazwa }} ({{ $material->typ }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ilość</label>
                                <input type="number" step="0.001" wire:model="materialAmount" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jednostka</label>
                                <input type="text" wire:model="materialUnit" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sposób przygotowania</label>
                            <input type="text" wire:model="materialPreparation" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="materialOptional" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Składnik opcjonalny</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" wire:click="closeStepMaterialModal" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Anuluj
                        </button>
                        <button type="button" wire:click="addStepMaterial" class="px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700">
                            Dodaj składnik
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
