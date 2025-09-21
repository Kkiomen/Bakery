<div class="space-y-6">
    <!-- Nagłówek -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">✏️ Edycja Kontrahenta</h1>
            <p class="text-sm text-gray-600">{{ $contractor->nazwa }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('contractors.index') }}"
               class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                ← Powrót do listy
            </a>
        </div>
    </div>

    <!-- Formularz edycji -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Dane Kontrahenta</h3>
        </div>

        <form wire:submit="save" class="p-6 space-y-6">
            <!-- Podstawowe dane -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-md font-medium text-gray-900 mb-4">Podstawowe Dane</h4>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nazwa kontrahenta <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="nazwa" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('nazwa') border-red-500 @enderror" />
                        @error('nazwa')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Typ kontrahenta</label>
                        <select wire:model="typ"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="klient">Klient</option>
                            <option value="dostawca">Dostawca</option>
                            <option value="obydwa">Klient i Dostawca</option>
                        </select>
                        @error('typ')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                        <input type="text" wire:model="nip"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('nip') border-red-500 @enderror" />
                        @error('nip')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">REGON</label>
                        <input type="text" wire:model="regon"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('regon') border-red-500 @enderror" />
                        @error('regon')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Adres -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-md font-medium text-gray-900 mb-4">Adres</h4>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Adres <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="adres" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('adres') border-red-500 @enderror" />
                        @error('adres')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Kod pocztowy <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="kod_pocztowy" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('kod_pocztowy') border-red-500 @enderror" />
                        @error('kod_pocztowy')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Miasto <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="miasto" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('miasto') border-red-500 @enderror" />
                        @error('miasto')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Dane kontaktowe -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-md font-medium text-gray-900 mb-4">Dane Kontaktowe</h4>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefon główny</label>
                        <input type="text" wire:model="telefon"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('telefon') border-red-500 @enderror" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" wire:model="email"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Osoba kontaktowa</label>
                        <input type="text" wire:model="osoba_kontaktowa"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('osoba_kontaktowa') border-red-500 @enderror" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefon kontaktowy</label>
                        <input type="text" wire:model="telefon_kontaktowy"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('telefon_kontaktowy') border-red-500 @enderror" />
                    </div>
                </div>
            </div>

            <!-- Dodatkowe informacje -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-md font-medium text-gray-900 mb-4">Dodatkowe Informacje</h4>

                <div class="space-y-4">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="aktywny" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Kontrahent aktywny</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Uwagi</label>
                        <textarea wire:model="uwagi" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                </div>
            </div>

            <!-- Przyciski -->
            <div class="flex justify-end space-x-3">
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    💾 Zapisz zmiany
                </button>
            </div>
        </form>
    </div>

    <!-- Zlecenia kontrahenta -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">📋 Zlecenia Kontrahenta</h3>
        </div>

        <div class="p-6">
            <!-- Filtry dla zleceń -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model.live="orderStatusFilter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Wszystkie</option>
                        <option value="planowane">Planowane</option>
                        <option value="w_trakcie">W trakcie</option>
                        <option value="zakonczone">Zakończone</option>
                        <option value="anulowane">Anulowane</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data od</label>
                    <input type="date" wire:model.live="orderDateFrom"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data do</label>
                    <input type="date" wire:model.live="orderDateTo"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Szukaj</label>
                    <input type="text" wire:model.live="orderSearch" placeholder="Numer, klient, nazwa..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Lista zleceń -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Zlecenie
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Klient
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data produkcji
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pozycje
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Akcje
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($contractorOrders as $order)
                            <tr class="hover:bg-gray-50 {{ $order->status === 'zakonczone' ? 'opacity-75' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->numer_zlecenia }}</div>
                                    @if($order->nazwa)
                                        <div class="text-sm text-gray-500">{{ $order->nazwa }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $order->klient ?: '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $order->data_produkcji->format('d.m.Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $order->status === 'planowane' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $order->status === 'w_trakcie' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $order->status === 'zakonczone' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $order->status === 'anulowane' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->items->count() }} pozycji
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button wire:click="unassignOrderFromContractor({{ $order->id }})"
                                            onclick="return confirm('Czy na pewno chcesz odłączyć to zlecenie od kontrahenta?')"
                                            class="text-red-600 hover:text-red-900">
                                        🔗❌ Odłącz
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    Brak zleceń przypisanych do tego kontrahenta
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginacja zleceń kontrahenta -->
            <div class="mt-4">
                {{ $contractorOrders->links() }}
            </div>
        </div>
    </div>

    <!-- Dostępne zlecenia do przypisania -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">📌 Dostępne Zlecenia do Przypisania</h3>
            <p class="text-sm text-gray-600">Zlecenia bez przypisanego kontrahenta (ostatnie 20)</p>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Zlecenie
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Klient
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data produkcji
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Akcje
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($availableOrders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->numer_zlecenia }}</div>
                                    @if($order->nazwa)
                                        <div class="text-sm text-gray-500">{{ $order->nazwa }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $order->klient ?: '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $order->data_produkcji->format('d.m.Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $order->status === 'planowane' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $order->status === 'w_trakcie' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $order->status === 'zakonczone' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $order->status === 'anulowane' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button wire:click="assignOrderToContractor({{ $order->id }})"
                                            class="text-blue-600 hover:text-blue-900">
                                        🔗 Przypisz
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Brak dostępnych zleceń do przypisania
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
