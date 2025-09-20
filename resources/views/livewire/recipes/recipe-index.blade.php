<div x-data="{ showFilters: false }" class="space-y-6">
    {{-- Nagłówek --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kartoteka receptur</h1>
            <p class="text-gray-600">Zarządzaj przepisami i procesami produkcyjnymi</p>
        </div>
        <div class="flex space-x-3">
            <button @click="showFilters = !showFilters"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filtry
            </button>
            <a href="{{ route('recipes.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Dodaj recepturę
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
                            <span class="text-white font-semibold text-xs">📋</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Wszystkie receptury</dt>
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
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <span class="text-white font-semibold text-xs">✅</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Przetestowane</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['tested'] }}</dd>
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
                            <span class="text-white font-semibold text-xs">⏱️</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Średni czas</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['avg_time']) }} min</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <span class="text-white font-semibold text-xs">🏷️</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Kategorie</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['categories'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtry --}}
    <div x-show="showFilters" x-transition class="bg-gray-50 p-4 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            {{-- Wyszukiwanie --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Wyszukaj</label>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Kod, nazwa, autor..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Kategoria --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategoria</label>
                <select wire:model.live="categoryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Wszystkie kategorie</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Poziom trudności --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trudność</label>
                <select wire:model.live="difficultyFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Wszystkie</option>
                    @foreach($difficulties as $key => $label)
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

            {{-- Testowane --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Testowanie</label>
                <select wire:model.live="testedFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Wszystkie</option>
                    <option value="1">Przetestowane</option>
                    <option value="0">W opracowaniu</option>
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

    {{-- Lista receptur --}}
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        @forelse($recipes as $recipe)
            <div class="border-b border-gray-200 last:border-b-0">
                <div class="px-6 py-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-4">
                                {{-- Checkbox --}}
                                <input type="checkbox"
                                       wire:model.live="selectedRecipes"
                                       value="{{ $recipe->id }}"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm">

                                {{-- Główne informacje --}}
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <h3 class="text-lg font-medium text-gray-900">
                                            <a href="{{ route('recipes.show', $recipe) }}" class="hover:text-blue-600">
                                                {{ $recipe->nazwa }}
                                            </a>
                                        </h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $recipe->getDifficultyColor() }}-100 text-{{ $recipe->getDifficultyColor() }}-800">
                                            {{ $difficulties[$recipe->poziom_trudnosci] ?? $recipe->poziom_trudnosci }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $recipe->getStatusColor() }}-100 text-{{ $recipe->getStatusColor() }}-800">
                                            {{ $recipe->getStatusLabel() }}
                                        </span>
                                    </div>

                                    <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                                        <span class="font-mono">{{ $recipe->kod }}</span>
                                        <span>{{ $categories[$recipe->kategoria] ?? $recipe->kategoria }}</span>
                                        <span>{{ $recipe->ilosc_porcji }} {{ $recipe->ilosc_porcji == 1 ? 'porcja' : 'porcje' }}</span>
                                        @if($recipe->waga_jednostkowa_g)
                                            <span>{{ $recipe->waga_jednostkowa_g }}g/szt</span>
                                        @endif
                                        @if($recipe->autor)
                                            <span>{{ $recipe->autor }}</span>
                                        @endif
                                    </div>

                                    @if($recipe->opis)
                                        <p class="mt-2 text-sm text-gray-600">{{ Str::limit($recipe->opis, 150) }}</p>
                                    @endif

                                    {{-- Informacje o czasie i procesach --}}
                                    <div class="mt-3 flex items-center space-x-6 text-sm">
                                        @if($recipe->czas_calkowity_min)
                                            <div class="flex items-center text-gray-500">
                                                <span class="mr-1">⏱️</span>
                                                {{ $recipe->czas_calkowity_formatted }}
                                            </div>
                                        @endif

                                        @if($recipe->temperatura_c)
                                            <div class="flex items-center text-gray-500">
                                                <span class="mr-1">🔥</span>
                                                {{ $recipe->temperatura_c }}°C
                                            </div>
                                        @endif

                                        <div class="flex items-center text-gray-500">
                                            <span class="mr-1">📋</span>
                                            {{ $recipe->steps->count() }} kroków
                                        </div>

                                        <div class="flex items-center text-gray-500">
                                            <span class="mr-1">🥄</span>
                                            {{ $recipe->materials->count() }} składników
                                        </div>

                                        @if($recipe->calculateTotalCost() > 0)
                                            <div class="flex items-center text-green-600">
                                                <span class="mr-1">💰</span>
                                                {{ number_format($recipe->calculateCostPerPortion(), 2, ',', '') }} zł/szt
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Akcje --}}
                        <div class="flex items-center space-x-2 ml-4">
                            <button wire:click="toggleRecipeTested({{ $recipe->id }})"
                                    class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white {{ $recipe->testowany ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 hover:bg-gray-500' }}">
                                {{ $recipe->testowany ? 'Przetestowane' : 'Do testów' }}
                            </button>

                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Akcje
                                    <svg class="ml-2 -mr-0.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div x-show="open" @click.away="open = false" x-transition
                                     class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                    <div class="py-1">
                                        <a href="{{ route('recipes.show', $recipe) }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Zobacz</a>
                                        <a href="{{ route('recipes.edit', $recipe) }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edytuj</a>
                                        <a href="{{ route('recipes.steps', $recipe) }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Procesy</a>
                                        <button wire:click="duplicateRecipe({{ $recipe->id }})"
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Kopiuj</button>
                                        <button wire:click="toggleRecipeStatus({{ $recipe->id }})"
                                                class="block w-full text-left px-4 py-2 text-sm {{ $recipe->aktywny ? 'text-red-700' : 'text-green-700' }} hover:bg-gray-100">
                                            {{ $recipe->aktywny ? 'Dezaktywuj' : 'Aktywuj' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Brak receptur</h3>
                <p class="mt-1 text-sm text-gray-500">Rozpocznij od dodania pierwszej receptury.</p>
                <div class="mt-6">
                    <a href="{{ route('recipes.create') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Dodaj pierwszą recepturę
                    </a>
                </div>
            </div>
        @endforelse

        {{-- Paginacja --}}
        @if($recipes->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $recipes->links() }}
            </div>
        @endif
    </div>

    {{-- JavaScript --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('recipe-updated', (message) => {
                console.log(message);
            });
        });
    </script>
</div>
