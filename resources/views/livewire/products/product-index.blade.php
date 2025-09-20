<div x-data="{ showFilters: false }" class="space-y-6">
    {{-- Nagłówek --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kartoteka towarowa</h1>
            <p class="text-gray-600">Zarządzaj produktami w swojej piekarni</p>
        </div>
        <div class="flex space-x-3">
            <button @click="showFilters = !showFilters"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filtry
            </button>
            <a href="{{ route('products.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Dodaj produkt
            </a>
        </div>
    </div>

    {{-- Filtry --}}
    <div x-show="showFilters" x-transition class="bg-gray-50 p-4 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Wyszukiwanie --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Wyszukaj</label>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="SKU, nazwa, EAN..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Kategoria --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategoria</label>
                <select wire:model.live="categoryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Wszystkie kategorie</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->nazwa }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="activeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Wszystkie</option>
                    <option value="1">Aktywne</option>
                    <option value="0">Nieaktywne</option>
                </select>
            </div>

            {{-- Alergeny --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alergeny</label>
                <div class="flex space-x-2">
                    <select wire:model.live="allergenFilter" class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Wybierz alergen</option>
                        @foreach($allergens as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="allergenMode" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="contains">Zawiera</option>
                        <option value="excludes">Nie zawiera</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Zakresy --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            {{-- Zakres wagi --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Waga (g)</label>
                <div class="flex space-x-2">
                    <input type="number"
                           wire:model.live.debounce.500ms="minWeight"
                           placeholder="Od"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <input type="number"
                           wire:model.live.debounce.500ms="maxWeight"
                           placeholder="Do"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            {{-- Zakres ceny --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cena netto (zł)</label>
                <div class="flex space-x-2">
                    <input type="number"
                           step="0.01"
                           wire:model.live.debounce.500ms="minPrice"
                           placeholder="Od"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <input type="number"
                           step="0.01"
                           wire:model.live.debounce.500ms="maxPrice"
                           placeholder="Do"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        {{-- Akcje filtrów --}}
        <div class="flex justify-end mt-4">
            <button wire:click="clearFilters"
                    class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">
                Wyczyść filtry
            </button>
        </div>
    </div>

    {{-- Akcje masowe --}}
    @if(!empty($selectedProducts))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <span class="text-sm text-blue-800">
                    Wybrano {{ count($selectedProducts) }} produktów
                </span>
                <div class="flex space-x-2">
                    <button wire:click="bulkToggleStatus"
                            class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                        Dezaktywuj wybrane
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Tabela produktów --}}
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox"
                                   wire:model.live="selectAll"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('nazwa')">
                            <div class="flex items-center space-x-1">
                                <span>Nazwa</span>
                                @if($sortBy === 'nazwa')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('sku')">
                            <div class="flex items-center space-x-1">
                                <span>SKU</span>
                                @if($sortBy === 'sku')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategoria</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('waga_g')">
                            <div class="flex items-center space-x-1">
                                <span>Waga</span>
                                @if($sortBy === 'waga_g')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('cena_netto_gr')">
                            <div class="flex items-center space-x-1">
                                <span>Cena netto</span>
                                @if($sortBy === 'cena_netto_gr')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox"
                                       wire:model.live="selectedProducts"
                                       value="{{ $product->id }}"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($product->images->first())
                                        <img class="h-10 w-10 rounded-full object-cover mr-3"
                                             src="{{ $product->images->first()->url }}"
                                             alt="{{ $product->nazwa }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $product->nazwa }}</div>
                                        @if($product->alergeny)
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach($product->alergeny as $allergen)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                        {{ $allergens[$allergen] ?? $allergen }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-mono text-gray-900">{{ $product->sku }}</span>
                                    <button wire:click="copyToClipboard('{{ $product->sku }}')"
                                            class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                </div>
                                @if($product->ean)
                                    <div class="text-xs text-gray-500">EAN: {{ $product->ean }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $product->category->nazwa }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>{{ $product->waga_g }} g</div>
                                <div class="text-xs text-gray-500">{{ $product->waga_kg }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>{{ $product->cena_netto }} zł</div>
                                <div class="text-xs text-gray-500">VAT {{ $product->stawka_vat }}%</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="toggleProductStatus({{ $product->id }})"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->aktywny ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->aktywny ? 'Aktywny' : 'Nieaktywny' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('products.show', $product) }}"
                                       class="text-blue-600 hover:text-blue-900">Zobacz</a>
                                    <a href="{{ route('products.edit', $product) }}"
                                       class="text-indigo-600 hover:text-indigo-900">Edytuj</a>
                                    @if($product->substitutes->count() > 0)
                                        <button class="text-green-600 hover:text-green-900"
                                                title="Zamienniki: {{ $product->substitutes->count() }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Brak produktów</h3>
                                <p class="mt-1 text-sm text-gray-500">Rozpocznij od dodania pierwszego produktu.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginacja --}}
        @if($products->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    {{-- JavaScript dla kopiowania do schowka --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('copy-to-clipboard', (sku) => {
                navigator.clipboard.writeText(sku).then(() => {
                    // Możesz dodać toast notification tutaj
                    console.log('SKU skopiowane: ' + sku);
                });
            });

            Livewire.on('product-updated', (message) => {
                // Możesz dodać toast notification tutaj
                console.log(message);
            });
        });
    </script>
</div>
