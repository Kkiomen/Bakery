<div class="min-h-screen bg-gray-50">
    <!-- Nagłówek -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🏢 Zarządzanie Klientami B2B</h1>
                    <p class="text-gray-600">Panel administracyjny</p>
                </div>
                <div class="flex space-x-4">
                    <button wire:click="openCreateModal"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        ➕ Nowy klient
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statystyki -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">👥</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Wszyscy klienci</dt>
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
                            <span class="text-2xl">✅</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Aktywni</dt>
                                <dd class="text-lg font-medium text-green-600">{{ $stats['active'] }}</dd>
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
                                <dt class="text-sm font-medium text-gray-500 truncate">Oczekujący</dt>
                                <dd class="text-lg font-medium text-yellow-600">{{ $stats['pending'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">🚫</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Zawieszeni</dt>
                                <dd class="text-lg font-medium text-red-600">{{ $stats['suspended'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📋</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Zamówienia</dt>
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
                            <span class="text-2xl">💰</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Obrót</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_value'], 0) }} zł</dd>
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
                               placeholder="Nazwa, email, NIP..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model.live="statusFilter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Typ biznesu</label>
                        <select wire:model.live="businessTypeFilter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @foreach($businessTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Poziom cenowy</label>
                        <select wire:model.live="pricingTierFilter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @foreach($pricingTiers as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Na stronie</label>
                        <select wire:model.live="perPage"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista klientów -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Lista klientów B2B</h3>
            </div>

            @forelse($clients as $client)
                <div class="border-b border-gray-200 last:border-b-0">
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-4">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $client->company_name }}
                                        </h3>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $client->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $client->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $client->status === 'suspended' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $client->status === 'inactive' ? 'bg-gray-100 text-gray-800' : '' }}">
                                            {{ $client->status_label }}
                                        </span>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $client->pricing_tier_label }}
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">{{ $client->orders->count() }} zamówień</p>
                                        <p class="text-lg font-bold text-gray-900">
                                            {{ number_format($client->orders->sum('total_amount'), 2) }} zł
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm text-gray-600">
                                    <div>
                                        <span class="font-medium">Email:</span><br>
                                        {{ $client->email }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Typ biznesu:</span><br>
                                        {{ $client->business_type_label }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Limit kredytowy:</span><br>
                                        {{ number_format($client->credit_limit, 2) }} zł
                                        @if($client->current_balance > 0)
                                            <br><span class="text-red-600">Zadłużenie: {{ number_format($client->current_balance, 2) }} zł</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-medium">Kontakt:</span><br>
                                        {{ $client->contact_person ?: 'Brak' }}<br>
                                        {{ $client->contact_phone ?: $client->phone }}
                                    </div>
                                </div>

                                <div class="mt-3 text-sm text-gray-600">
                                    <span class="font-medium">Adres:</span>
                                    {{ $client->full_address }}
                                </div>

                                @if($client->notes)
                                    <div class="mt-3 p-3 bg-gray-50 rounded-md">
                                        <span class="font-medium text-gray-700">Uwagi:</span>
                                        <p class="text-sm text-gray-600 mt-1">{{ $client->notes }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="ml-6 flex flex-col space-y-2">
                                <button wire:click="openDetailsModal({{ $client->id }})"
                                        class="bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700">
                                    👁️ Szczegóły
                                </button>
                                <button wire:click="openEditModal({{ $client->id }})"
                                        class="bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">
                                    ✏️ Edytuj
                                </button>
                                <button wire:click="toggleStatus({{ $client->id }})"
                                        class="px-3 py-2 rounded text-sm
                                            {{ $client->status === 'active' ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-green-600 hover:bg-green-700 text-white' }}">
                                    {{ $client->status === 'active' ? '🚫 Zawieś' : '✅ Aktywuj' }}
                                </button>
                                <button wire:click="resetPassword({{ $client->id }})"
                                        onclick="return confirm('Czy na pewno chcesz zresetować hasło?')"
                                        class="bg-yellow-600 text-white px-3 py-2 rounded text-sm hover:bg-yellow-700">
                                    🔑 Reset hasła
                                </button>
                                @if($client->orders->count() == 0)
                                    <button wire:click="deleteClient({{ $client->id }})"
                                            onclick="return confirm('Czy na pewno chcesz usunąć tego klienta?')"
                                            class="bg-red-600 text-white px-3 py-2 rounded text-sm hover:bg-red-700">
                                        🗑️ Usuń
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <span class="text-gray-400 text-6xl">👥</span>
                    <h3 class="text-lg font-medium text-gray-900 mt-4">Brak klientów B2B</h3>
                    <p class="text-gray-600">Nie znaleźliśmy klientów spełniających kryteria wyszukiwania.</p>
                    <div class="mt-6">
                        <button wire:click="openCreateModal"
                                class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                            ➕ Dodaj pierwszego klienta
                        </button>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Paginacja -->
        @if($clients->hasPages())
            <div class="mt-8">
                {{ $clients->links() }}
            </div>
        @endif
    </div>

    <!-- Modal tworzenia klienta -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">➕ Nowy klient B2B</h3>
                    <button wire:click="closeCreateModal"
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit="createClient" class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    @include('livewire.admin.partials.client-form')

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeCreateModal"
                                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                            Anuluj
                        </button>
                        <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            💾 Utwórz klienta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal edycji klienta -->
    @if($showEditModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">✏️ Edycja klienta B2B</h3>
                    <button wire:click="closeEditModal"
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit="updateClient" class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    @include('livewire.admin.partials.client-form')

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeEditModal"
                                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                            Anuluj
                        </button>
                        <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            💾 Zapisz zmiany
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal szczegółów klienta -->
    @if($showDetailsModal && $selectedClient)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-6xl w-full max-h-[90vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-xl font-medium text-gray-900">👁️ Szczegóły klienta: {{ $selectedClient->company_name }}</h3>
                    <button wire:click="closeDetailsModal"
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)] space-y-6">
                    <!-- Informacje podstawowe -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-4">📋 Podstawowe informacje</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nazwa firmy</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedClient->company_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">NIP</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedClient->nip ?: '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedClient->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Telefon</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedClient->phone ?: '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="text-sm">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($selectedClient->status === 'active') bg-green-100 text-green-800
                                            @elseif($selectedClient->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($selectedClient->status === 'suspended') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($selectedClient->status) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-4">📍 Adres i kontakt</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Adres</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedClient->address }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Kod pocztowy</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedClient->postal_code }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Miasto</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedClient->city }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Osoba kontaktowa</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedClient->contact_person ?: '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Telefon kontaktowy</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedClient->contact_phone ?: '-' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Ostatnie zamówienia -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-semibold text-gray-900">📦 Ostatnie zamówienia ({{ $selectedClient->orders->count() }})</h4>
                            <a href="{{ route('admin.b2b-orders') }}?clientFilter={{ $selectedClient->id }}"
                               class="text-blue-600 hover:text-blue-800 text-sm">
                                Zobacz wszystkie →
                            </a>
                        </div>

                        @if($selectedClient->orders->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Numer</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Wartość</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Akcje</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($selectedClient->orders as $order)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $order->numer_zamowienia }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $order->created_at->format('d.m.Y') }}</td>
                                            <td class="px-4 py-2">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    @switch($order->status)
                                                        @case('oczekujace') bg-yellow-100 text-yellow-800 @break
                                                        @case('potwierdzone') bg-blue-100 text-blue-800 @break
                                                        @case('w_produkcji') bg-orange-100 text-orange-800 @break
                                                        @case('zrealizowane') bg-green-100 text-green-800 @break
                                                        @default bg-gray-100 text-gray-800
                                                    @endswitch
                                                ">
                                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ number_format($order->wartosc_brutto, 2) }} zł</td>
                                            <td class="px-4 py-2">
                                                <button wire:click="goToOrder({{ $order->id }})"
                                                        class="text-blue-600 hover:text-blue-800 text-xs">
                                                    Zobacz
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">Brak zamówień</p>
                        @endif
                    </div>

                    <!-- Zamówienia cykliczne -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-900 mb-4">🔄 Zamówienia cykliczne ({{ $selectedClient->recurringOrders->count() }})</h4>

                        @if($selectedClient->recurringOrders->count() > 0)
                            <div class="space-y-3">
                                @foreach($selectedClient->recurringOrders as $recurringOrder)
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h5 class="font-medium text-gray-900">{{ $recurringOrder->name }}</h5>
                                            <p class="text-sm text-gray-500">{{ $recurringOrder->description }}</p>
                                            <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                                                <span>Częstotliwość: {{ $recurringOrder->frequency }}</span>
                                                <span>Następne: {{ $recurringOrder->next_generation_at ? $recurringOrder->next_generation_at->format('d.m.Y') : '-' }}</span>
                                                <span class="inline-flex px-2 py-1 rounded-full
                                                    {{ $recurringOrder->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ $recurringOrder->is_active ? 'Aktywne' : 'Nieaktywne' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-medium text-gray-900">{{ number_format($recurringOrder->estimated_total, 2) }} zł</div>
                                            <div class="text-xs text-gray-500">szacowana wartość</div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">Brak zamówień cyklicznych</p>
                        @endif
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                    <button wire:click="impersonateClient({{ $selectedClient->id }})"
                            class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">
                        👤 Przełącz na klienta
                    </button>
                    <button wire:click="closeDetailsModal"
                            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        Zamknij
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
