<div x-data="{ showFilters: false }" class="space-y-6">
    {{-- Nagłówek --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kartoteka towarowa</h1>
            <p class="text-gray-600">Zarządzaj surowcami i składnikami do produkcji</p>
        </div>
        <div class="flex space-x-3">
            <button @click="showFilters = !showFilters"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filtry
            </button>
            <a href="{{ route('materials.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Dodaj surowiec
            </a>
        </div>
    </div>

    {{-- Statystyki --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <span class="text-white font-semibold text-xs">📦</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Wszystkie surowce</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                            <span class="text-white font-semibold text-xs">⚠️</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Niski stan</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['low_stock'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <span class="text-white font-semibold text-xs">❌</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Brak w magazynie</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['out_of_stock'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <span class="text-white font-semibold text-xs">💰</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Wartość magazynu</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_value'], 2, ',', '') }} zł</dd>
                        </dl>
                    </div>
                </div>
            </div>
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
                       placeholder="Kod, nazwa, dostawca..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Typ surowca --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Typ surowca</label>
                <select wire:model.live="typeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Wszystkie typy</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Wszystkie</option>
                    <option value="1">Aktywne</option>
                    <option value="0">Nieaktywne</option>
                </select>
            </div>

            {{-- Stan magazynowy --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stan magazynowy</label>
                <select wire:model.live="stockFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Wszystkie</option>
                    <option value="low">Niski stan</option>
                    <option value="out">Brak w magazynie</option>
                </select>
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

    {{-- Tabela surowców --}}
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
                            wire:click="sortBy('kod')">
                            <div class="flex items-center space-x-1">
                                <span>Kod</span>
                                @if($sortBy === 'kod')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Typ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('stan_aktualny')">
                            <div class="flex items-center space-x-1">
                                <span>Stan</span>
                                @if($sortBy === 'stan_aktualny')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cena</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($materials as $material)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox"
                                       wire:model.live="selectedMaterials"
                                       value="{{ $material->id }}"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $material->nazwa }}</div>
                                        @if($material->dostawca)
                                            <div class="text-sm text-gray-500">{{ $material->dostawca }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono text-gray-900">{{ $material->kod }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $types[$material->typ] ?? $material->typ }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <div class="text-sm text-gray-900">
                                            {{ $material->stan_aktualny }} {{ $material->jednostka_podstawowa }}
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                            @php
                                                $percentage = $material->stan_optymalny > 0
                                                    ? min(($material->stan_aktualny / $material->stan_optymalny) * 100, 100)
                                                    : 0;
                                                $color = $material->getStockStatusColor();
                                            @endphp
                                            <div class="bg-{{ $color }}-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $material->getStockStatusColor() }}-100 text-{{ $material->getStockStatusColor() }}-800">
                                        {{ $material->getStockStatusLabel() }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($material->cena_zakupu_gr)
                                    <div>{{ $material->cena_zakupu }} zł</div>
                                    <div class="text-xs text-gray-500">VAT {{ $material->stawka_vat }}%</div>
                                @else
                                    <span class="text-gray-400">Brak ceny</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="toggleMaterialStatus({{ $material->id }})"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $material->aktywny ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $material->aktywny ? 'Aktywny' : 'Nieaktywny' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('materials.show', $material) }}"
                                       class="text-blue-600 hover:text-blue-900">Zobacz</a>
                                    <a href="{{ route('materials.edit', $material) }}"
                                       class="text-indigo-600 hover:text-indigo-900">Edytuj</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Brak surowców</h3>
                                <p class="mt-1 text-sm text-gray-500">Rozpocznij od dodania pierwszego surowca.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginacja --}}
        @if($materials->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $materials->links() }}
            </div>
        @endif
    </div>

    {{-- JavaScript --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('material-updated', (message) => {
                console.log(message);
            });
        });
    </script>
</div>
