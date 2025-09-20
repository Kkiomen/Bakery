<div class="space-y-6">
    {{-- Nagłówek --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-medium text-gray-900">Zamienniki produktu</h2>
            <p class="text-sm text-gray-600">{{ $product->nazwa }} ({{ $product->sku }})</p>
        </div>
        <button wire:click="openModal"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Dodaj zamienniki
        </button>
    </div>

    {{-- Lista aktualnych zamienników --}}
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            @if($currentSubstitutes->count() > 0)
                <div class="space-y-4">
                    @foreach($currentSubstitutes as $substitute)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    @if($substitute->images->first())
                                        <img class="h-12 w-12 rounded-lg object-cover"
                                             src="{{ $substitute->images->first()->url }}"
                                             alt="{{ $substitute->nazwa }}">
                                    @else
                                        <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-gray-900">{{ $substitute->nazwa }}</h3>
                                    <p class="text-sm text-gray-500">SKU: {{ $substitute->sku }}</p>
                                    <p class="text-sm text-gray-500">Kategoria: {{ $substitute->category->nazwa }}</p>
                                    @if($substitute->pivot->uwagi)
                                        <p class="text-sm text-gray-600 mt-1">{{ $substitute->pivot->uwagi }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Priorytet: {{ $substitute->pivot->priorytet }}
                                    </span>
                                    <p class="text-sm text-gray-500 mt-1">{{ $substitute->waga_kg }}</p>
                                    <p class="text-sm text-gray-500">{{ $substitute->cena_netto }} zł</p>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <button wire:click="removeSubstitute({{ $substitute->id }})"
                                        class="text-red-600 hover:text-red-900">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Brak zamienników</h3>
                    <p class="mt-1 text-sm text-gray-500">Ten produkt nie ma jeszcze żadnych zamienników.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal wyboru zamienników --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Wybierz zamienniki</h3>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        {{-- Wyszukiwanie --}}
                        <div class="mb-4">
                            <input type="text"
                                   wire:model.live.debounce.300ms="search"
                                   placeholder="Wyszukaj po nazwie lub SKU..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Lista dostępnych produktów --}}
                        <div class="max-h-96 overflow-y-auto">
                            @foreach($availableProducts as $availableProduct)
                                <div class="flex items-center justify-between p-3 border-b border-gray-200 hover:bg-gray-50">
                                    <div class="flex items-center space-x-3">
                                        <input type="checkbox"
                                               wire:click="toggleSubstitute({{ $availableProduct->id }})"
                                               @if(in_array($availableProduct->id, $selectedSubstitutes)) checked @endif
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">

                                        @if($availableProduct->images->first())
                                            <img class="h-10 w-10 rounded-lg object-cover"
                                                 src="{{ $availableProduct->images->first()->url }}"
                                                 alt="{{ $availableProduct->nazwa }}">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif

                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">{{ $availableProduct->nazwa }}</h4>
                                            <p class="text-sm text-gray-500">SKU: {{ $availableProduct->sku }} | {{ $availableProduct->category->nazwa }}</p>
                                            <p class="text-sm text-gray-500">{{ $availableProduct->waga_kg }} | {{ $availableProduct->cena_netto }} zł</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Paginacja --}}
                        @if($availableProducts->hasPages())
                            <div class="mt-4">
                                {{ $availableProducts->links() }}
                            </div>
                        @endif

                        {{-- Wybrane zamienniki z ustawieniami --}}
                        @if(!empty($selectedSubstitutes))
                            <div class="mt-6 border-t pt-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Wybrane zamienniki ({{ count($selectedSubstitutes) }})</h4>
                                <div class="space-y-3 max-h-48 overflow-y-auto">
                                    @foreach($selectedSubstitutes as $selectedId)
                                        @php
                                            $selectedProduct = $availableProducts->firstWhere('id', $selectedId) ??
                                                             App\Models\Product::find($selectedId);
                                        @endphp
                                        @if($selectedProduct)
                                            <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                                                <div class="flex-1">
                                                    <h5 class="text-sm font-medium text-gray-900">{{ $selectedProduct->nazwa }}</h5>
                                                    <p class="text-xs text-gray-500">{{ $selectedProduct->sku }}</p>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <label class="text-xs text-gray-700">Priorytet:</label>
                                                    <input type="number"
                                                           wire:change="updatePriority({{ $selectedId }}, $event.target.value)"
                                                           value="{{ $substitutePriorities[$selectedId] ?? 0 }}"
                                                           min="0"
                                                           class="w-16 px-2 py-1 text-xs border border-gray-300 rounded">
                                                </div>
                                                <div class="flex-1">
                                                    <input type="text"
                                                           wire:change="updateNotes({{ $selectedId }}, $event.target.value)"
                                                           value="{{ $substituteNotes[$selectedId] ?? '' }}"
                                                           placeholder="Uwagi..."
                                                           class="w-full px-2 py-1 text-xs border border-gray-300 rounded">
                                                </div>
                                                <button wire:click="toggleSubstitute({{ $selectedId }})"
                                                        class="text-red-600 hover:text-red-900">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="saveSubstitutes"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Zapisz zamienniki
                        </button>
                        <button wire:click="closeModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Anuluj
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- JavaScript dla obsługi zdarzeń --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('substitutes-updated', (message) => {
                // Możesz dodać toast notification tutaj
                console.log(message);
            });

            Livewire.on('substitutes-error', (message) => {
                // Możesz dodać toast notification tutaj
                console.log(message);
            });
        });
    </script>
</div>
