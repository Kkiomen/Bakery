<div x-data="{ activeTab: 'basic' }" class="max-w-4xl mx-auto space-y-6">
    {{-- Nagłówek --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dodaj nowy surowiec</h1>
            <p class="text-gray-600">Wprowadź dane nowego surowca do magazynu</p>
        </div>
        <div class="flex space-x-3">
            <button wire:click="cancel"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Anuluj
            </button>
            <button wire:click="save"
                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                Zapisz surowiec
            </button>
        </div>
    </div>

    {{-- Komunikaty --}}
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 rounded-md p-4">
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
        <div class="bg-red-50 border border-red-200 rounded-md p-4">
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

    <form wire:submit="save" class="space-y-8">
        {{-- Dane podstawowe --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Dane podstawowe</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kod --}}
                <div>
                    <label for="kod" class="block text-sm font-medium text-gray-700 mb-1">
                        Kod surowca <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="kod"
                           wire:model="kod"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('kod') border-red-300 @enderror"
                           placeholder="np. MAK-001">
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
                           wire:model="nazwa"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('nazwa') border-red-300 @enderror"
                           placeholder="np. Mąka pszenna typ 500">
                    @error('nazwa')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Typ --}}
                <div>
                    <label for="typ" class="block text-sm font-medium text-gray-700 mb-1">
                        Typ surowca <span class="text-red-500">*</span>
                    </label>
                    <select id="typ"
                            wire:model="typ"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('typ') border-red-300 @enderror">
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
                            wire:model="jednostka_podstawowa"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('jednostka_podstawowa') border-red-300 @enderror">
                        @foreach($units as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('jednostka_podstawowa')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Dostawca --}}
                <div>
                    <label for="dostawca" class="block text-sm font-medium text-gray-700 mb-1">Dostawca</label>
                    <input type="text"
                           id="dostawca"
                           wire:model="dostawca"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('dostawca') border-red-300 @enderror"
                           placeholder="Nazwa dostawcy">
                    @error('dostawca')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Waga opakowania --}}
                <div>
                    <label for="waga_opakowania" class="block text-sm font-medium text-gray-700 mb-1">Waga opakowania</label>
                    <input type="number"
                           id="waga_opakowania"
                           wire:model="waga_opakowania"
                           min="0"
                           step="0.001"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('waga_opakowania') border-red-300 @enderror"
                           placeholder="25">
                    @error('waga_opakowania')
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
                              placeholder="Dodatkowy opis surowca..."></textarea>
                    @error('opis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Stany magazynowe --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Stany magazynowe</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Stan aktualny --}}
                <div>
                    <label for="stan_aktualny" class="block text-sm font-medium text-gray-700 mb-1">
                        Stan aktualny <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           id="stan_aktualny"
                           wire:model="stan_aktualny"
                           min="0"
                           step="0.001"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('stan_aktualny') border-red-300 @enderror"
                           placeholder="100">
                    @error('stan_aktualny')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Stan minimalny --}}
                <div>
                    <label for="stan_minimalny" class="block text-sm font-medium text-gray-700 mb-1">
                        Stan minimalny <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           id="stan_minimalny"
                           wire:model="stan_minimalny"
                           min="0"
                           step="0.001"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('stan_minimalny') border-red-300 @enderror"
                           placeholder="10">
                    @error('stan_minimalny')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Stan optymalny --}}
                <div>
                    <label for="stan_optymalny" class="block text-sm font-medium text-gray-700 mb-1">
                        Stan optymalny <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           id="stan_optymalny"
                           wire:model="stan_optymalny"
                           min="0"
                           step="0.001"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('stan_optymalny') border-red-300 @enderror"
                           placeholder="50">
                    @error('stan_optymalny')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Ceny i VAT --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Ceny i VAT</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Cena zakupu w złotych --}}
                <div>
                    <label for="cena_zakupu_zl" class="block text-sm font-medium text-gray-700 mb-1">Cena zakupu (zł)</label>
                    <input type="number"
                           id="cena_zakupu_zl"
                           wire:model="cena_zakupu_zl"
                           min="0"
                           step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('cena_zakupu_zl') border-red-300 @enderror"
                           placeholder="5.50">
                    @if($cena_zakupu_gr)
                        <p class="mt-1 text-sm text-gray-500">{{ $cena_zakupu_gr }} gr</p>
                    @endif
                    @error('cena_zakupu_zl')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Stawka VAT --}}
                <div>
                    <label for="stawka_vat" class="block text-sm font-medium text-gray-700 mb-1">
                        Stawka VAT (%) <span class="text-red-500">*</span>
                    </label>
                    <select id="stawka_vat"
                            wire:model="stawka_vat"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('stawka_vat') border-red-300 @enderror">
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

        {{-- Ważność i dostawa --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Ważność i dostawa</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Dni ważności --}}
                <div>
                    <label for="dni_waznosci" class="block text-sm font-medium text-gray-700 mb-1">Dni ważności</label>
                    <input type="number"
                           id="dni_waznosci"
                           wire:model="dni_waznosci"
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('dni_waznosci') border-red-300 @enderror"
                           placeholder="365">
                    @error('dni_waznosci')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Data ostatniej dostawy --}}
                <div>
                    <label for="data_ostatniej_dostawy" class="block text-sm font-medium text-gray-700 mb-1">Data ostatniej dostawy</label>
                    <input type="date"
                           id="data_ostatniej_dostawy"
                           wire:model="data_ostatniej_dostawy"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('data_ostatniej_dostawy') border-red-300 @enderror">
                    @error('data_ostatniej_dostawy')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Uwagi i status --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Uwagi i status</h2>

            <div class="space-y-6">
                {{-- Uwagi --}}
                <div>
                    <label for="uwagi" class="block text-sm font-medium text-gray-700 mb-1">Uwagi</label>
                    <textarea id="uwagi"
                              wire:model="uwagi"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('uwagi') border-red-300 @enderror"
                              placeholder="Dodatkowe uwagi dotyczące surowca..."></textarea>
                    @error('uwagi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="flex items-center">
                        <input type="checkbox"
                               wire:model="aktywny"
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Surowiec aktywny</span>
                    </label>
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
                Zapisz surowiec
            </button>
        </div>
    </form>
</div>
