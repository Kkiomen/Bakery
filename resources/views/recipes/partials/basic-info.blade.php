<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Lewa kolumna - Informacje podstawowe --}}
    <div class="lg:col-span-1 space-y-6">
        {{-- Podstawowe informacje --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">📊 Podstawowe informacje</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Ilość porcji</dt>
                    <dd class="text-sm text-gray-900">{{ $recipe->ilosc_porcji }} szt</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Waga jednostkowa</dt>
                    <dd class="text-sm text-gray-900">{{ $recipe->waga_jednostkowa_g }}g</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Całkowita waga</dt>
                    <dd class="text-sm text-gray-900">{{ $recipe->ilosc_porcji * $recipe->waga_jednostkowa_g }}g</dd>
                </div>
            </dl>
        </div>

        {{-- Czasy --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">⏱️ Czasy</h3>
            <dl class="space-y-3">
                @if($recipe->czas_przygotowania_min)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Przygotowanie</dt>
                        <dd class="text-sm text-gray-900">{{ $recipe->czas_przygotowania_min }} min</dd>
                    </div>
                @endif
                @if($recipe->czas_wypiekania_min)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Wypiekanie</dt>
                        <dd class="text-sm text-gray-900">{{ $recipe->czas_wypiekania_min }} min</dd>
                    </div>
                @endif
                @if($recipe->czas_calkowity_min)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Czas całkowity</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $recipe->czas_calkowity_min }} min</dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Temperatura --}}
        @if($recipe->temperatura_c)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">🌡️ Temperatura</h3>
                <div class="text-center">
                    <div class="text-3xl font-bold text-orange-600">{{ $recipe->temperatura_c }}°C</div>
                    <div class="text-sm text-gray-500">Temperatura wypiekania</div>
                </div>
            </div>
        @endif

        {{-- Instrukcje wypiekania --}}
        @if($recipe->instrukcje_wypiekania)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">🔥 Instrukcje wypiekania</h3>
                <p class="text-sm text-gray-700">{{ $recipe->instrukcje_wypiekania }}</p>
            </div>
        @endif

        {{-- Wskazówki --}}
        @if($recipe->wskazowki)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">💡 Wskazówki</h3>
                <p class="text-sm text-gray-700">{{ $recipe->wskazowki }}</p>
            </div>
        @endif

        {{-- Uwagi --}}
        @if($recipe->uwagi)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">📝 Uwagi</h3>
                <p class="text-sm text-gray-700">{{ $recipe->uwagi }}</p>
            </div>
        @endif
    </div>
