<div class="max-w-6xl mx-auto space-y-6">
    {{-- Nagłówek --}}
    <div class="flex justify-between items-start">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ $order->nazwa }}</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                    {{ $order->status_label }}
                </span>
                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-{{ $order->priorytet_color }}-100 text-{{ $order->priorytet_color }}-800">
                    {{ $order->priorytet_label }}
                </span>
            </div>
            <p class="text-gray-600 mt-1">{{ $order->numer_zlecenia }}</p>
            @if($order->isOverdue())
                <div class="mt-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Opóźnione
                    </span>
                </div>
            @endif
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('production.orders.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Powrót do listy
            </a>

            <a href="{{ route('production.orders.edit', $order) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edytuj
            </a>

            <button wire:click="duplicateOrder"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                Duplikuj
            </button>

            {{-- Menu akcji --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Akcje
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-show="open"
                     @click.away="open = false"
                     x-transition
                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                    <div class="py-1">
                        @if($order->canBeStarted())
                            <button wire:click="startProduction"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Rozpocznij produkcję
                            </button>
                        @endif

                        @if($order->canBeCompleted())
                            <button wire:click="completeProduction"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Zakończ zlecenie
                            </button>
                        @endif

                        @if($order->canBeCancelled())
                            <button wire:click="cancelOrder"
                                    wire:confirm="Czy na pewno chcesz anulować to zlecenie?"
                                    class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                Anuluj zlecenie
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Informacje podstawowe --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Szczegóły zlecenia --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Szczegóły zlecenia</h3>

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Data produkcji</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->data_produkcji->format('d.m.Y') }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Odpowiedzialny</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->user->name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Typ zlecenia</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->typ_zlecenia_label }}</dd>
                    </div>

                    @if($order->klient)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Klient</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->klient }}</dd>
                        </div>
                    @endif

                    @if($order->data_rozpoczecia)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Rozpoczęto</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->data_rozpoczecia->format('d.m.Y H:i') }}</dd>
                        </div>
                    @endif

                    @if($order->data_zakonczenia)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Zakończono</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->data_zakonczenia->format('d.m.Y H:i') }}</dd>
                        </div>
                    @endif
                </dl>

                @if($order->opis)
                    <div class="mt-4">
                        <dt class="text-sm font-medium text-gray-500">Opis</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->opis }}</dd>
                    </div>
                @endif

                @if($order->uwagi)
                    <div class="mt-4">
                        <dt class="text-sm font-medium text-gray-500">Uwagi</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->uwagi }}</dd>
                    </div>
                @endif
            </div>

            {{-- Pozycje zlecenia --}}
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Pozycje do wyprodukowania</h3>
                    <span class="text-sm text-gray-500">{{ $order->items->count() }} pozycji</span>
                </div>

                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <h4 class="font-medium text-gray-900">{{ $item->product->nazwa }}</h4>
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-{{ $item->status_color }}-100 text-{{ $item->status_color }}-800">
                                            {{ $item->status_label }}
                                        </span>
                                    </div>

                                    <div class="mt-1 text-sm text-gray-500">
                                        <span>Zamówiono: {{ $item->formatted_quantity }}</span>
                                        <span class="mx-2">•</span>
                                        <span>Wyprodukowano: {{ $item->formatted_produced_quantity }}</span>
                                        @if($item->remaining_quantity > 0)
                                            <span class="mx-2">•</span>
                                            <span>Pozostało: {{ $item->formatted_remaining_quantity }}</span>
                                        @endif
                                    </div>

                                    {{-- Aktualny krok produkcji --}}
                                    <div class="mt-2 flex items-center space-x-2">
                                        <span class="text-xs text-gray-500">Krok:</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-{{ $item->step_color }}-100 text-{{ $item->step_color }}-800">
                                            {{ $item->current_step_label }}
                                        </span>
                                        @if($item->step_started_at)
                                            <span class="text-xs text-gray-500">
                                                ({{ $item->step_started_at->diffForHumans() }})
                                            </span>
                                        @endif
                                    </div>

                                    @if($item->uwagi)
                                        <div class="mt-1 text-sm text-gray-500">
                                            Uwagi: {{ $item->uwagi }}
                                        </div>
                                    @endif

                                    {{-- Pasek postępu --}}
                                    @if($item->ilosc > 0)
                                        <div class="mt-2">
                                            <div class="flex items-center space-x-2">
                                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-{{ $item->progress_percentage >= 100 ? 'green' : 'blue' }}-600 h-2 rounded-full"
                                                         style="width: {{ $item->progress_percentage }}%"></div>
                                                </div>
                                                <span class="text-xs text-gray-500">{{ $item->progress_percentage }}%</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center space-x-2">
                                    @if($item->canBeStarted())
                                        <button wire:click="startItemProduction({{ $item->id }})"
                                                class="text-blue-600 hover:text-blue-800 text-sm">
                                            Rozpocznij
                                        </button>
                                    @endif

                                    @if($item->status === 'w_produkcji')
                                        <button wire:click="openUpdateModal({{ $item->id }})"
                                                class="text-yellow-600 hover:text-yellow-800 text-sm">
                                            Aktualizuj
                                        </button>

                                        <button wire:click="completeItem({{ $item->id }})"
                                                class="text-green-600 hover:text-green-800 text-sm">
                                            Ukończ
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Panel boczny --}}
        <div class="space-y-6">
            {{-- Podsumowanie --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Podsumowanie</h3>

                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Łączna ilość pozycji:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $order->getTotalItems() }}</dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Wyprodukowano:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $order->getTotalProduced() }}</dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Postęp:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $order->getProgressPercentage() }}%</dd>
                    </div>
                </dl>

                @if($order->status === 'w_produkcji')
                    <div class="mt-4">
                        <div class="bg-gray-200 rounded-full h-3">
                            <div class="bg-blue-600 h-3 rounded-full"
                                 style="width: {{ $order->getProgressPercentage() }}%"></div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Historia zmian --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Historia</h3>

                <div class="space-y-3">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full mt-2"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">Zlecenie utworzone</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>

                    @if($order->data_rozpoczecia)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-2 h-2 bg-yellow-600 rounded-full mt-2"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900">Produkcja rozpoczęta</p>
                                <p class="text-xs text-gray-500">{{ $order->data_rozpoczecia->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($order->data_zakonczenia)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-2 h-2 bg-green-600 rounded-full mt-2"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900">Zlecenie zakończone</p>
                                <p class="text-xs text-gray-500">{{ $order->data_zakonczenia->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal aktualizacji ilości --}}
    @if($showUpdateModal && $selectedItem)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Aktualizuj ilość wyprodukowaną
                </h3>

                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-2">{{ $selectedItem->product->nazwa }}</p>
                    <p class="text-sm text-gray-500">Zamówiono: {{ $selectedItem->formatted_quantity }}</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Ilość wyprodukowana
                    </label>
                    <input type="number"
                           wire:model="producedQuantity"
                           min="0"
                           max="{{ $selectedItem->ilosc }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('producedQuantity') border-red-500 @enderror">
                    @error('producedQuantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('showUpdateModal', false)"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Anuluj
                    </button>
                    <button wire:click="updateProducedQuantity"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        Zapisz
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
