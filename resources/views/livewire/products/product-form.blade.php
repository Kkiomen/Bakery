<div x-data="productForm()" class="max-w-4xl mx-auto space-y-6">
    {{-- Nagłówek --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ $isEditing ? 'Edytuj produkt' : 'Dodaj nowy produkt' }}
            </h1>
            <p class="text-gray-600">
                {{ $isEditing ? 'Zaktualizuj informacje o produkcie' : 'Wprowadź dane nowego produktu' }}
            </p>
        </div>
        <div class="flex space-x-3">
            <button wire:click="cancel"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Anuluj
            </button>
            <button wire:click="save"
                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                {{ $isEditing ? 'Zaktualizuj' : 'Zapisz' }}
            </button>
        </div>
    </div>

    <form wire:submit="save" class="space-y-8">
        {{-- Dane główne --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Dane główne</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- SKU --}}
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">
                        SKU <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="sku"
                           wire:model.blur="sku"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('sku') border-red-500 @enderror">
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- EAN --}}
                <div>
                    <label for="ean" class="block text-sm font-medium text-gray-700 mb-1">EAN</label>
                    <input type="text"
                           id="ean"
                           wire:model.blur="ean"
                           placeholder="8-14 znaków"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('ean') border-red-500 @enderror">
                    @error('ean')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nazwa --}}
                <div class="md:col-span-2">
                    <label for="nazwa" class="block text-sm font-medium text-gray-700 mb-1">
                        Nazwa produktu <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="nazwa"
                           wire:model.blur="nazwa"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('nazwa') border-red-500 @enderror">
                    @error('nazwa')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kategoria --}}
                <div>
                    <label for="kategoria_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Kategoria <span class="text-red-500">*</span>
                    </label>
                    <select id="kategoria_id"
                            wire:model.blur="kategoria_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('kategoria_id') border-red-500 @enderror">
                        <option value="">Wybierz kategorię</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->nazwa }}</option>
                        @endforeach
                    </select>
                    @error('kategoria_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="flex items-center">
                    <input type="checkbox"
                           id="aktywny"
                           wire:model="aktywny"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <label for="aktywny" class="ml-2 block text-sm text-gray-900">Produkt aktywny</label>
                </div>

                {{-- Opis --}}
                <div class="md:col-span-2">
                    <label for="opis" class="block text-sm font-medium text-gray-700 mb-1">Opis</label>
                    <textarea id="opis"
                              wire:model.blur="opis"
                              rows="4"
                              placeholder="Opis produktu (obsługuje Markdown)"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('opis') border-red-500 @enderror"></textarea>
                    @error('opis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Zdjęcia produktu --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Zdjęcia produktu</h2>


            {{-- Upload nowych zdjęć --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Dodaj nowe zdjęcia
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                    <input type="file"
                           wire:model="photos"
                           multiple
                           accept="image/*"
                           class="hidden"
                           id="photo-upload">
                    <label for="photo-upload" class="cursor-pointer">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div class="mt-4">
                            <p class="text-sm text-gray-600">
                                <span class="font-medium text-blue-600 hover:text-blue-500">Kliknij aby wybrać pliki</span>
                                lub przeciągnij i upuść
                            </p>
                            <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG do 5MB</p>
                        </div>
                    </label>
                </div>
                @error('photos.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Podgląd nowych zdjęć --}}
            @if(!empty($photos))
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Nowe zdjęcia do dodania:</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($photos as $index => $photo)
                            <div class="relative group">
                                <img src="{{ $photo->temporaryUrl() }}"
                                     alt="Podgląd zdjęcia"
                                     class="w-full h-32 object-cover rounded-lg border">
                                <button type="button"
                                        wire:click="removePhoto({{ $index }})"
                                        class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Istniejące zdjęcia --}}
            @if($existingImages && $existingImages->count() > 0)
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Obecne zdjęcia:</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($existingImages as $image)
                            <div class="relative group">
                                <img src="{{ $image->url }}"
                                     alt="{{ $image->alt_text }}"
                                     class="w-full h-32 object-cover rounded-lg border {{ in_array($image->id, $imagesToDelete) ? 'opacity-50' : '' }}">

                                {{-- Badge dla zdjęcia głównego --}}
                                @if($image->is_primary)
                                    <span class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                                        Główne
                                    </span>
                                @endif

                                {{-- Akcje --}}
                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center space-x-2">
                                    @if(!$image->is_primary && !in_array($image->id, $imagesToDelete))
                                        <button type="button"
                                                wire:click="setPrimaryImage({{ $image->id }})"
                                                class="bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700"
                                                title="Ustaw jako główne">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                            </svg>
                                        </button>
                                    @endif

                                    @if(in_array($image->id, $imagesToDelete))
                                        <button type="button"
                                                wire:click="unmarkImageForDeletion({{ $image->id }})"
                                                class="bg-green-600 text-white p-2 rounded-full hover:bg-green-700"
                                                title="Anuluj usunięcie">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                    @else
                                        <button type="button"
                                                wire:click="markImageForDeletion({{ $image->id }})"
                                                class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700"
                                                title="Oznacz do usunięcia">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>

                                {{-- Overlay dla oznaczonych do usunięcia --}}
                                @if(in_array($image->id, $imagesToDelete))
                                    <div class="absolute inset-0 bg-red-600 bg-opacity-75 rounded-lg flex items-center justify-center">
                                        <span class="text-white font-medium text-sm">Do usunięcia</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Ceny B2B i rabaty ilościowe --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Ceny B2B i rabaty ilościowe</h2>

            {{-- Dodaj nowy cennik B2B --}}
            @if($isEditing && $product && $product->id)
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h3 class="text-md font-medium text-blue-900 mb-3">Dodaj nowy cennik</h3>
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Poziom cenowy</label>
                            <select wire:model="newB2BPricing.pricing_tier" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="standard">Standard</option>
                                <option value="bronze">Bronze</option>
                                <option value="silver">Silver</option>
                                <option value="gold">Gold</option>
                                <option value="platinum">Platinum</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Min. ilość</label>
                            <input type="number" wire:model="newB2BPricing.min_quantity" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Max. ilość</label>
                            <input type="number" wire:model="newB2BPricing.max_quantity" placeholder="Brak limitu" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cena netto (zł)</label>
                            <input type="number" wire:model="newB2BPricing.price_net" step="0.01" min="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rabat (%)</label>
                            <input type="number" wire:model="newB2BPricing.discount_percent" step="0.01" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="flex items-end">
                            <button type="button" wire:click="addB2BPricing" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                ➕ Dodaj
                            </button>
                        </div>
                    </div>

                    @error('newB2BPricing.pricing_tier') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    @error('newB2BPricing.min_quantity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    @error('newB2BPricing.price_net') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            @endif

            {{-- Lista istniejących cenników --}}
            @if(!empty($b2bPricings))
                <div>
                    <h3 class="text-md font-medium text-gray-900 mb-3">Obecne cenniki B2B</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Poziom</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ilość</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cena netto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cena brutto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rabat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($b2bPricings as $index => $pricing)
                                    <tr class="{{ in_array($pricing['id'], $pricingsToDelete) ? 'opacity-50 bg-red-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $pricing['pricing_tier'] === 'platinum' ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ $pricing['pricing_tier'] === 'gold' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $pricing['pricing_tier'] === 'silver' ? 'bg-gray-100 text-gray-800' : '' }}
                                                {{ $pricing['pricing_tier'] === 'bronze' ? 'bg-orange-100 text-orange-800' : '' }}
                                                {{ $pricing['pricing_tier'] === 'standard' ? 'bg-blue-100 text-blue-800' : '' }}">
                                                {{ ucfirst($pricing['pricing_tier']) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $pricing['min_quantity'] }}{{ $pricing['max_quantity'] ? ' - ' . $pricing['max_quantity'] : '+' }} szt
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($pricing['price_net'], 2) }} zł
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($pricing['price_gross'], 2) }} zł
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $pricing['discount_percent'] }}%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox"
                                                       wire:change="updateB2BPricingStatus({{ $index }}, $event.target.checked)"
                                                       {{ $pricing['is_active'] ? 'checked' : '' }}
                                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                <span class="ml-2 text-sm text-gray-600">Aktywny</span>
                                            </label>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if(in_array($pricing['id'], $pricingsToDelete))
                                                <button type="button"
                                                        wire:click="restoreB2BPricing({{ $pricing['id'] }})"
                                                        class="text-green-600 hover:text-green-900">
                                                    ↶ Przywróć
                                                </button>
                                            @else
                                                <button type="button"
                                                        wire:click="removeB2BPricing({{ $pricing['id'] }})"
                                                        class="text-red-600 hover:text-red-900">
                                                    🗑️ Usuń
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @elseif($isEditing)
                <div class="text-center py-8 text-gray-500">
                    <p>Brak cenników B2B dla tego produktu.</p>
                    <p class="text-sm">Użyj formularza powyżej aby dodać pierwszy cennik.</p>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p>Zapisz produkt aby móc dodać cenniki B2B.</p>
                </div>
            @endif
        </div>

        {{-- Waga i jednostki --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Waga i jednostki</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Waga --}}
                <div>
                    <label for="waga_g" class="block text-sm font-medium text-gray-700 mb-1">
                        Waga (g) <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           id="waga_g"
                           wire:model.blur="waga_g"
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('waga_g') border-red-500 @enderror">
                    @if($wagaKgDisplay)
                        <p class="mt-1 text-sm text-gray-500">{{ $wagaKgDisplay }}</p>
                    @endif
                    @error('waga_g')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jednostka sprzedaży --}}
                <div>
                    <label for="jednostka_sprzedazy" class="block text-sm font-medium text-gray-700 mb-1">
                        Jednostka sprzedaży <span class="text-red-500">*</span>
                    </label>
                    <select id="jednostka_sprzedazy"
                            wire:model.blur="jednostka_sprzedazy"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('jednostka_sprzedazy') border-red-500 @enderror">
                        <option value="szt">Sztuka</option>
                        <option value="opak">Opakowanie</option>
                        <option value="kg">Kilogram</option>
                    </select>
                    @error('jednostka_sprzedazy')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Zawartość opakowania --}}
                <div>
                    <label for="zawartosc_opakowania" class="block text-sm font-medium text-gray-700 mb-1">
                        Zawartość opakowania
                    </label>
                    <input type="number"
                           id="zawartosc_opakowania"
                           wire:model.blur="zawartosc_opakowania"
                           min="1"
                           placeholder="np. liczba sztuk"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('zawartosc_opakowania') border-red-500 @enderror">
                    @error('zawartosc_opakowania')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Ceny i VAT --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Ceny i VAT</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Cena netto --}}
                <div>
                    <label for="cena_netto_gr" class="block text-sm font-medium text-gray-700 mb-1">
                        Cena netto (gr) <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           id="cena_netto_gr"
                           wire:model.blur="cena_netto_gr"
                           min="1"
                           placeholder="Cena w groszach"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('cena_netto_gr') border-red-500 @enderror">
                    @if($cenaNettoDisplay)
                        <p class="mt-1 text-sm text-gray-500">{{ $cenaNettoDisplay }}</p>
                    @endif
                    @error('cena_netto_gr')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Stawka VAT --}}
                <div>
                    <label for="stawka_vat" class="block text-sm font-medium text-gray-700 mb-1">
                        Stawka VAT <span class="text-red-500">*</span>
                    </label>
                    <select id="stawka_vat"
                            wire:model.blur="stawka_vat"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('stawka_vat') border-red-500 @enderror">
                        <option value="0">0%</option>
                        <option value="5">5%</option>
                        <option value="8">8%</option>
                        <option value="23">23%</option>
                    </select>
                    @error('stawka_vat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Alergeny --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Alergeny</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($availableAllergens as $key => $label)
                    <div class="flex items-center">
                        <input type="checkbox"
                               id="allergen_{{ $key }}"
                               wire:click="toggleAllergen('{{ $key }}')"
                               @if(in_array($key, $alergeny)) checked @endif
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <label for="allergen_{{ $key }}" class="ml-2 block text-sm text-gray-900">{{ $label }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Wartości odżywcze --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Wartości odżywcze (na 100g)</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                {{-- Kalorie --}}
                <div>
                    <label for="kcal" class="block text-sm font-medium text-gray-700 mb-1">Kalorie (kcal)</label>
                    <input type="number"
                           id="kcal"
                           wire:model.blur="kcal"
                           step="0.1"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('kcal') border-red-500 @enderror">
                    @error('kcal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Białko --}}
                <div>
                    <label for="bialko_g" class="block text-sm font-medium text-gray-700 mb-1">Białko (g)</label>
                    <input type="number"
                           id="bialko_g"
                           wire:model.blur="bialko_g"
                           step="0.1"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('bialko_g') border-red-500 @enderror">
                    @error('bialko_g')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tłuszcz --}}
                <div>
                    <label for="tluszcz_g" class="block text-sm font-medium text-gray-700 mb-1">Tłuszcz (g)</label>
                    <input type="number"
                           id="tluszcz_g"
                           wire:model.blur="tluszcz_g"
                           step="0.1"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('tluszcz_g') border-red-500 @enderror">
                    @error('tluszcz_g')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Węglowodany --}}
                <div>
                    <label for="wegle_g" class="block text-sm font-medium text-gray-700 mb-1">Węglowodany (g)</label>
                    <input type="number"
                           id="wegle_g"
                           wire:model.blur="wegle_g"
                           step="0.1"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('wegle_g') border-red-500 @enderror">
                    @error('wegle_g')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- SEO --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Metadane SEO</h2>
            <div class="space-y-6">
                {{-- Meta title --}}
                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Tytuł SEO</label>
                    <input type="text"
                           id="meta_title"
                           wire:model.blur="meta_title"
                           maxlength="255"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('meta_title') border-red-500 @enderror">
                    @error('meta_title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Meta description --}}
                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Opis SEO</label>
                    <textarea id="meta_description"
                              wire:model.blur="meta_description"
                              rows="3"
                              maxlength="500"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('meta_description') border-red-500 @enderror"></textarea>
                    @error('meta_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </form>

    {{-- Alpine.js script --}}
    <script>
        function productForm() {
            return {
                init() {
                    // Inicjalizacja
                }
            }
        }

        document.addEventListener('livewire:init', () => {
            Livewire.on('product-saved', (event) => {
                // Możesz dodać toast notification tutaj
                console.log(event.message);
                // Przekierowanie lub inne akcje
            });

            Livewire.on('product-error', (message) => {
                // Możesz dodać toast notification tutaj
                console.log(message);
            });

            Livewire.on('product-cancelled', () => {
                // Przekierowanie lub inne akcje
                window.history.back();
            });
        });
    </script>
</div>
