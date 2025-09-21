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
</div>
