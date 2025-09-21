<div class="min-h-screen bg-gray-50">
    <!-- Nagłówek -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🛒 Składanie Zamówienia</h1>
                    <p class="text-gray-600">{{ Auth::guard('b2b')->user()->company_name }}</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('b2b.catalog') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        ← Katalog
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                        {{ $step >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                        1
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $step >= 1 ? 'text-blue-600' : 'text-gray-500' }}">
                        Przegląd koszyka
                    </span>
                </div>

                <div class="w-16 h-1 {{ $step >= 2 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>

                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                        {{ $step >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                        2
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $step >= 2 ? 'text-blue-600' : 'text-gray-500' }}">
                        Szczegóły dostawy
                    </span>
                </div>

                <div class="w-16 h-1 {{ $step >= 3 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>

                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                        {{ $step >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                        3
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $step >= 3 ? 'text-blue-600' : 'text-gray-500' }}">
                        Podsumowanie
                    </span>
                </div>
            </div>
        </div>

        @if($step == 1)
            <!-- Krok 1: Przegląd koszyka -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">🛒 Przegląd koszyka</h2>
                </div>
                <div class="p-6">
                    @if(!empty($cart))
                        <div class="space-y-4">
                            @foreach($cart as $key => $item)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $item['product_name'] }}</h3>
                                        <p class="text-sm text-gray-600">
                                            {{ number_format($item['unit_price'], 2) }} zł/szt netto
                                            @if($item['discount_percent'] > 0)
                                                <span class="text-red-600">(-{{ $item['discount_percent'] }}%)</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <input type="number"
                                               wire:model.live="cart.{{ $key }}.quantity"
                                               wire:change="updateCartQuantity('{{ $key }}', $event.target.value)"
                                               min="1"
                                               class="w-20 px-3 py-2 border border-gray-300 rounded-md text-center">
                                        <div class="text-right">
                                            <div class="font-semibold text-gray-900">
                                                {{ number_format($item['line_total_gross'] ?? 0, 2) }} zł
                                            </div>
                                            <div class="text-sm text-gray-600">brutto</div>
                                        </div>
                                        <button wire:click="removeFromCart('{{ $key }}')"
                                                class="text-red-600 hover:text-red-800 p-2">
                                            🗑️
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Podsumowanie koszyka -->
                        <div class="mt-6 border-t border-gray-200 pt-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-600">Wartość netto:</span>
                                    <span class="font-medium">{{ number_format($this->getCartTotal(), 2) }} zł</span>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-600">VAT:</span>
                                    <span class="font-medium">{{ number_format($this->getCartTaxAmount(), 2) }} zł</span>
                                </div>
                                <div class="flex justify-between items-center text-lg font-bold border-t border-gray-200 pt-2">
                                    <span>Razem brutto:</span>
                                    <span>{{ number_format($this->getCartTotalGross(), 2) }} zł</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">
                                    Łącznie {{ $this->getCartItemsCount() }} produktów
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-between">
                            <a href="{{ route('b2b.catalog') }}"
                               class="bg-gray-300 text-gray-700 px-6 py-3 rounded-md hover:bg-gray-400">
                                ← Kontynuuj zakupy
                            </a>
                            <button wire:click="nextStep"
                                    class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                                Dalej →
                            </button>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <span class="text-gray-400 text-6xl">🛒</span>
                            <h3 class="text-lg font-medium text-gray-900 mt-4">Koszyk jest pusty</h3>
                            <p class="text-gray-600">Dodaj produkty do koszyka, aby złożyć zamówienie.</p>
                            <div class="mt-6">
                                <a href="{{ route('b2b.catalog') }}"
                                   class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                                    Przejdź do katalogu
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        @elseif($step == 2)
            <!-- Krok 2: Szczegóły dostawy -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">📦 Szczegóły dostawy</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Data dostawy <span class="text-red-500">*</span>
                            </label>
                            <input type="date" wire:model="deliveryDate"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('deliveryDate') border-red-500 @enderror">
                            @error('deliveryDate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Typ zamówienia</label>
                            <select wire:model="orderType"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="one_time">Jednorazowe</option>
                                <option value="recurring">Cykliczne</option>
                                <option value="standing">Stałe</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Godzina dostawy od</label>
                            <input type="time" wire:model="deliveryTimeFrom"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Godzina dostawy do</label>
                            <input type="time" wire:model="deliveryTimeTo"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Adres dostawy <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="deliveryAddress"
                                   placeholder="Ulica i numer"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('deliveryAddress') border-red-500 @enderror">
                            @error('deliveryAddress')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Kod pocztowy <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="deliveryPostalCode"
                                   placeholder="00-000"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('deliveryPostalCode') border-red-500 @enderror">
                            @error('deliveryPostalCode')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Miasto <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="deliveryCity"
                                   placeholder="Nazwa miasta"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('deliveryCity') border-red-500 @enderror">
                            @error('deliveryCity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Uwagi do dostawy</label>
                            <textarea wire:model="deliveryNotes"
                                      rows="3"
                                      placeholder="Dodatkowe informacje dla kuriera..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Uwagi do zamówienia</label>
                            <textarea wire:model="customerNotes"
                                      rows="3"
                                      placeholder="Dodatkowe uwagi do zamówienia..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Metoda płatności</label>
                            <select wire:model="paymentMethod"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="transfer">Przelew bankowy</option>
                                <option value="card">Karta płatnicza</option>
                                <option value="cash">Gotówka przy odbiorze</option>
                                <option value="credit">Na kredyt (14 dni)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between">
                        <button wire:click="previousStep"
                                class="bg-gray-300 text-gray-700 px-6 py-3 rounded-md hover:bg-gray-400">
                            ← Wróć
                        </button>
                        <button wire:click="nextStep"
                                class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                            Dalej →
                        </button>
                    </div>
                </div>
            </div>

        @elseif($step == 3)
            <!-- Krok 3: Podsumowanie -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <!-- Podsumowanie zamówienia -->
                    <div class="bg-white shadow rounded-lg mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900">📋 Podsumowanie zamówienia</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @foreach($cart as $item)
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900">{{ $item['product_name'] }}</h3>
                                            <p class="text-sm text-gray-600">
                                                {{ number_format($item['unit_price_gross'], 2) }} zł/szt × {{ $item['quantity'] }} szt
                                            </p>
                                        </div>
                                        <div class="font-bold text-gray-900">
                                            {{ number_format($item['line_total_gross'], 2) }} zł
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Szczegóły dostawy -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900">📦 Szczegóły dostawy</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700">Data dostawy:</span>
                                    <p class="text-gray-900">{{ \Carbon\Carbon::parse($deliveryDate)->format('d.m.Y') }}</p>
                                </div>
                                @if($deliveryTimeFrom)
                                    <div>
                                        <span class="font-medium text-gray-700">Godzina:</span>
                                        <p class="text-gray-900">{{ $deliveryTimeFrom }} - {{ $deliveryTimeTo ?: '...' }}</p>
                                    </div>
                                @endif
                                <div class="md:col-span-2">
                                    <span class="font-medium text-gray-700">Adres:</span>
                                    <p class="text-gray-900">{{ $deliveryAddress }}, {{ $deliveryPostalCode }} {{ $deliveryCity }}</p>
                                </div>
                                @if($deliveryNotes)
                                    <div class="md:col-span-2">
                                        <span class="font-medium text-gray-700">Uwagi do dostawy:</span>
                                        <p class="text-gray-900">{{ $deliveryNotes }}</p>
                                    </div>
                                @endif
                                @if($customerNotes)
                                    <div class="md:col-span-2">
                                        <span class="font-medium text-gray-700">Uwagi do zamówienia:</span>
                                        <p class="text-gray-900">{{ $customerNotes }}</p>
                                    </div>
                                @endif
                                <div>
                                    <span class="font-medium text-gray-700">Płatność:</span>
                                    <p class="text-gray-900">
                                        @switch($paymentMethod)
                                            @case('transfer') Przelew bankowy @break
                                            @case('card') Karta płatnicza @break
                                            @case('cash') Gotówka przy odbiorze @break
                                            @case('credit') Na kredyt (14 dni) @break
                                        @endswitch
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Podsumowanie finansowe -->
                <div>
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900">💰 Do zapłaty</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Wartość netto:</span>
                                    <span class="font-medium">{{ number_format($this->getCartTotal(), 2) }} zł</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">VAT:</span>
                                    <span class="font-medium">{{ number_format($this->getCartTaxAmount(), 2) }} zł</span>
                                </div>
                                <div class="border-t border-gray-200 pt-3">
                                    <div class="flex justify-between text-xl font-bold">
                                        <span>Razem:</span>
                                        <span>{{ number_format($this->getCartTotalGross(), 2) }} zł</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 space-y-3">
                                <button wire:click="placeOrder"
                                        class="w-full bg-green-600 text-white py-4 rounded-md hover:bg-green-700 font-bold text-lg">
                                    ✅ Złóż zamówienie
                                </button>
                                <button wire:click="previousStep"
                                        class="w-full bg-gray-300 text-gray-700 py-2 rounded-md hover:bg-gray-400">
                                    ← Wróć do edycji
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
