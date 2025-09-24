<div class="space-y-6">
    <!-- Header with Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-semibold">📋</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Wszystkie zamówienia</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_orders']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-semibold">⏳</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Oczekujące</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['pending_orders']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-semibold">🏭</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">W produkcji</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['in_production_orders']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-semibold">✅</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Zrealizowane</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['completed_orders']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-semibold">💰</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Łączna wartość</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_value'], 2) }} zł</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Filtry wyszukiwania</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Szukaj</label>
                    <input type="text"
                           wire:model.live="search"
                           placeholder="Numer zamówienia lub nazwa klienta..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select wire:model.live="statusFilter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Klient</label>
                    <select wire:model.live="clientFilter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Wszyscy klienci</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data od</label>
                    <input type="date"
                           wire:model.live="dateFrom"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Zamówienia B2B</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('order_number')">
                            Numer zamówienia
                            @if($sortBy === 'order_number')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('created_at')">
                            Data złożenia
                            @if($sortBy === 'created_at')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Klient
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data realizacji
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Wartość
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Zlecenia
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Akcje
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $order->numer_zamowienia }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $order->client->company_name }}</div>
                            <div class="text-sm text-gray-500">{{ $order->client->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @switch($order->status)
                                    @case('oczekujace') bg-yellow-100 text-yellow-800 @break
                                    @case('potwierdzone') bg-blue-100 text-blue-800 @break
                                    @case('w_produkcji') bg-orange-100 text-orange-800 @break
                                    @case('gotowe_do_dostawy') bg-purple-100 text-purple-800 @break
                                    @case('w_dostawie') bg-indigo-100 text-indigo-800 @break
                                    @case('zrealizowane') bg-green-100 text-green-800 @break
                                    @case('anulowane') bg-red-100 text-red-800 @break
                                    @default bg-gray-100 text-gray-800
                                @endswitch
                            ">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $order->data_realizacji ? $order->data_realizacji->format('d.m.Y') : '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ number_format($order->wartosc_brutto, 2) }} zł</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($order->productionOrders->count() > 0)
                                <div class="flex flex-col space-y-1">
                                    @foreach($order->productionOrders as $productionOrder)
                                        <button wire:click="goToProductionOrder({{ $productionOrder->id }})"
                                                class="text-xs text-blue-600 hover:text-blue-800 text-left">
                                            {{ $productionOrder->numer_zlecenia }}
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-sm text-gray-500">Brak zleceń</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <button wire:click="viewOrderDetails({{ $order->id }})"
                                        class="text-blue-600 hover:text-blue-800"
                                        title="Zobacz szczegóły">
                                    👁️
                                </button>

                                @if($order->productionOrders->count() === 0)
                                    <button wire:click="createProductionOrder({{ $order->id }})"
                                            class="text-green-600 hover:text-green-800"
                                            title="Utwórz zlecenie produkcyjne">
                                        🏭
                                    </button>
                                @endif

                                <button wire:click="impersonateClient({{ $order->client->id }})"
                                        class="text-purple-600 hover:text-purple-800"
                                        title="Przełącz na klienta">
                                    👤
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <span class="text-2xl">📋</span>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">Brak zamówień</h3>
                                <p class="text-gray-500">Nie znaleziono zamówień spełniających kryteria wyszukiwania.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- Order Details Modal -->
    @if($showDetailsModal && $selectedOrder)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-screen overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-xl font-semibold">Szczegóły zamówienia {{ $selectedOrder->numer_zamowienia }}</h3>
                    <button wire:click="closeDetailsModal" class="text-gray-400 hover:text-gray-600">
                        <span class="text-2xl">×</span>
                    </button>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Order Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Informacje o zamówieniu</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Numer zamówienia</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedOrder->numer_zamowienia }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Data złożenia</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedOrder->created_at->format('d.m.Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $selectedOrder->status)) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Data realizacji</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ $selectedOrder->data_realizacji ? $selectedOrder->data_realizacji->format('d.m.Y') : '-' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Wartość brutto</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ number_format($selectedOrder->wartosc_brutto, 2) }} zł</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Dane klienta</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nazwa firmy</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedOrder->client->company_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedOrder->client->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Telefon</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedOrder->client->phone ?: '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Adres</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ $selectedOrder->client->address }}<br>
                                        {{ $selectedOrder->client->postal_code }} {{ $selectedOrder->client->city }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-4">Pozycje zamówienia</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produkt</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ilość</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cena jedn.</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wartość</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($selectedOrder->items as $item)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $item->product->nazwa }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $item->ilosc }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($item->cena_jednostkowa, 2) }} zł</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($item->wartosc_brutto, 2) }} zł</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Production Orders -->
                    @if($selectedOrder->productionOrders->count() > 0)
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Zlecenia produkcyjne</h4>
                            <div class="space-y-2">
                                @foreach($selectedOrder->productionOrders as $productionOrder)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $productionOrder->numer_zlecenia }}</div>
                                            <div class="text-sm text-gray-500">Status: {{ $productionOrder->status }}</div>
                                        </div>
                                        <button wire:click="goToProductionOrder({{ $productionOrder->id }})"
                                                class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                                            Zobacz zlecenie
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <p class="text-gray-500 mb-4">Nie utworzono jeszcze zlecenia produkcyjnego dla tego zamówienia.</p>
                            <button wire:click="createProductionOrder({{ $selectedOrder->id }})"
                                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                Utwórz zlecenie produkcyjne
                            </button>
                        </div>
                    @endif
                </div>

                <div class="flex justify-between items-center p-6 border-t bg-gray-50">
                    <button wire:click="impersonateClient({{ $selectedOrder->client->id }})"
                            class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                        Przełącz na klienta
                    </button>
                    <button wire:click="closeDetailsModal"
                            class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                        Zamknij
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
