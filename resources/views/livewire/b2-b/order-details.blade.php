<div class="min-h-screen bg-gray-50">
    <!-- Nagłówek -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📋 Zamówienie {{ $order->order_number }}</h1>
                    <p class="text-gray-600">{{ Auth::guard('b2b')->user()->company_name }}</p>
                </div>
                <div class="flex space-x-4">
                    <button wire:click="printOrder"
                            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        🖨️ Drukuj
                    </button>
                    <button wire:click="downloadInvoice"
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        📄 Faktura
                    </button>
                    <a href="{{ route('b2b.orders.index') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        ← Lista zamówień
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" id="order-printable">
        <!-- Status i podstawowe informacje -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <h2 class="text-xl font-semibold text-gray-900">Status zamówienia</h2>
                        <span class="inline-flex px-3 py-2 text-sm font-semibold rounded-full
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
                    <div class="flex space-x-3">
                        @if(in_array($order->status, ['delivered', 'cancelled']))
                            <button wire:click="reorderItems"
                                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                🔄 Zamów ponownie
                            </button>
                        @endif
                        @if($order->status === 'pending')
                            <button wire:click="cancelOrder"
                                    onclick="return confirm('Czy na pewno chcesz anulować to zamówienie?')"
                                    class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                ❌ Anuluj zamówienie
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">📅 Data zamówienia</h3>
                        <p class="text-lg text-gray-900">{{ $order->order_date->format('d.m.Y H:i') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">🚚 Data dostawy</h3>
                        <p class="text-lg text-gray-900">
                            {{ $order->delivery_date ? $order->delivery_date->format('d.m.Y') : 'Nie określono' }}
                        </p>
                        @if($order->delivery_time_from)
                            <p class="text-sm text-gray-600">
                                {{ $order->delivery_time_from->format('H:i') }} -
                                {{ $order->delivery_time_to ? $order->delivery_time_to->format('H:i') : '...' }}
                            </p>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">💰 Wartość</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($order->total_amount, 2) }} zł</p>
                        <p class="text-sm text-gray-600">{{ $order->items->count() }} pozycji</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">💳 Płatność</h3>
                        <p class="text-lg text-gray-900">{{ $order->payment_method_label }}</p>
                        <p class="text-sm text-gray-600">{{ $order->payment_status_label }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Szczegóły dostawy -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">📦 Szczegóły dostawy</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Adres dostawy</h3>
                                <p class="text-gray-900">{{ $order->delivery_address }}</p>
                                <p class="text-gray-900">{{ $order->delivery_postal_code }} {{ $order->delivery_city }}</p>
                            </div>

                            @if($order->delivery_notes)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-1">Uwagi do dostawy</h3>
                                    <p class="text-gray-900">{{ $order->delivery_notes }}</p>
                                </div>
                            @endif

                            @if($order->customer_notes)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-1">Uwagi klienta</h3>
                                    <p class="text-gray-900">{{ $order->customer_notes }}</p>
                                </div>
                            @endif

                            @if($order->bakery_notes)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-1">Uwagi piekarni</h3>
                                    <p class="text-gray-900 bg-blue-50 p-3 rounded-md">{{ $order->bakery_notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Lista produktów -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">🛒 Zamówione produkty</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $item->product_name }}</h3>
                                        @if($item->product)
                                            <p class="text-sm text-gray-600">{{ $item->product->opis ?? 'Brak opisu' }}</p>
                                        @endif
                                        @if($item->notes)
                                            <p class="text-sm text-blue-600 mt-1">Uwagi: {{ $item->notes }}</p>
                                        @endif

                                        <!-- Status pozycji -->
                                        @if($item->delivery_status)
                                            <span class="inline-flex mt-2 px-2 py-1 text-xs font-semibold rounded-full
                                                {{ $item->delivery_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $item->delivery_status === 'in_delivery' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $item->delivery_status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $item->delivery_status === 'shortage' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ $item->delivery_status_label ?? $item->delivery_status }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="text-right ml-6">
                                        <div class="text-lg font-semibold text-gray-900">{{ $item->quantity }} szt</div>
                                        <div class="text-sm text-gray-600">
                                            {{ number_format($item->unit_price_gross, 2) }} zł/szt
                                            @if($item->discount_percent > 0)
                                                <span class="text-red-600">(-{{ $item->discount_percent }}%)</span>
                                            @endif
                                        </div>
                                        <div class="text-lg font-bold text-gray-900">
                                            {{ number_format($item->line_total_gross, 2) }} zł
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Podsumowanie finansowe -->
            <div>
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">💰 Podsumowanie</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Wartość netto:</span>
                                <span class="font-medium">{{ number_format($order->subtotal, 2) }} zł</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">VAT:</span>
                                <span class="font-medium">{{ number_format($order->tax_amount, 2) }} zł</span>
                            </div>
                            @if($order->delivery_cost > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Koszt dostawy:</span>
                                    <span class="font-medium">{{ number_format($order->delivery_cost, 2) }} zł</span>
                                </div>
                            @endif
                            @if($order->discount_amount > 0)
                                <div class="flex justify-between text-red-600">
                                    <span>Rabat:</span>
                                    <span class="font-medium">-{{ number_format($order->discount_amount, 2) }} zł</span>
                                </div>
                            @endif
                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Razem do zapłaty:</span>
                                    <span>{{ number_format($order->total_amount, 2) }} zł</span>
                                </div>
                            </div>
                        </div>

                        <!-- Informacje o płatności -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-sm font-medium text-gray-500 mb-3">Informacje o płatności</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Metoda:</span>
                                    <span>{{ $order->payment_method_label }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="
                                        {{ $order->payment_status === 'pending' ? 'text-yellow-600' : '' }}
                                        {{ $order->payment_status === 'paid' ? 'text-green-600' : '' }}
                                        {{ $order->payment_status === 'overdue' ? 'text-red-600' : '' }}">
                                        {{ $order->payment_status_label }}
                                    </span>
                                </div>
                                @if($order->payment_due_date)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Termin płatności:</span>
                                        <span>{{ $order->payment_due_date->format('d.m.Y') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historia statusów -->
                @if($order->status_history)
                    <div class="bg-white shadow rounded-lg mt-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900">📈 Historia statusów</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach($order->status_history as $status)
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium">{{ $status['status_label'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $status['date'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Script dla drukowania -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('print-order', () => {
                window.print();
            });
        });

        // Style dla drukowania
        const printStyles = `
            @media print {
                body * {
                    visibility: hidden;
                }
                #order-printable, #order-printable * {
                    visibility: visible;
                }
                #order-printable {
                    position: absolute;
                    left: 0;
                    top: 0;
                }
                .no-print {
                    display: none !important;
                }
            }
        `;

        const styleSheet = document.createElement("style");
        styleSheet.type = "text/css";
        styleSheet.innerText = printStyles;
        document.head.appendChild(styleSheet);
    </script>
</div>
