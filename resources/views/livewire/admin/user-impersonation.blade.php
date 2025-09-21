<div class="min-h-screen bg-gray-50">
    <!-- Nagłówek -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">👤 Przełączanie na konta B2B</h1>
                    <p class="text-gray-600">Testowanie platformy z perspektywy klienta</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('admin.b2b-clients') }}"
                       class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        ← Panel B2B
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Informacja o aktualnym przełączeniu -->
        @if(session()->has('impersonating'))
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-yellow-600 text-xl mr-3">⚠️</span>
                        <div>
                            <h3 class="text-yellow-800 font-medium">Jesteś przełączony na inne konto</h3>
                            <p class="text-yellow-700 text-sm">
                                Aktualnie testujesz platformę jako klient B2B.
                                Rozpoczęto: {{ session('impersonating.started_at')->format('d.m.Y H:i') }}
                            </p>
                        </div>
                    </div>
                    <button wire:click="stopImpersonation"
                            class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700">
                        🔄 Wróć do konta administratora
                    </button>
                </div>
            </div>
        @endif

        <!-- Filtry -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Szukaj</label>
                        <input type="text" wire:model.live="search"
                               placeholder="Nazwa firmy, email, NIP..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model.live="statusFilter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Wszystkie</option>
                            <option value="pending">Oczekujący</option>
                            <option value="active">Aktywny</option>
                            <option value="suspended">Zawieszony</option>
                            <option value="inactive">Nieaktywny</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Typ biznesu</label>
                        <select wire:model.live="businessTypeFilter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Wszystkie</option>
                            <option value="hotel">Hotel</option>
                            <option value="restaurant">Restauracja</option>
                            <option value="cafe">Kawiarnia</option>
                            <option value="shop">Sklep</option>
                            <option value="catering">Catering</option>
                            <option value="other">Inne</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista klientów -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Dostępne konta klientów B2B</h3>
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
                                            {{ number_format($client->orders->sum('gross_value'), 2) }} zł
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

                                <!-- Dane logowania -->
                                <div class="mt-3 p-3 bg-blue-50 rounded-md">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium text-blue-700">Login:</span>
                                            <span class="text-blue-900 font-mono">{{ $client->email }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-blue-700">Hasło:</span>
                                            <span class="text-blue-900 font-mono">password123</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ml-6 flex flex-col space-y-2">
                                @if($client->status === 'active')
                                    <button wire:click="impersonateUser({{ $client->id }})"
                                            class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                                        🎭 Przełącz się na to konto
                                    </button>
                                @else
                                    <div class="bg-gray-100 text-gray-500 px-4 py-2 rounded text-sm text-center">
                                        Konto nieaktywne
                                    </div>
                                @endif

                                <div class="text-center">
                                    <a href="{{ route('b2b.login') }}" target="_blank"
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        🔗 Otwórz logowanie B2B
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <span class="text-gray-400 text-6xl">👥</span>
                    <h3 class="text-lg font-medium text-gray-900 mt-4">Brak klientów B2B</h3>
                    <p class="text-gray-600">Nie znaleźliśmy klientów spełniających kryteria wyszukiwania.</p>
                </div>
            @endforelse
        </div>

        <!-- Paginacja -->
        @if($clients->hasPages())
            <div class="mt-8">
                {{ $clients->links() }}
            </div>
        @endif

        <!-- Instrukcje -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-blue-900 mb-4">📋 Instrukcje testowania</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-blue-800">
                <div>
                    <h4 class="font-medium mb-2">🎭 Przełączanie kont:</h4>
                    <ul class="space-y-1 text-blue-700">
                        <li>• Kliknij "Przełącz się na to konto" przy wybranym kliencie</li>
                        <li>• Zostaniesz automatycznie zalogowany jako ten klient</li>
                        <li>• Możesz testować wszystkie funkcje B2B</li>
                        <li>• Kliknij "Wróć do konta administratora" aby wrócić</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium mb-2">🔐 Manualne logowanie:</h4>
                    <ul class="space-y-1 text-blue-700">
                        <li>• Otwórz nową kartę przeglądarki</li>
                        <li>• Wejdź na <code class="bg-blue-100 px-1 rounded">/b2b/login</code></li>
                        <li>• Użyj emaila klienta jako loginu</li>
                        <li>• Hasło: <code class="bg-blue-100 px-1 rounded">password123</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
