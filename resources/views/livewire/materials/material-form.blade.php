<div x-data="{ activeTab: 'basic' }" class="max-w-4xl mx-auto">
    <form wire:submit="save" class="space-y-6">
        {{-- Nagłówek --}}
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $isEditing ? 'Edytuj surowiec' : 'Dodaj nowy surowiec' }}
                    </h1>
                    <p class="text-gray-600">
                        {{ $isEditing ? 'Zaktualizuj informacje o surowcu' : 'Wprowadź dane nowego surowca do magazynu' }}
                    </p>
                </div>
                @if($isEditing)
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Kod surowca</div>
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
                        Dane podstawowe
                    </button>
                    <button type="button"
                            @click="activeTab = 'stock'"
                            :class="activeTab === 'stock' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Magazyn i ceny
                    </button>
                    <button type="button"
                            @click="activeTab = 'details'"
                            :class="activeTab === 'details' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Szczegóły
                    </button>
                </nav>
            </div>

            {{-- Zakładka: Dane podstawowe --}}
            <div x-show="activeTab === 'basic'" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Kod surowca --}}
                    <div>
                        <label for="kod" class="block text-sm font-medium text-gray-700 mb-1">
                            Kod surowca <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="kod"
                               wire:model.blur="kod"
                               placeholder="np. MAK-001"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('kod') border-red-500 @enderror">
                        @error('kod')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nazwa --}}
                    <div>
                        <label for="nazwa" class="block text-sm font-medium text-gray-700 mb-1">
                            Nazwa surowca <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="nazwa"
                               wire:model.blur="nazwa"
                               placeholder="np. Mąka pszenna typ 500"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('nazwa') border-red-500 @enderror">
                        @error('nazwa')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Typ surowca --}}
                    <div>
                        <label for="typ" class="block text-sm font-medium text-gray-700 mb-1">
                            Typ surowca <span class="text-red-500">*</span>
                        </label>
                        <select id="typ"
                                wire:model.blur="typ"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('typ') border-red-500 @enderror">
                            <option value="">Wybierz typ</option>
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('typ')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jednostka podstawowa --}}
                    <div>
                        <label for="jednostka_podstawowa" class="block text-sm font-medium text-gray-700 mb-1">
                            Jednostka podstawowa <span class="text-red-500">*</span>
                        </label>
                        <select id="jednostka_podstawowa"
                                wire:model.blur="jednostka_podstawowa"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('jednostka_podstawowa') border-red-500 @enderror">
                            @foreach($units as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('jednostka_podstawowa')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Waga opakowania --}}
                    <div>
                        <label for="waga_opakowania" class="block text-sm font-medium text-gray-700 mb-1">
                            Waga opakowania (kg)
                        </label>
                        <input type="number"
                               id="waga_opakowania"
                               wire:model.blur="waga_opakowania"
                               step="0.001"
                               placeholder="np. 25.0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('waga_opakowania') border-red-500 @enderror">
                        @error('waga_opakowania')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Dostawca --}}
                    <div>
                        <label for="dostawca" class="block text-sm font-medium text-gray-700 mb-1">
                            Dostawca
                        </label>
                        <input type="text"
                               id="dostawca"
                               wire:model.blur="dostawca"
                               placeholder="np. Młyny Polskie Sp. z o.o."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('dostawca') border-red-500 @enderror">
                        @error('dostawca')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Opis --}}
                <div>
                    <label for="opis" class="block text-sm font-medium text-gray-700 mb-1">
                        Opis
                    </label>
                    <textarea id="opis"
                              wire:model.blur="opis"
                              rows="3"
                              placeholder="Dodatkowe informacje o surowcu..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('opis') border-red-500 @enderror"></textarea>
                    @error('opis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Zakładka: Magazyn i ceny --}}
            <div x-show="activeTab === 'stock'" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Stan aktualny --}}
                    <div>
                        <label for="stan_aktualny" class="block text-sm font-medium text-gray-700 mb-1">
                            Stan aktualny <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number"
                                   id="stan_aktualny"
                                   wire:model.blur="stan_aktualny"
                                   step="0.001"
                                   min="0"
                                   placeholder="0.000"
                                   class="w-full px-3 py-2 pr-12 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('stan_aktualny') border-red-500 @enderror">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">{{ $jednostka_podstawowa }}</span>
                            </div>
                        </div>
                        @error('stan_aktualny')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Stan minimalny --}}
                    <div>
                        <label for="stan_minimalny" class="block text-sm font-medium text-gray-700 mb-1">
                            Stan minimalny <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number"
                                   id="stan_minimalny"
                                   wire:model.blur="stan_minimalny"
                                   step="0.001"
                                   min="0"
                                   placeholder="0.000"
                                   class="w-full px-3 py-2 pr-12 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('stan_minimalny') border-red-500 @enderror">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">{{ $jednostka_podstawowa }}</span>
                            </div>
                        </div>
                        @error('stan_minimalny')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Stan optymalny --}}
                    <div>
                        <label for="stan_optymalny" class="block text-sm font-medium text-gray-700 mb-1">
                            Stan optymalny <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number"
                                   id="stan_optymalny"
                                   wire:model.blur="stan_optymalny"
                                   step="0.001"
                                   min="0"
                                   placeholder="0.000"
                                   class="w-full px-3 py-2 pr-12 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('stan_optymalny') border-red-500 @enderror">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">{{ $jednostka_podstawowa }}</span>
                            </div>
                        </div>
                        @error('stan_optymalny')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Cena zakupu --}}
                    <div>
                        <label for="cena_zakupu_zl" class="block text-sm font-medium text-gray-700 mb-1">
                            Cena zakupu za {{ $jednostka_podstawowa }}
                        </label>
                        <div class="relative">
                            <input type="number"
                                   id="cena_zakupu_zl"
                                   wire:model.blur="cena_zakupu_zl"
                                   step="0.01"
                                   min="0"
                                   placeholder="0.00"
                                   class="w-full px-3 py-2 pr-12 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('cena_zakupu_gr') border-red-500 @enderror">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">zł</span>
                            </div>
                        </div>
                        @if($cena_zakupu_zl)
                            <p class="mt-1 text-xs text-gray-500">{{ number_format($cena_zakupu_gr) }} gr</p>
                        @endif
                        @error('cena_zakupu_gr')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Stawka VAT --}}
                    <div>
                        <label for="stawka_vat" class="block text-sm font-medium text-gray-700 mb-1">
                            Stawka VAT <span class="text-red-500">*</span>
                        </label>
                        <select id="stawka_vat"
                                wire:model.blur="stawka_vat"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('stawka_vat') border-red-500 @enderror">
                            <option value="0">0%</option>
                            <option value="5">5%</option>
                            <option value="8">8%</option>
                            <option value="23">23%</option>
                        </select>
                        @error('stawka_vat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Zakładka: Szczegóły --}}
            <div x-show="activeTab === 'details'" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Dni ważności --}}
                    <div>
                        <label for="dni_waznosci" class="block text-sm font-medium text-gray-700 mb-1">
                            Dni ważności
                        </label>
                        <input type="number"
                               id="dni_waznosci"
                               wire:model.blur="dni_waznosci"
                               min="1"
                               placeholder="np. 365"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('dni_waznosci') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Ile dni surowiec jest ważny od daty dostawy</p>
                        @error('dni_waznosci')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Data ostatniej dostawy --}}
                    <div>
                        <label for="data_ostatniej_dostawy" class="block text-sm font-medium text-gray-700 mb-1">
                            Data ostatniej dostawy
                        </label>
                        <input type="date"
                               id="data_ostatniej_dostawy"
                               wire:model.blur="data_ostatniej_dostawy"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('data_ostatniej_dostawy') border-red-500 @enderror">
                        @error('data_ostatniej_dostawy')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Uwagi --}}
                <div>
                    <label for="uwagi" class="block text-sm font-medium text-gray-700 mb-1">
                        Uwagi
                    </label>
                    <textarea id="uwagi"
                              wire:model.blur="uwagi"
                              rows="4"
                              placeholder="Dodatkowe uwagi, instrukcje przechowywania, itp..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('uwagi') border-red-500 @enderror"></textarea>
                    @error('uwagi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status aktywny --}}
                <div>
                    <div class="flex items-center">
                        <input type="checkbox"
                               id="aktywny"
                               wire:model="aktywny"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="aktywny" class="ml-2 block text-sm text-gray-900">
                            Surowiec aktywny
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Nieaktywne surowce nie będą widoczne w listach wyboru</p>
                </div>
            </div>

            {{-- Przyciski akcji --}}
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button"
                        wire:click="cancel"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Anuluj
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ $isEditing ? 'Zaktualizuj surowiec' : 'Dodaj surowiec' }}
                </button>
            </div>
        </div>
    </form>

    {{-- JavaScript --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('material-saved', (message) => {
                console.log(message);
            });
        });
    </script>
</div>
