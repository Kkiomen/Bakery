<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Dane podstawowe -->
    <div class="md:col-span-2">
        <h4 class="text-lg font-medium text-gray-900 mb-4">📋 Dane podstawowe</h4>
    </div>

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
            Email <span class="text-red-500">*</span>
        </label>
        <input type="email" wire:model="email"
               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Adres -->
    <div class="md:col-span-2">
        <h4 class="text-lg font-medium text-gray-900 mb-4 mt-6">📍 Adres</h4>
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

    <!-- Kontakt -->
    <div class="md:col-span-2">
        <h4 class="text-lg font-medium text-gray-900 mb-4 mt-6">📞 Kontakt</h4>
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

    <!-- Biznes -->
    <div class="md:col-span-2">
        <h4 class="text-lg font-medium text-gray-900 mb-4 mt-6">🏢 Działalność</h4>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Typ działalności <span class="text-red-500">*</span>
        </label>
        <select wire:model="business_type"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('business_type') border-red-500 @enderror">
            <option value="">Wybierz typ</option>
            <option value="hotel">Hotel</option>
            <option value="restaurant">Restauracja</option>
            <option value="cafe">Kawiarnia</option>
            <option value="shop">Sklep</option>
            <option value="catering">Catering</option>
            <option value="other">Inne</option>
        </select>
        @error('business_type')
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

    <!-- Ustawienia konta -->
    <div class="md:col-span-2">
        <h4 class="text-lg font-medium text-gray-900 mb-4 mt-6">⚙️ Ustawienia konta</h4>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Status <span class="text-red-500">*</span>
        </label>
        <select wire:model="status"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
            <option value="pending">Oczekujący</option>
            <option value="active">Aktywny</option>
            <option value="suspended">Zawieszony</option>
            <option value="inactive">Nieaktywny</option>
        </select>
        @error('status')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Poziom cenowy <span class="text-red-500">*</span>
        </label>
        <select wire:model="pricing_tier"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('pricing_tier') border-red-500 @enderror">
            <option value="standard">Standard</option>
            <option value="bronze">Brązowy</option>
            <option value="silver">Srebrny</option>
            <option value="gold">Złoty</option>
            <option value="platinum">Platynowy</option>
        </select>
        @error('pricing_tier')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Limit kredytowy (zł) <span class="text-red-500">*</span>
        </label>
        <input type="number" wire:model="credit_limit" step="0.01" min="0"
               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('credit_limit') border-red-500 @enderror">
        @error('credit_limit')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Aktualne zadłużenie (zł) <span class="text-red-500">*</span>
        </label>
        <input type="number" wire:model="current_balance" step="0.01" min="0"
               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('current_balance') border-red-500 @enderror">
        @error('current_balance')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Uwagi wewnętrzne</label>
        <textarea wire:model="notes"
                  rows="3"
                  placeholder="Notatki dla wewnętrznego użytku..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror"></textarea>
        @error('notes')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
