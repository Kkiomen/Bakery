<div class="space-y-6">
    <form wire:submit="createDelivery">
        <!-- Production Order Selection -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Zlecenie Produkcyjne</h3>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Wybierz zlecenie <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="productionOrderId"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('productionOrderId') border-red-500 @enderror">
                        <option value="">Wybierz zlecenie produkcyjne</option>
                        @foreach($availableProductionOrders as $order)
                            <option value="{{ $order->id }}">
                                {{ $order->numer_zlecenia }} - {{ $order->nazwa }}
                                ({{ $order->data_produkcji->format('d.m.Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('productionOrderId')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                @if($selectedProductionOrder)
                <div class="bg-white rounded-lg border p-4">
                    <h4 class="font-medium text-gray-900 mb-2">Szczegóły zlecenia</h4>
                    <div class="text-sm text-gray-600 space-y-1">
                        <div><strong>Klient:</strong> {{ $selectedProductionOrder->klient ?: 'Nie określono' }}</div>
                        <div><strong>Data produkcji:</strong> {{ $selectedProductionOrder->data_produkcji->format('d.m.Y') }}</div>
                        <div><strong>Status:</strong> {{ $selectedProductionOrder->status_label }}</div>
                        <div><strong>Pozycji:</strong> {{ $selectedProductionOrder->items->count() }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Delivery Details -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Szczegóły Dostawy</h3>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data dostawy <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="data_dostawy" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('data_dostawy') border-red-500 @enderror" />
                    </div>
                    @error('data_dostawy')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Godzina planowana</label>
                        <input type="time" wire:model="godzina_planowana"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('godzina_planowana') border-red-500 @enderror" />
                    </div>
                    @error('godzina_planowana')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Priorytet <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="priorytet"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('priorytet') border-red-500 @enderror">
                        <option value="niski">Niski</option>
                        <option value="normalny">Normalny</option>
                        <option value="wysoki">Wysoki</option>
                        <option value="pilny">Pilny</option>
                    </select>
                    @error('priorytet')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Kierowca (opcjonalnie)
                    </label>
                    <select wire:model="driver_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Przypisz później</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kolejność dostawy</label>
                        <flux:input type="number" wire:model="kolejnosc_dostawy" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('kolejnosc_dostawy') border-red-500 @enderror" />
                    </div>
                    @error('kolejnosc_dostawy')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Customer Details -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Dane Klienta</h3>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nazwa klienta <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="klient_nazwa" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('klient_nazwa') border-red-500 @enderror" />
                    </div>
                    @error('klient_nazwa')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefon klienta</label>
                        <flux:input type="text" wire:model="klient_telefon"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('klient_telefon') border-red-500 @enderror" />
                    </div>
                    @error('klient_telefon')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email klienta</label>
                        <flux:input type="email" wire:model="klient_email"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('klient_email') border-red-500 @enderror" />
                    </div>
                    @error('klient_email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Osoba kontaktowa</label>
                        <flux:input type="text" wire:model="osoba_kontaktowa"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('osoba_kontaktowa') border-red-500 @enderror" />
                    </div>
                    @error('osoba_kontaktowa')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefon kontaktowy</label>
                        <flux:input type="text" wire:model="telefon_kontaktowy"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('telefon_kontaktowy') border-red-500 @enderror" />
                    </div>
                    @error('telefon_kontaktowy')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Address -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Adres Dostawy</h3>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adres <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.live="klient_adres"
                               required
                               placeholder="Ulica, numer domu/mieszkania"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('klient_adres') border-red-500 @enderror" />
                    </div>
                    @error('klient_adres')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror

                    @if($showLocationSearch && !empty($locationSearchResults))
                        <div class="mt-2 bg-white border rounded-md shadow-sm max-h-40 overflow-y-auto">
                            @foreach($locationSearchResults as $result)
                                <div wire:click="selectLocation({{ $result['lat'] }}, {{ $result['lon'] }}, '{{ $result['display_name'] }}')"
                                     class="px-3 py-2 hover:bg-gray-100 cursor-pointer border-b last:border-b-0">
                                    <div class="text-sm">{{ $result['display_name'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kod pocztowy</label>
                        <flux:input type="text" wire:model="kod_pocztowy"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('kod_pocztowy') border-red-500 @enderror" />
                    </div>
                    @error('kod_pocztowy')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Miasto</label>
                        <flux:input type="text" wire:model="miasto"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('miasto') border-red-500 @enderror" />
                    </div>
                    @error('miasto')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Uwagi do dostawy</label>
                    <textarea wire:model="uwagi_dostawy" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('uwagi_dostawy') border-red-500 @enderror"></textarea>
                </div>
                @error('uwagi_dostawy')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Items Selection -->
        @if($selectedProductionOrder)
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Pozycje do Dostawy</h3>

            <div class="space-y-3">
                @foreach($selectedProductionOrder->items as $item)
                <div class="bg-white rounded-lg border p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox"
                                   wire:click="toggleItemSelection({{ $item->id }})"
                                   @checked(isset($selectedItems[$item->id]))
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">

                            <div>
                                <div class="font-medium text-gray-900">{{ $item->product->nazwa }}</div>
                                <div class="text-sm text-gray-500">
                                    Wyprodukowano: {{ $item->ilosc_wyprodukowana }} {{ $item->jednostka }}
                                </div>
                            </div>
                        </div>

                        @if(isset($selectedItems[$item->id]))
                        <div class="flex items-center space-x-2">
                            <label class="text-sm text-gray-600">Ilość do dostawy:</label>
                            <input type="number"
                                   wire:model="selectedItems.{{ $item->id }}.ilosc"
                                   min="1"
                                   max="{{ $item->ilosc_wyprodukowana }}"
                                   class="w-20 text-center border border-gray-300 rounded-md px-2 py-1">
                            <span class="text-sm text-gray-500">{{ $item->jednostka }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            @error('selectedItems')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        @endif

        <!-- Custom Items -->
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Dodatkowe Pozycje</h3>
                <flux:button type="button" wire:click="addCustomItem" variant="outline" size="sm">
                    ➕ Dodaj pozycję
                </flux:button>
            </div>

            @if(!empty($customItems))
            <div class="space-y-3">
                @foreach($customItems as $index => $customItem)
                <div class="bg-white rounded-lg border p-4">
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 items-end">
                        <div>
                            <flux:input wire:model="customItems.{{ $index }}.nazwa_produktu"
                                      label="Nazwa produktu"
                                      required />
                        </div>

                        <div>
                            <flux:input wire:model="customItems.{{ $index }}.ilosc"
                                      type="number"
                                      label="Ilość"
                                      min="1"
                                      required />
                        </div>

                        <div>
                            <flux:input wire:model="customItems.{{ $index }}.jednostka"
                                      label="Jednostka"
                                      required />
                        </div>

                        <div>
                            <flux:input wire:model="customItems.{{ $index }}.waga_kg"
                                      type="number"
                                      step="0.001"
                                      label="Waga (kg)" />
                        </div>

                        <div>
                            <flux:button type="button"
                                       wire:click="removeCustomItem({{ $index }})"
                                       variant="ghost"
                                       size="sm">
                                🗑️
                            </flux:button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 pt-6 border-t">
            <flux:button type="button" wire:click="cancel" variant="ghost">
                Anuluj
            </flux:button>
            <flux:button type="submit" variant="primary">
                Utwórz Dostawę
            </flux:button>
        </div>
    </form>
</div>

@script
<script>
    $wire.on('delivery-created', (data) => {
        // Close modal and show success message
        $wire.dispatch('hideCreateForm');
        // Show notification
        console.log(data.message);
    });

    $wire.on('delivery-creation-failed', (data) => {
        // Show error notification
        console.error(data.message);
    });

    $wire.on('delivery-creation-cancelled', () => {
        $wire.dispatch('hideCreateForm');
    });
</script>
@endscript
