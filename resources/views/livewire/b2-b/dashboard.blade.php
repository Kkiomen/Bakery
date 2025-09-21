<div class="min-h-screen bg-gray-50">
    <!-- Nagłówek -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🏢 Portal B2B</h1>
                    <p class="text-gray-600">{{ Auth::guard('b2b')->user()->company_name }}</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('b2b.catalog') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        🛒 Katalog produktów
                    </a>
                    <a href="{{ route('b2b.orders.index') }}"
                       class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        📋 Moje zamówienia
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statystyki -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <span class="text-white text-sm font-medium">📊</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Wszystkie zamówienia</dt>
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
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <span class="text-white text-sm font-medium">⏳</span>
                            </div>
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
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <span class="text-white text-sm font-medium">📅</span>
                            </div>
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
        </div>

        <!-- Limit kredytowy -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">💳 Status kredytowy</h3>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm text-gray-600">Dostępny kredyt</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($stats['available_credit'], 2) }} zł</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Limit kredytowy</p>
                        <p class="text-lg font-medium text-gray-900">{{ number_format($stats['credit_limit'], 2) }} zł</p>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full"
                         style="width: {{ ($stats['available_credit'] / $stats['credit_limit']) * 100 }}%"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Ostatnie zamówienia -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">📋 Ostatnie zamówienia</h3>
                </div>
                <div class="p-6">
                    @forelse($recentOrders as $order)
                        <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-b-0">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $order->order_number }}</p>
                                <p class="text-sm text-gray-500">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ number_format($order->total_amount, 2) }} zł</p>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $order->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Brak zamówień</p>
                    @endforelse

                    @if($recentOrders->count() > 0)
                        <div class="mt-4">
                            <a href="{{ route('b2b.orders.index') }}"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Zobacz wszystkie zamówienia →
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Zamówienia cykliczne -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">🔄 Zamówienia cykliczne</h3>
                </div>
                <div class="p-6">
                    @forelse($recurringOrders as $recurringOrder)
                        <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-b-0">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $recurringOrder->name }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $recurringOrder->frequency_label }}
                                    @if($recurringOrder->next_generation_at)
                                        - następne: {{ $recurringOrder->next_generation_at->format('d.m.Y') }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ number_format($recurringOrder->estimated_total, 2) }} zł</p>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $recurringOrder->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $recurringOrder->is_active ? 'Aktywne' : 'Nieaktywne' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <p class="text-gray-500 mb-3">Brak zamówień cyklicznych</p>
                            <a href="{{ route('b2b.recurring-orders') }}"
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                ➕ Utwórz pierwsze zamówienie cykliczne
                            </a>
                        </div>
                    @endforelse

                    @if($recurringOrders->count() > 0)
                        <div class="mt-4 text-center">
                            <a href="{{ route('b2b.recurring-orders') }}"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Zarządzaj zamówieniami cyklicznymi →
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Szybkie akcje -->
        <div class="mt-8">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h4 class="text-lg font-medium text-blue-900 mb-4">🚀 Szybkie akcje</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('b2b.catalog') }}"
                       class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow">
                        <div class="text-blue-600 text-2xl mb-2">🛒</div>
                        <h5 class="font-medium text-gray-900">Przeglądaj katalog</h5>
                        <p class="text-sm text-gray-600">Zobacz nasze produkty i ceny</p>
                    </a>

                    <a href="{{ route('b2b.orders.create') }}"
                       class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow">
                        <div class="text-green-600 text-2xl mb-2">➕</div>
                        <h5 class="font-medium text-gray-900">Złóż zamówienie</h5>
                        <p class="text-sm text-gray-600">Utwórz nowe zamówienie</p>
                    </a>

                    <a href="{{ route('b2b.orders.index') }}"
                       class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow">
                        <div class="text-purple-600 text-2xl mb-2">📋</div>
                        <h5 class="font-medium text-gray-900">Historia zamówień</h5>
                        <p class="text-sm text-gray-600">Przeglądaj swoje zamówienia</p>
                    </a>

                    <a href="{{ route('b2b.profile') }}"
                       class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow">
                        <div class="text-orange-600 text-2xl mb-2">⚙️</div>
                        <h5 class="font-medium text-gray-900">Ustawienia konta</h5>
                        <p class="text-sm text-gray-600">Zarządzaj swoim profilem</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
