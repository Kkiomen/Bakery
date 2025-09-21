<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Zarządzanie Dostawami</h1>
            <p class="text-sm text-gray-600 mt-1">
                Planuj i monitoruj dostawy do klientów
            </p>
        </div>

        <div class="flex space-x-3 mt-4 sm:mt-0">
            <button wire:click="openCreateModal"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nowa Dostawa
            </button>
            <flux:button wire:click="optimizeRoutes" variant="outline">
                Optymalizuj Trasy
            </flux:button>
            <flux:button wire:click="exportDeliveries" variant="outline">
                Eksport
            </flux:button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        🚚
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Wszystkie</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                        ⏰
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Oczekujące</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['oczekujace'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        👤
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Przypisane</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['przypisane'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        🚛
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">W drodze</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['w_drodze'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        ✅
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Dostarczone</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['dostarczone'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        ⚠️
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Problemy</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['problemy'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            <div>
                <flux:input
                    type="date"
                    wire:model.live="selectedDate"
                    label="Data dostawy"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="statusFilter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Wszystkie</option>
                    <option value="oczekujaca">Oczekujące</option>
                    <option value="przypisana">Przypisane</option>
                    <option value="w_drodze">W drodze</option>
                    <option value="dostarczona">Dostarczone</option>
                    <option value="problem">Problemy</option>
                    <option value="anulowana">Anulowane</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kierowca</label>
                <select wire:model.live="driverFilter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Wszyscy</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Priorytet</label>
                <select wire:model.live="priorityFilter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Wszystkie</option>
                    <option value="niski">Niski</option>
                    <option value="normalny">Normalny</option>
                    <option value="wysoki">Wysoki</option>
                    <option value="pilny">Pilny</option>
                </select>
            </div>

<div>
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Szukaj dostaw..."
                    label="Wyszukaj"
                />
            </div>

            <div class="flex items-end">
                <flux:button wire:click="resetFilters" variant="outline" size="sm">
                    Wyczyść filtry
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    @if(!empty($selectedDeliveries))
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="text-sm text-blue-700">
                    Wybrano {{ count($selectedDeliveries) }} dostaw
                </span>
            </div>

            <div class="flex space-x-2">
                <select wire:model="bulkDriverId"
                        class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Przypisz kierowcę</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                    @endforeach
                </select>

                <flux:button wire:click="bulkAssignDriver($event.target.previousElementSibling.value)"
                           variant="outline" size="sm">
                    Przypisz
                </flux:button>

                <flux:button wire:click="bulkUpdatePriority('pilny')"
                           variant="outline" size="sm">
                    Pilny
                </flux:button>
            </div>
        </div>
    </div>
    @endif

    <!-- Deliveries Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" wire:model.live="selectAll"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('kolejnosc_dostawy')">
                            <div class="flex items-center space-x-1">
                                <span>Kolejność</span>
                                @if($sortBy === 'kolejnosc_dostawy')
                                    <span class="{{ $sortDirection === 'asc' ? '' : 'rotate-180' }}">▲</span>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dostawa
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Klient
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kierowca
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Priorytet
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Godzina
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Akcje
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($deliveries as $delivery)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" wire:model.live="selectedDeliveries" value="{{ $delivery->id }}"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <input type="number" wire:change="updateDeliveryOrder({{ $delivery->id }}, $event.target.value)"
                                       value="{{ $delivery->kolejnosc_dostawy }}"
                                       class="w-16 text-center border-gray-300 rounded-md">
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $delivery->numer_dostawy }}</div>
                            <div class="text-sm text-gray-500">{{ $delivery->productionOrder->nazwa }}</div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $delivery->klient_nazwa }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($delivery->klient_adres, 40) }}</div>
                            @if($delivery->telefon_kontaktowy)
                                <div class="text-xs text-gray-400">{{ $delivery->telefon_kontaktowy }}</div>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($delivery->driver)
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-blue-600">
                                            {{ substr($delivery->driver->name, 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-900">{{ $delivery->driver->name }}</div>
                                </div>
                            @else
                                <select wire:change="assignDriver({{ $delivery->id }}, $event.target.value)"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    <option value="">Przypisz kierowcę</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge :color="$delivery->status_color">
                                {{ $delivery->status_label }}
                            </flux:badge>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge :color="$delivery->priorytet_color" size="sm">
                                {{ $delivery->priorytet_label }}
                            </flux:badge>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($delivery->godzina_planowana)
                                {{ $delivery->godzina_planowana->format('H:i') }}
                            @else
                                -
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                @if($delivery->driver)
                                    <flux:button wire:click="unassignDriver({{ $delivery->id }})"
                                               variant="ghost" size="sm">
                                        ✕
                                    </flux:button>
                                @endif

                                <flux:button wire:click="duplicateDelivery({{ $delivery->id }})"
                                           variant="ghost" size="sm">
                                    📋
                                </flux:button>

                                @if($delivery->canBeCancelled())
                                    <flux:button wire:click="cancelDelivery({{ $delivery->id }})"
                                               variant="ghost" size="sm"
                                               wire:confirm="Czy na pewno chcesz anulować tę dostawę?">
                                        🗑️
                                    </flux:button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="text-6xl mb-4">🚚</div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Brak dostaw</h3>
                                <p class="text-gray-500 mb-4">Nie znaleziono dostaw dla wybranych kryteriów.</p>
                                <flux:button wire:click="showCreateForm" variant="primary">
                                    Utwórz pierwszą dostawę
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($deliveries->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $deliveries->links() }}
            </div>
        @endif
    </div>

    <!-- Debug info -->
    <div class="bg-yellow-100 p-4 mb-4 rounded">
        <div class="text-sm">
            <strong>Debug:</strong><br>
            showCreateForm = {{ $showCreateForm ? 'true' : 'false' }}<br>
            testMessage = {{ $testMessage }}
        </div>
    </div>

    <!-- Create Delivery Modal -->
    @if($showCreateForm)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Nowa Dostawa</h3>
                    <button wire:click="closeCreateModal"
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    @livewire('deliveries.create-delivery')
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
    // Auto-refresh every 30 seconds
    setInterval(() => {
        $wire.$refresh();
    }, 30000);

    // Listen for events
    $wire.on('delivery-assigned', (data) => {
        // Show success notification
        console.log(data.message);
    });

    $wire.on('delivery-unassigned', (data) => {
        console.log(data.message);
    });

    $wire.on('delivery-cancelled', (data) => {
        console.log(data.message);
    });

    $wire.on('delivery-duplicated', (data) => {
        console.log(data.message);
    });
</script>
@endscript
