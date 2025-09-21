<div class="min-h-screen bg-gray-50">
    <!-- Nagłówek -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📋 Historia Zamówień</h1>
                    <p class="text-gray-600">{{ Auth::guard('b2b')->user()->company_name }}</p>
                </div>
                <div class="flex space-x-4">
                    <button wire:click="exportOrders"
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        📊 Eksportuj
                    </button>
                    <a href="{{ route('b2b.catalog') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        🛒 Katalog
                    </a>
                    <a href="{{ route('b2b.dashboard') }}"
                       class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        ← Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statystyki -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📊</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Wszystkie</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_orders'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">⏳</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Oczekujące</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['pending_orders'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">✅</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Dostarczone</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['delivered_orders'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">💰</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Wartość total</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_value'], 0) }} zł</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📅</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">W tym miesiącu</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['this_month_orders'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">💵</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Wartość m-c</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['this_month_value'], 0) }} zł</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtry -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Szukaj</label>
                        <input type="text" wire:model.live="search"
                               placeholder="Numer zamówienia, adres..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model.live="statusFilter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @foreach($orderStatuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data od</label>
                        <input type="date" wire:model.live="dateFrom"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data do</label>
                        <input type="date" wire:model.live="dateTo"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Na stronie</label>
                        <select wire:model.live="perPage"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista zamówień -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            @forelse($orders as $order)
                <div class="border-b border-gray-200 last:border-b-0">
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-4">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $order->order_number }}
                                        </h3>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $order->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $order->status === 'in_production' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $order->status === 'ready' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $order->status === 'shipped' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                            {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-gray-900">{{ number_format($order->total_amount, 2) }} zł</p>
                                        <p class="text-sm text-gray-600">{{ $order->items->count() }} pozycji</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm text-gray-600">
                                    <div>
                                        <span class="font-medium">Data zamówienia:</span><br>
                                        {{ $order->order_date->format('d.m.Y') }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Data dostawy:</span><br>
                                        {{ $order->delivery_date ? $order->delivery_date->format('d.m.Y') : 'Nie określono' }}
                                        @if($order->delivery_time_from)
                                            <br><span class="text-xs">{{ $order->delivery_time_from->format('H:i') }} - {{ $order->delivery_time_to ? $order->delivery_time_to->format('H:i') : '...' }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-medium">Adres dostawy:</span><br>
                                        {{ $order->delivery_address }}<br>
                                        {{ $order->delivery_postal_code }} {{ $order->delivery_city }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Płatność:</span><br>
                                        {{ $order->payment_method_label }}<br>
                                        <span class="text-xs">{{ $order->payment_status_label }}</span>
                                    </div>
                                </div>

                                @if($order->customer_notes)
                                    <div class="mt-3 p-3 bg-gray-50 rounded-md">
                                        <span class="font-medium text-gray-700">Uwagi:</span>
                                        <p class="text-sm text-gray-600 mt-1">{{ $order->customer_notes }}</p>
                                    </div>
                                @endif

                                <!-- Produkty w zamówieniu -->
                                <div class="mt-4">
                                    <button class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                            onclick="toggleOrderItems('order-{{ $order->id }}')">
                                        👁️ Pokaż produkty ({{ $order->items->count() }})
                                    </button>

                                    <div id="order-{{ $order->id }}" class="hidden mt-3 bg-gray-50 rounded-md p-4">
                                        <div class="space-y-2">
                                            @foreach($order->items as $item)
                                                <div class="flex justify-between items-center text-sm">
                                                    <div class="flex-1">
                                                        <span class="font-medium">{{ $item->product_name }}</span>
                                                        @if($item->notes)
                                                            <span class="text-gray-500">- {{ $item->notes }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-right">
                                                        <span class="font-medium">{{ $item->quantity }} szt</span>
                                                        <span class="text-gray-500">× {{ number_format($item->unit_price_gross, 2) }} zł</span>
                                                        <span class="font-bold">= {{ number_format($item->line_total_gross, 2) }} zł</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="mt-3 pt-3 border-t border-gray-200">
                                            <div class="flex justify-between items-center font-bold">
                                                <span>Razem:</span>
                                                <span>{{ number_format($order->total_amount, 2) }} zł brutto</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ml-6 flex flex-col space-y-2">
                                <a href="{{ route('b2b.orders.show', $order) }}"
                                   class="bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700 text-center">
                                    👁️ Szczegóły
                                </a>
                                @if(in_array($order->status, ['delivered', 'cancelled']))
                                    <button wire:click="reorder({{ $order->id }})"
                                            class="bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700">
                                        🔄 Zamów ponownie
                                    </button>
                                @endif
                                @if($order->status === 'pending')
                                    <button onclick="return confirm('Czy na pewno chcesz anulować to zamówienie?')"
                                            class="bg-red-600 text-white px-3 py-2 rounded text-sm hover:bg-red-700">
                                        ❌ Anuluj
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <span class="text-gray-400 text-6xl">📋</span>
                    <h3 class="text-lg font-medium text-gray-900 mt-4">Brak zamówień</h3>
                    <p class="text-gray-600">Nie znaleźliśmy zamówień spełniających kryteria wyszukiwania.</p>
                    <div class="mt-6">
                        <a href="{{ route('b2b.catalog') }}"
                           class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                            🛒 Przejdź do katalogu
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Paginacja -->
        @if($orders->hasPages())
            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <script>
        function toggleOrderItems(orderId) {
            const element = document.getElementById(orderId);
            if (element.classList.contains('hidden')) {
                element.classList.remove('hidden');
            } else {
                element.classList.add('hidden');
            }
        }
    </script>
</div>
