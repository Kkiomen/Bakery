<div class="space-y-6">
    <!-- Delivery Header -->
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $delivery->numer_dostawy }}</h2>
                <p class="text-sm text-gray-600">{{ $delivery->klient_nazwa }}</p>
            </div>

            <div class="flex items-center space-x-2">
                <flux:badge :color="$delivery->status_color">
                    {{ $delivery->status_label }}
                </flux:badge>
                @if($delivery->priorytet !== 'normalny')
                    <flux:badge :color="$delivery->priorytet_color" size="sm">
                        {{ $delivery->priorytet_label }}
                    </flux:badge>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-2">
            @if($delivery->canBeStarted())
                <flux:button wire:click="startDelivery" variant="primary" >
                    Rozpocznij dostawę
                </flux:button>
            @endif

            @if($delivery->status === 'w_drodze')
                <flux:button wire:click="openPhotoModal('produkty')" variant="outline" >
                    Dodaj zdjęcia produktów
                </flux:button>

                @if(!$delivery->signature()->exists())
                    <flux:button wire:click="openSignatureModal" variant="outline" >
                        Podpis odbiorcy
                    </flux:button>
                @endif
            @endif

            @if($delivery->canBeCompleted())
                <flux:button wire:click="completeDelivery"
                           variant="primary"
                           wire:confirm="Czy na pewno chcesz zakończyć tę dostawę?">
                    Zakończ dostawę
                </flux:button>
            @endif

            <a href="{{ $delivery->google_maps_url }}"
               target="_blank"
               class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100">
                🗺️
                Nawigacja
            </a>

            @if($delivery->telefon_kontaktowy || $delivery->klient_telefon)
                <a href="tel:{{ $delivery->telefon_kontaktowy ?: $delivery->klient_telefon }}"
                   class="inline-flex items-center px-3 py-2 text-sm font-medium text-green-600 bg-green-50 border border-green-200 rounded-md hover:bg-green-100">
                    📞
                    Zadzwoń
                </a>
            @endif
        </div>
    </div>

    <!-- Address & Contact Info -->
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Informacje o dostawie</h3>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Map -->
            <div class="lg:col-span-1">
                <h4 class="font-medium text-gray-900 mb-2">Lokalizacja</h4>
                <div class="w-full h-48 bg-gray-200 rounded-lg border border-gray-300 flex items-center justify-center">
                    <div class="text-center text-gray-500">
                        <div class="text-2xl mb-2">🗺️</div>
                        <div class="text-sm">Mapa zostanie wczytana</div>
                        <div class="text-xs mt-1">{{ $delivery->full_address }}</div>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Adres dostawy</h4>
                <div class="text-sm text-gray-600 space-y-1">
                    <div class="flex items-start space-x-2">
                        📍
                        <div>
                            <div>{{ $delivery->klient_adres }}</div>
                            @if($delivery->kod_pocztowy || $delivery->miasto)
                                <div>{{ $delivery->kod_pocztowy }} {{ $delivery->miasto }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($delivery->uwagi_dostawy)
                    <div class="mt-3">
                        <h5 class="font-medium text-gray-900 text-sm mb-1">Uwagi do dostawy:</h5>
                        <p class="text-sm text-gray-600 bg-yellow-50 p-2 rounded">{{ $delivery->uwagi_dostawy }}</p>
                    </div>
                @endif
            </div>

            <!-- Contact -->
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Kontakt</h4>
                <div class="text-sm text-gray-600 space-y-2">
                    @if($delivery->osoba_kontaktowa)
                        <div class="flex items-center space-x-2">
                            👤
                            <span>{{ $delivery->osoba_kontaktowa }}</span>
                        </div>
                    @endif

                    @if($delivery->telefon_kontaktowy)
                        <div class="flex items-center space-x-2">
                            📞
                            <a href="tel:{{ $delivery->telefon_kontaktowy }}"
                               class="text-blue-600 hover:text-blue-800">
                                {{ $delivery->telefon_kontaktowy }}
                            </a>
                        </div>
                    @endif

                    @if($delivery->klient_email)
                        <div class="flex items-center space-x-2">
                            ✉️
                            <a href="mailto:{{ $delivery->klient_email }}"
                               class="text-blue-600 hover:text-blue-800">
                                {{ $delivery->klient_email }}
                            </a>
                        </div>
                    @endif

                    @if($delivery->godzina_planowana)
                        <div class="flex items-center space-x-2">
                            ⏰
                            <span>Planowana: {{ $delivery->godzina_planowana->format('H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Items to Deliver -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">
                Produkty do dostawy ({{ $delivery->items->count() }})
            </h3>
            <div class="text-sm text-gray-500">
                Łączna waga: {{ number_format($this->getTotalWeight(), 2) }} kg
            </div>
        </div>

        <div class="space-y-3">
            @foreach($delivery->items as $item)
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                          {{ $item->status === 'dostarczony' ? 'bg-green-100' :
                                             ($item->status === 'brakuje' ? 'bg-red-100' :
                                              ($item->status === 'uszkodzony' ? 'bg-orange-100' : 'bg-gray-100')) }}">
                                    @if($item->status === 'dostarczony')
                                        ✅
                                    @elseif($item->status === 'brakuje')
                                        ❌
                                    @elseif($item->status === 'uszkodzony')
                                        ⚠️
                                    @else
                                        📦
                                    @endif
                                </div>
                            </div>

                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ $item->nazwa_produktu }}</div>
                                <div class="text-sm text-gray-500">
                                    Do dostawy: {{ $item->ilosc }} {{ $item->jednostka }}
                                    @if($item->waga_kg)
                                        • {{ number_format($item->waga_kg, 2) }} kg
                                    @endif
                                </div>

                                @if($item->ilosc_dostarczona > 0 && $item->ilosc_dostarczona < $item->ilosc)
                                    <div class="text-sm text-orange-600">
                                        Dostarczone częściowo: {{ $item->ilosc_dostarczona }} {{ $item->jednostka }}
                                    </div>
                                @endif
                            </div>

                            <flux:badge :color="$item->status_color" size="sm">
                                {{ $item->status_label }}
                            </flux:badge>
                        </div>

                        @if($item->uwagi)
                            <div class="mt-2 text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                {{ $item->uwagi }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Item Actions -->
                @if($delivery->status === 'w_drodze' && $item->status !== 'dostarczony')
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <label class="text-sm text-gray-600">Dostarczona ilość:</label>
                            <input type="number"
                                   wire:model.live="itemQuantityDelivered.{{ $item->id }}"
                                   wire:change="updateItemQuantity({{ $item->id }})"
                                   min="0"
                                   max="{{ $item->ilosc }}"
                                   class="w-20 text-center border-gray-300 rounded-md">
                            <span class="text-sm text-gray-500">{{ $item->jednostka }}</span>
                        </div>

                        <div class="flex space-x-2">
                            @if($item->status !== 'brakuje')
                                <flux:button wire:click="markItemAsMissing({{ $item->id }})"
                                           variant="ghost"
                                           size="sm"
                                           wire:confirm="Oznaczyć jako brakującą pozycję?">
                                    Brakuje
                                </flux:button>
                            @endif

                            @if($item->status !== 'uszkodzony')
                                <flux:button wire:click="markItemAsDamaged({{ $item->id }})"
                                           variant="ghost"
                                           size="sm"
                                           wire:confirm="Oznaczyć jako uszkodzoną pozycję?">
                                    Uszkodzony
                                </flux:button>
                            @endif

                            <flux:button wire:click="viewItemDetails({{ $item->id }})"
                                       variant="ghost"
                                       size="sm">
                                Szczegóły
                            </flux:button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Progress Bar -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Postęp dostawy</span>
                <span class="text-sm text-gray-500">{{ $this->getDeliveryProgress() }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full"
                     style="width: {{ $this->getDeliveryProgress() }}%"></div>
            </div>
        </div>
    </div>

    <!-- Photos Section -->
    @if($delivery->photos->count() > 0 || $delivery->status === 'w_drodze')
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Zdjęcia dostawy</h3>
            @if($delivery->status === 'w_drodze')
                <flux:button wire:click="openPhotoModal('produkty')" variant="outline" size="sm" >
                    Dodaj zdjęcia
                </flux:button>
            @endif
        </div>

        @if($delivery->photos->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($delivery->photos as $photo)
                <div class="relative group">
                    <img src="{{ $photo->full_url }}"
                         alt="{{ $photo->opis }}"
                         class="w-full h-32 object-cover rounded-lg">

                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity rounded-lg flex items-center justify-center">
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                            <flux:button wire:click="deletePhoto({{ $photo->id }})"
                                       variant="ghost"
                                       size="sm"
                                       wire:confirm="Czy na pewno chcesz usunąć to zdjęcie?">
                            </flux:button>
                        </div>
                    </div>

                    <div class="absolute top-2 left-2">
                        <flux:badge :color="match($photo->typ_zdjecia) {
                            'produkty' => 'blue',
                            'dowod_dostawy' => 'green',
                            'problem' => 'red',
                            'lokalizacja' => 'purple',
                            default => 'gray'
                        }" size="sm">
                            {{ $photo->typ_zdjecia_label }}
                        </flux:badge>
                    </div>

                    @if($photo->opis)
                        <div class="absolute bottom-2 left-2 right-2">
                            <div class="bg-black bg-opacity-75 text-white text-xs p-1 rounded truncate">
                                {{ $photo->opis }}
                            </div>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-6xl mb-4">📸</div>
                <p class="text-gray-500">Brak zdjęć. Dodaj zdjęcia produktów przed zakończeniem dostawy.</p>
            </div>
        @endif
    </div>
    @endif

    <!-- Signature Section -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Podpis odbiorcy</h3>
            @if($delivery->status === 'w_drodze' && !$delivery->signature()->exists())
                <flux:button wire:click="openSignatureModal" variant="outline" size="sm" >
                    Dodaj podpis
                </flux:button>
            @endif
        </div>

        @if($delivery->signature()->exists())
            @php $signature = $delivery->signature()->first() @endphp
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <img src="{{ $signature->signature_image }}"
                             alt="Podpis odbiorcy"
                             class="w-full h-32 object-contain border border-gray-200 rounded bg-white">
                    </div>

                    <div class="space-y-2 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Podpisał:</span>
                            <span class="text-gray-900">{{ $signature->signer_name }}</span>
                        </div>

                        @if($signature->signer_position)
                            <div>
                                <span class="font-medium text-gray-700">Stanowisko:</span>
                                <span class="text-gray-900">{{ $signature->signer_position }}</span>
                            </div>
                        @endif

                        <div>
                            <span class="font-medium text-gray-700">Data i czas:</span>
                            <span class="text-gray-900">{{ $signature->signature_date->format('d.m.Y H:i') }}</span>
                        </div>

                        @if($signature->uwagi)
                            <div>
                                <span class="font-medium text-gray-700">Uwagi:</span>
                                <span class="text-gray-900">{{ $signature->uwagi }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                <div class="text-6xl mb-4">✍️</div>
                <p class="text-gray-500 mb-2">Brak podpisu odbiorcy</p>
                <p class="text-sm text-gray-400">Podpis jest wymagany do zakończenia dostawy</p>
            </div>
        @endif
    </div>

    <!-- Delivery Timeline -->
    @if($delivery->godzina_rozpoczecia || $delivery->godzina_zakonczenia)
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Historia dostawy</h3>

        <div class="flow-root">
            <ul class="-mb-8">
                <li>
                    <div class="relative pb-8">
                        <div class="relative flex space-x-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500">
                                ▶️
                            </div>
                            <div class="min-w-0 flex-1">
                                <div>
                                    <div class="text-sm text-gray-500">
                                        <span class="font-medium text-gray-900">Rozpoczęcie dostawy</span>
                                    </div>
                                    @if($delivery->godzina_rozpoczecia)
                                        <div class="mt-1 text-sm text-gray-700">
                                            {{ $delivery->godzina_rozpoczecia->format('d.m.Y H:i') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                @if($delivery->godzina_zakonczenia)
                <li>
                    <div class="relative">
                        <div class="relative flex space-x-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-500">
                                ✅
                            </div>
                            <div class="min-w-0 flex-1">
                                <div>
                                    <div class="text-sm text-gray-500">
                                        <span class="font-medium text-gray-900">Zakończenie dostawy</span>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-700">
                                        {{ $delivery->godzina_zakonczenia->format('d.m.Y H:i') }}
                                        @if($delivery->getDeliveryDuration())
                                            <span class="text-gray-500">
                                                ({{ $delivery->getDeliveryDuration() }} min)
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @endif
            </ul>
        </div>
    </div>
    @endif

    <!-- Photo Upload Modal -->
    @if($showPhotoModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-lg w-full">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Dodaj zdjęcia - {{ $delivery->numer_dostawy }}</h3>
                    <button wire:click="closeModal"
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Typ zdjęcia</label>
                            <select wire:model="photoType"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="produkty">Zdjęcia produktów</option>
                                <option value="dowod_dostawy">Dowód dostawy</option>
                                <option value="problem">Problem/uszkodzenie</option>
                                <option value="lokalizacja">Lokalizacja</option>
                                <option value="inne">Inne</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Wybierz zdjęcia (max 10)
                            </label>
                            <input type="file"
                                   wire:model="newPhotos"
                                   multiple
                                   accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('newPhotos')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            @error('newPhotos.*')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Opis zdjęć (opcjonalnie)</label>
                            <textarea wire:model="photoDescription"
                                     rows="3"
                                     class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button wire:click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Anuluj
                    </button>
                    <button wire:click="uploadPhotos"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                        Prześlij zdjęcia
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Signature Modal -->
    @if($showSignatureModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Podpis odbiorcy - {{ $delivery->numer_dostawy }}</h3>
                    <button wire:click="closeModal"
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Imię i nazwisko osoby odbierającej <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="signerName"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stanowisko (opcjonalnie)</label>
                                <input type="text" wire:model="signerPosition"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Podpis *
                            </label>
                            <div class="border-2 border-gray-300 rounded-lg p-4 bg-white">
                                <canvas id="signature-pad-details"
                                        class="w-full h-48 border border-gray-200 rounded cursor-crosshair"
                                        style="touch-action: none;">
                                </canvas>
                                <div class="flex justify-between mt-2">
                                    <button type="button" onclick="clearSignatureDetails()"
                                            class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">
                                        Wyczyść
                                    </button>
                                    <span class="text-xs text-gray-500">Podpisz palcem lub myszą</span>
                                </div>
                            </div>
                            @error('signatureData')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Uwagi (opcjonalnie)</label>
                            <textarea wire:model="signatureNotes"
                                     rows="2"
                                     class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button wire:click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Anuluj
                    </button>
                    <button wire:click="saveSignature"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                        Zapisz podpis
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Item Details Modal -->
    @if($showItemDetails && $selectedItem)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-lg w-full">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Szczegóły pozycji: {{ $selectedItem->nazwa_produktu }}</h3>
                    <button wire:click="closeModal"
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Ilość zamówiona:</span>
                                <span class="text-gray-900">{{ $selectedItem->ilosc }} {{ $selectedItem->jednostka }}</span>
                            </div>

                            <div>
                                <span class="font-medium text-gray-700">Ilość dostarczona:</span>
                                <span class="text-gray-900">{{ $selectedItem->ilosc_dostarczona }} {{ $selectedItem->jednostka }}</span>
                            </div>

                            @if($selectedItem->waga_kg)
                                <div>
                                    <span class="font-medium text-gray-700">Waga:</span>
                                    <span class="text-gray-900">{{ number_format($selectedItem->waga_kg, 2) }} kg</span>
                                </div>
                            @endif

                            <div>
                                <span class="font-medium text-gray-700">Status:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($selectedItem->status === 'dostarczony') bg-green-100 text-green-800
                                    @elseif($selectedItem->status === 'brakuje') bg-red-100 text-red-800
                                    @elseif($selectedItem->status === 'uszkodzony') bg-orange-100 text-orange-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $selectedItem->status_label }}
                                </span>
                            </div>
                        </div>

                        @if($selectedItem->product)
                            <div class="border-t pt-4">
                                <h4 class="font-medium text-gray-900 mb-2">Informacje o produkcie</h4>
                                <div class="text-sm text-gray-600 space-y-1">
                                    <div><strong>SKU:</strong> {{ $selectedItem->product->sku }}</div>
                                    @if($selectedItem->product->ean)
                                        <div><strong>EAN:</strong> {{ $selectedItem->product->ean }}</div>
                                    @endif
                                    <div><strong>Kategoria:</strong> {{ $selectedItem->product->category->nazwa ?? 'Brak' }}</div>
                                    @if($selectedItem->product->waga_g)
                                        <div><strong>Waga jednostkowa:</strong> {{ $selectedItem->product->waga_g }}g</div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($selectedItem->uwagi)
                            <div class="border-t pt-4">
                                <h4 class="font-medium text-gray-900 mb-2">Uwagi</h4>
                                <p class="text-sm text-gray-600">{{ $selectedItem->uwagi }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                    <button wire:click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Zamknij
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
    let signaturePadDetails;

    // Initialize signature pad when modal opens
    $wire.on('showSignatureModal', () => {
        setTimeout(() => {
            const canvas = document.getElementById('signature-pad-details');
            if (canvas) {
                signaturePadDetails = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)'
                });

                signaturePadDetails.addEventListener('endStroke', () => {
                    $wire.updateSignatureData(signaturePadDetails.toDataURL());
                });

                // Resize canvas
                const resizeCanvas = () => {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext('2d').scale(ratio, ratio);
                    signaturePadDetails.clear();
                };

                window.addEventListener('resize', resizeCanvas);
                resizeCanvas();
            }
        }, 100);
    });

    // Clear signature function
    window.clearSignatureDetails = () => {
        if (signaturePadDetails) {
            signaturePadDetails.clear();
            $wire.updateSignatureData('');
        }
    };

    // Event listeners
    $wire.on('delivery-started', (data) => {
        console.log(data.message);
    });

    $wire.on('delivery-completed', (data) => {
        console.log(data.message);
    });

    $wire.on('photos-uploaded', (data) => {
        console.log(data.message);
    });

    $wire.on('signature-saved', (data) => {
        console.log(data.message);
    });
</script>
@endscript
