<div x-data="productForm()" class="max-w-4xl mx-auto space-y-6">
    {{-- Nagłówek --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ $isEditing ? 'Edytuj produkt' : 'Dodaj nowy produkt' }}
            </h1>
            <p class="text-gray-600">
                {{ $isEditing ? 'Zaktualizuj informacje o produkcie' : 'Wprowadź dane nowego produktu' }}
            </p>
        </div>
        <div class="flex space-x-3">
            <button wire:click="cancel"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Anuluj
            </button>
            <button wire:click="save"
                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                {{ $isEditing ? 'Zaktualizuj' : 'Zapisz' }}
            </button>
        </div>
    </div>

    <form wire:submit="save" class="space-y-8">
        {{-- Dane główne --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Dane główne</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- SKU --}}
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">
                        SKU <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="sku"
                           wire:model.blur="sku"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('sku') border-red-500 @enderror">
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- EAN --}}
                <div>
                    <label for="ean" class="block text-sm font-medium text-gray-700 mb-1">EAN</label>
                    <input type="text"
                           id="ean"
                           wire:model.blur="ean"
                           placeholder="8-14 znaków"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('ean') border-red-500 @enderror">
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
                           wire:model.blur="nazwa"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('nazwa') border-red-500 @enderror">
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
                            wire:model.blur="kategoria_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('kategoria_id') border-red-500 @enderror">
                        <option value="">Wybierz kategorię</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->nazwa }}</option>
                        @endforeach
                    </select>
                    @error('kategoria_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="flex items-center">
                    <input type="checkbox"
                           id="aktywny"
                           wire:model="aktywny"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <label for="aktywny" class="ml-2 block text-sm text-gray-900">Produkt aktywny</label>
                </div>

                {{-- Opis --}}
                <div class="md:col-span-2">
                    <label for="opis" class="block text-sm font-medium text-gray-700 mb-1">Opis</label>
                    <textarea id="opis"
                              wire:model.blur="opis"
                              rows="4"
                              placeholder="Opis produktu (obsługuje Markdown)"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('opis') border-red-500 @enderror"></textarea>
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
                           wire:model.blur="waga_g"
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('waga_g') border-red-500 @enderror">
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
                            wire:model.blur="jednostka_sprzedazy"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('jednostka_sprzedazy') border-red-500 @enderror">
                        <option value="szt">Sztuka</option>
                        <option value="opak">Opakowanie</option>
                        <option value="kg">Kilogram</option>
                    </select>
                    @error('jednostka_sprzedazy')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Zawartość opakowania --}}
                <div>
                    <label for="zawartosc_opakowania" class="block text-sm font-medium text-gray-700 mb-1">
                        Zawartość opakowania
                    </label>
                    <input type="number"
                           id="zawartosc_opakowania"
                           wire:model.blur="zawartosc_opakowania"
                           min="1"
                           placeholder="np. liczba sztuk"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('zawartosc_opakowania') border-red-500 @enderror">
                    @error('zawartosc_opakowania')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Ceny i VAT --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Ceny i VAT</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Cena netto --}}
                <div>
                    <label for="cena_netto_gr" class="block text-sm font-medium text-gray-700 mb-1">
                        Cena netto (gr) <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           id="cena_netto_gr"
                           wire:model.blur="cena_netto_gr"
                           min="1"
                           placeholder="Cena w groszach"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('cena_netto_gr') border-red-500 @enderror">
                    @if($cenaNettoDisplay)
                        <p class="mt-1 text-sm text-gray-500">{{ $cenaNettoDisplay }}</p>
                    @endif
                    @error('cena_netto_gr')
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

        {{-- Alergeny --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Alergeny</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($availableAllergens as $key => $label)
                    <div class="flex items-center">
                        <input type="checkbox"
                               id="allergen_{{ $key }}"
                               wire:click="toggleAllergen('{{ $key }}')"
                               @if(in_array($key, $alergeny)) checked @endif
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <label for="allergen_{{ $key }}" class="ml-2 block text-sm text-gray-900">{{ $label }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Wartości odżywcze --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Wartości odżywcze (na 100g)</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                {{-- Kalorie --}}
                <div>
                    <label for="kcal" class="block text-sm font-medium text-gray-700 mb-1">Kalorie (kcal)</label>
                    <input type="number"
                           id="kcal"
                           wire:model.blur="kcal"
                           step="0.1"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('kcal') border-red-500 @enderror">
                    @error('kcal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Białko --}}
                <div>
                    <label for="bialko_g" class="block text-sm font-medium text-gray-700 mb-1">Białko (g)</label>
                    <input type="number"
                           id="bialko_g"
                           wire:model.blur="bialko_g"
                           step="0.1"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('bialko_g') border-red-500 @enderror">
                    @error('bialko_g')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tłuszcz --}}
                <div>
                    <label for="tluszcz_g" class="block text-sm font-medium text-gray-700 mb-1">Tłuszcz (g)</label>
                    <input type="number"
                           id="tluszcz_g"
                           wire:model.blur="tluszcz_g"
                           step="0.1"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('tluszcz_g') border-red-500 @enderror">
                    @error('tluszcz_g')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Węglowodany --}}
                <div>
                    <label for="wegle_g" class="block text-sm font-medium text-gray-700 mb-1">Węglowodany (g)</label>
                    <input type="number"
                           id="wegle_g"
                           wire:model.blur="wegle_g"
                           step="0.1"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('wegle_g') border-red-500 @enderror">
                    @error('wegle_g')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- SEO --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Metadane SEO</h2>
            <div class="space-y-6">
                {{-- Meta title --}}
                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Tytuł SEO</label>
                    <input type="text"
                           id="meta_title"
                           wire:model.blur="meta_title"
                           maxlength="255"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('meta_title') border-red-500 @enderror">
                    @error('meta_title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Meta description --}}
                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Opis SEO</label>
                    <textarea id="meta_description"
                              wire:model.blur="meta_description"
                              rows="3"
                              maxlength="500"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('meta_description') border-red-500 @enderror"></textarea>
                    @error('meta_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </form>

    {{-- Alpine.js script --}}
    <script>
        function productForm() {
            return {
                init() {
                    // Inicjalizacja
                }
            }
        }

        document.addEventListener('livewire:init', () => {
            Livewire.on('product-saved', (event) => {
                // Możesz dodać toast notification tutaj
                console.log(event.message);
                // Przekierowanie lub inne akcje
            });

            Livewire.on('product-error', (message) => {
                // Możesz dodać toast notification tutaj
                console.log(message);
            });

            Livewire.on('product-cancelled', () => {
                // Przekierowanie lub inne akcje
                window.history.back();
            });
        });
    </script>
</div>
