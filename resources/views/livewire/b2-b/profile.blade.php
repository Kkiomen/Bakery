<div class="min-h-screen bg-gray-50">
    <!-- Nagłówek -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">⚙️ Profil Firmy</h1>
                    <p class="text-gray-600">{{ Auth::guard('b2b')->user()->company_name }}</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('b2b.dashboard') }}"
                       class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        ← Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Tabs Navigation -->
        <div class="mb-8">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button wire:click="setActiveTab('company')"
                            class="py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap
                                {{ $activeTab === 'company' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        🏢 Dane firmy
                    </button>
                    <button wire:click="setActiveTab('contact')"
                            class="py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap
                                {{ $activeTab === 'contact' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        📞 Kontakt
                    </button>
                    <button wire:click="setActiveTab('delivery')"
                            class="py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap
                                {{ $activeTab === 'delivery' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        🚚 Preferencje dostaw
                    </button>
                    <button wire:click="setActiveTab('notifications')"
                            class="py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap
                                {{ $activeTab === 'notifications' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        🔔 Powiadomienia
                    </button>
                    <button wire:click="setActiveTab('security')"
                            class="py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap
                                {{ $activeTab === 'security' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        🔐 Bezpieczeństwo
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Content -->
        @if($activeTab === 'company')
            <!-- Dane firmy -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">🏢 Dane firmy</h2>
                </div>
                <div class="p-6">
                    <form wire:submit="updateProfile">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nazwa firmy <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="company_name"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('company_name') border-red-500 @enderror">
                                @error('company_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                                <input type="text" wire:model="nip"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('nip') border-red-500 @enderror">
                                @error('nip')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">REGON</label>
                                <input type="text" wire:model="regon"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('regon') border-red-500 @enderror">
                                @error('regon')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Typ działalności <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="business_type"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('business_type') border-red-500 @enderror">
                                    <option value="">Wybierz typ</option>
                                    @foreach($this->getBusinessTypes() as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('business_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Adres <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="address"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror">
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Kod pocztowy <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="postal_code"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('postal_code') border-red-500 @enderror">
                                @error('postal_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Miasto <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="city"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('city') border-red-500 @enderror">
                                @error('city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                                <input type="text" wire:model="phone"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Strona internetowa</label>
                                <input type="url" wire:model="website"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('website') border-red-500 @enderror">
                                @error('website')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Opis działalności</label>
                                <textarea wire:model="business_description"
                                          rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('business_description') border-red-500 @enderror"></textarea>
                                @error('business_description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                    class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                                💾 Zapisz zmiany
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        @elseif($activeTab === 'contact')
            <!-- Kontakt -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">📞 Dane kontaktowe</h2>
                </div>
                <div class="p-6">
                    <form wire:submit="updateProfile">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" wire:model="email"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Osoba kontaktowa</label>
                                <input type="text" wire:model="contact_person"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('contact_person') border-red-500 @enderror">
                                @error('contact_person')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telefon kontaktowy</label>
                                <input type="text" wire:model="contact_phone"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('contact_phone') border-red-500 @enderror">
                                @error('contact_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email kontaktowy</label>
                                <input type="email" wire:model="contact_email"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('contact_email') border-red-500 @enderror">
                                @error('contact_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                    class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                                💾 Zapisz zmiany
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        @elseif($activeTab === 'delivery')
            <!-- Preferencje dostaw -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">🚚 Preferencje dostaw</h2>
                </div>
                <div class="p-6">
                    <form wire:submit="updateProfile">
                        <!-- Preferowany czas dostaw -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Preferowany czas dostaw</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($this->getDeliveryTimes() as $value => $label)
                                    <label class="flex items-center">
                                        <input type="radio" wire:model="preferred_delivery_time" value="{{ $value }}"
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Preferowane dni dostaw -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Preferowane dni dostaw</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($this->getDaysOfWeek() as $value => $label)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="delivery_days" value="{{ $value }}"
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Adresy dostaw -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-3">
                                <label class="block text-sm font-medium text-gray-700">Adresy dostaw</label>
                                <button type="button" wire:click="addDeliveryAddress"
                                        class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                                    ➕ Dodaj adres
                                </button>
                            </div>

                            @if(!empty($delivery_addresses))
                                <div class="space-y-4">
                                    @foreach($delivery_addresses as $index => $address)
                                        <div class="p-4 border border-gray-300 rounded-lg">
                                            <div class="flex justify-between items-center mb-3">
                                                <h4 class="text-sm font-medium text-gray-700">Adres {{ $index + 1 }}</h4>
                                                <button type="button" wire:click="removeDeliveryAddress({{ $index }})"
                                                        class="text-red-600 hover:text-red-800 text-sm">
                                                    🗑️ Usuń
                                                </button>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1">Nazwa</label>
                                                    <input type="text" wire:model="delivery_addresses.{{ $index }}.name"
                                                           placeholder="np. Magazyn główny"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1">Osoba kontaktowa</label>
                                                    <input type="text" wire:model="delivery_addresses.{{ $index }}.contact"
                                                           placeholder="Imię i nazwisko"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-xs text-gray-600 mb-1">Adres</label>
                                                    <input type="text" wire:model="delivery_addresses.{{ $index }}.address"
                                                           placeholder="Ulica, numer, kod pocztowy, miasto"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1">Telefon</label>
                                                    <input type="text" wire:model="delivery_addresses.{{ $index }}.phone"
                                                           placeholder="Numer telefonu"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1">Uwagi</label>
                                                    <input type="text" wire:model="delivery_addresses.{{ $index }}.notes"
                                                           placeholder="Dodatkowe informacje"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500">Brak dodanych adresów dostaw.</p>
                            @endif
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                    class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                                💾 Zapisz zmiany
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        @elseif($activeTab === 'notifications')
            <!-- Powiadomienia -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">🔔 Ustawienia powiadomień</h2>
                </div>
                <div class="p-6">
                    <form wire:submit="updateProfile">
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Powiadomienia email</h3>
                                    <p class="text-sm text-gray-600">Otrzymuj powiadomienia o statusie zamówień na email</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="email_notifications" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Powiadomienia SMS</h3>
                                    <p class="text-sm text-gray-600">Otrzymuj powiadomienia SMS o ważnych zmianach</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="sms_notifications" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                    class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                                💾 Zapisz zmiany
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        @elseif($activeTab === 'security')
            <!-- Bezpieczeństwo -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">🔐 Zmiana hasła</h2>
                </div>
                <div class="p-6">
                    <form wire:submit="changePassword">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Aktualne hasło <span class="text-red-500">*</span>
                                </label>
                                <input type="password" wire:model="current_password"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-500 @enderror">
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nowe hasło <span class="text-red-500">*</span>
                                </label>
                                <input type="password" wire:model="password"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Potwierdź nowe hasło <span class="text-red-500">*</span>
                                </label>
                                <input type="password" wire:model="password_confirmation"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('password_confirmation') border-red-500 @enderror">
                                @error('password_confirmation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                    class="bg-red-600 text-white px-6 py-3 rounded-md hover:bg-red-700">
                                🔐 Zmień hasło
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informacje o koncie -->
            <div class="bg-white shadow rounded-lg mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">ℹ️ Informacje o koncie</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Status konta:</span>
                            <span class="ml-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ Auth::guard('b2b')->user()->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ Auth::guard('b2b')->user()->status_label }}
                            </span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Poziom cenowy:</span>
                            <span class="ml-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ Auth::guard('b2b')->user()->pricing_tier_label }}
                            </span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Data założenia:</span>
                            <span class="ml-2 text-gray-900">{{ Auth::guard('b2b')->user()->created_at->format('d.m.Y') }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Limit kredytowy:</span>
                            <span class="ml-2 text-gray-900">{{ number_format(Auth::guard('b2b')->user()->credit_limit, 2) }} zł</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
