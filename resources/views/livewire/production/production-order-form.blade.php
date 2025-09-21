<div class="max-w-4xl mx-auto space-y-6">
    {{-- Nagłówek --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ $isEdit ? 'Edytuj zlecenie' : 'Nowe zlecenie produkcji' }}
            </h1>
            @if($isEdit && $order)
                <p class="text-gray-600">{{ $order->numer_zlecenia }}</p>
            @endif
        </div>
        <a href="{{ route('production.orders.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Powrót do listy
        </a>
    </div>

    <form wire:submit="save" class="space-y-6">
        {{-- Podstawowe informacje --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Podstawowe informacje</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nazwa zlecenia <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           wire:model="nazwa"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('nazwa') border-red-500 @enderror"
                           placeholder="np. Zamówienie sklep ABC">
                    @error('nazwa')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Data produkcji <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           wire:model="data_produkcji"
                           min="{{ now()->format('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('data_produkcji') border-red-500 @enderror">
                    @error('data_produkcji')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Odpowiedzialny <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="user_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('user_id') border-red-500 @enderror">
                        <option value="">Wybierz użytkownika</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priorytet</label>
                    <select wire:model="priorytet"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Typ zlecenia</label>
                    <select wire:model="typ_zlecenia"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @foreach($typeOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Klient</label>
                    <input type="text"
                           wire:model="klient"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Nazwa klienta/sklepu">
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Opis</label>
                <textarea wire:model="opis"
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Dodatkowy opis zlecenia"></textarea>
            </div>
        </div>

        {{-- Pozycje zlecenia --}}
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Pozycje zlecenia</h3>
                <span class="text-sm text-gray-500">{{ count($items) }} pozycji</span>
            </div>

            @error('items')
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                    <p class="text-sm text-red-600">{{ $message }}</p>
                </div>
            @enderror

            {{-- Lista pozycji --}}
            @if(count($items) > 0)
                <div class="space-y-3 mb-6">
                    @foreach($items as $index => $item)
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-md">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">
                                    {{ $this->getProductName($item['product_id']) }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $item['ilosc'] }} {{ $item['jednostka'] }}
                                    @if($item['uwagi'])
                                        - {{ $item['uwagi'] }}
                                    @endif
                                </div>
                            </div>
                            <button type="button"
                                    wire:click="removeItem({{ $index }})"
                                    class="text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Dodawanie nowej pozycji --}}
            <div class="border-t pt-6">
                <h4 class="text-md font-medium text-gray-900 mb-3">Dodaj pozycję</h4>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Produkt</label>
                        <div class="relative">
                            <select wire:model="newItem.product_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('newItem.product_id') border-red-500 @enderror">
                                <option value="">Wybierz produkt</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->nazwa }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('newItem.product_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ilość</label>
                        <input type="number"
                               wire:model="newItem.ilosc"
                               min="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('newItem.ilosc') border-red-500 @enderror">
                        @error('newItem.ilosc')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jednostka</label>
                        <select wire:model="newItem.jednostka"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="szt">szt</option>
                            <option value="kg">kg</option>
                            <option value="g">g</option>
                            <option value="l">l</option>
                            <option value="ml">ml</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Uwagi</label>
                    <input type="text"
                           wire:model="newItem.uwagi"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Dodatkowe uwagi do pozycji">
                </div>

                <div class="mt-4">
                    <button type="button"
                            wire:click="addItem"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Dodaj pozycję
                    </button>
                </div>
            </div>
        </div>

        {{-- Uwagi --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Dodatkowe uwagi</h3>
            <textarea wire:model="uwagi"
                      rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Dodatkowe uwagi do całego zlecenia"></textarea>
        </div>

        {{-- Przyciski akcji --}}
        <div class="flex justify-end space-x-3">
            <a href="{{ route('production.orders.index') }}"
               class="px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Anuluj
            </a>
            <button type="submit"
                    class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                {{ $isEdit ? 'Zaktualizuj zlecenie' : 'Utwórz zlecenie' }}
            </button>
        </div>
    </form>
</div>
