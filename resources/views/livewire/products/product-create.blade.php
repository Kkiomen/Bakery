<div x-data="productCreateForm()" class="max-w-4xl mx-auto space-y-6">
    {{-- Nagłówek --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dodaj nowy produkt</h1>
            <p class="text-gray-600">Wprowadź dane nowego produktu do systemu</p>
        </div>
        <div class="flex space-x-3">
            <button wire:click="cancel"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Anuluj
            </button>
            <button wire:click="save"
                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                Zapisz produkt
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
                {{-- SKU --}}
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">
                        SKU <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="sku"
                           wire:model="sku" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('sku') border-red-300 @enderror"
                           placeholder="np. CHLEB001">
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- EAN --}}
                <div>
                    <label for="ean" class="block text-sm font-medium text-gray-700 mb-1">EAN</label>
                    <input type="text" 
                           id="ean"
                           wire:model="ean" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('ean') border-red-300 @enderror"
                           placeholder="np. 1234567890123">
                    @error('ean')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nazwa --}}
                <div class="md:col-span-2">
                    <label for="nazwa" class="block text-sm font-medium text-gray-700 mb-1">
                        Nazwa produktu <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="nazwa"
                           wire:model="nazwa" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('nazwa') border-red-300 @enderror"
                           placeholder="np. Chleb żytni">
                    @error('nazwa')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kategoria --}}
                <div>
                    <label for="kategoria_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Kategoria <span class="text-red-500">*</span>
                    </label>
                    <select id="kategoria_id"
                            wire:model="kategoria_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('kategoria_id') border-red-300 @enderror">
                        <option value="">Wybierz kategorię</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->nazwa }}</option>
                        @endforeach
                    </select>
                    @error('kategoria_id')
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
                              placeholder="Opis produktu..."></textarea>
                    @error('opis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Waga i jednostki --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Waga i jednostki</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Waga --}}
                <div>
                    <label for="waga_g" class="block text-sm font-medium text-gray-700 mb-1">
                        Waga (g) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="waga_g"
                           wire:model="waga_g" 
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('waga_g') border-red-300 @enderror"
                           placeholder="500">
                    @if($wagaKgDisplay)
                        <p class="mt-1 text-sm text-gray-500">{{ $wagaKgDisplay }}</p>
                    @endif
                    @error('waga_g')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jednostka sprzedaży --}}
                <div>
                    <label for="jednostka_sprzedazy" class="block text-sm font-medium text-gray-700 mb-1">
                        Jednostka sprzedaży <span class="text-red-500">*</span>
                    </label>
                    <select id="jednostka_sprzedazy"
                            wire:model="jednostka_sprzedazy" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('jednostka_sprzedazy') border-red-300 @enderror">
                        <option value="szt">sztuka</option>
                        <option value="opak">opakowanie</option>
                        <option value="kg">kilogram</option>
                    </select>
                    @error('jednostka_sprzedazy')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Zawartość opakowania --}}
                <div>
                    <label for="zawartosc_opakowania" class="block text-sm font-medium text-gray-700 mb-1">Zawartość opakowania</label>
                    <input type="number" 
                           id="zawartosc_opakowania"
                           wire:model="zawartosc_opakowania" 
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('zawartosc_opakowania') border-red-300 @enderror"
                           placeholder="1">
                    @error('zawartosc_opakowania')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Alergeny --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Alergeny</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($availableAllergens as $key => $label)
                    <label class="flex items-center">
                        <input type="checkbox" 
                               wire:click="toggleAllergen('{{ $key }}')"
                               @if(in_array($key, $alergeny)) checked @endif
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Wartości odżywcze --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Wartości odżywcze (na 100g)</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <label for="kcal" class="block text-sm font-medium text-gray-700 mb-1">Kalorie (kcal)</label>
                    <input type="number" 
                           id="kcal"
                           wire:model="kcal" 
                           min="0"
                           step="0.1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('kcal') border-red-300 @enderror">
                    @error('kcal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bialko_g" class="block text-sm font-medium text-gray-700 mb-1">Białko (g)</label>
                    <input type="number" 
                           id="bialko_g"
                           wire:model="bialko_g" 
                           min="0"
                           step="0.1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('bialko_g') border-red-300 @enderror">
                    @error('bialko_g')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tluszcz_g" class="block text-sm font-medium text-gray-700 mb-1">Tłuszcz (g)</label>
                    <input type="number" 
                           id="tluszcz_g"
                           wire:model="tluszcz_g" 
                           min="0"
                           step="0.1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('tluszcz_g') border-red-300 @enderror">
                    @error('tluszcz_g')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="wegle_g" class="block text-sm font-medium text-gray-700 mb-1">Węglowodany (g)</label>
                    <input type="number" 
                           id="wegle_g"
                           wire:model="wegle_g" 
                           min="0"
                           step="0.1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('wegle_g') border-red-300 @enderror">
                    @error('wegle_g')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Ceny i VAT --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Ceny i VAT</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="cena_netto_gr" class="block text-sm font-medium text-gray-700 mb-1">
                        Cena netto (gr) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="cena_netto_gr"
                           wire:model="cena_netto_gr" 
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('cena_netto_gr') border-red-300 @enderror"
                           placeholder="500">
                    @if($cenaNettoDisplay)
                        <p class="mt-1 text-sm text-gray-500">{{ $cenaNettoDisplay }}</p>
                    @endif
                    @error('cena_netto_gr')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

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

        {{-- SEO --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">SEO</h2>
            
            <div class="space-y-6">
                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                    <input type="text" 
                           id="meta_title"
                           wire:model="meta_title" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('meta_title') border-red-300 @enderror"
                           placeholder="Tytuł dla wyszukiwarek">
                    @error('meta_title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                    <textarea id="meta_description"
                              wire:model="meta_description" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('meta_description') border-red-300 @enderror"
                              placeholder="Opis dla wyszukiwarek..."></textarea>
                    @error('meta_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Status</h2>
            
            <div>
                <label class="flex items-center">
                    <input type="checkbox" 
                           wire:model="aktywny"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Produkt aktywny</span>
                </label>
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
                Zapisz produkt
            </button>
        </div>
    </form>
</div>

<script>
function productCreateForm() {
    return {
        // Możemy dodać tutaj dodatkową logikę JavaScript jeśli potrzeba
    }
}
</script>
