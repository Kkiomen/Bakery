<x-layouts.app title="Szczegóły surowca - {{ $material->nazwa }}">
    <div class="max-w-6xl mx-auto space-y-6">
        {{-- Nagłówek --}}
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $material->nazwa }}</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $material->aktywny ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $material->aktywny ? 'Aktywny' : 'Nieaktywny' }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $material->getStockStatusColor() === 'green' ? 'bg-green-100 text-green-800' :
                           ($material->getStockStatusColor() === 'yellow' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ $material->getStockStatusLabel() }}
                    </span>
                </div>
                <p class="text-gray-600 mt-1">Kod: <span class="font-mono">{{ $material->kod }}</span></p>
                @if($material->opis)
                    <p class="text-gray-600 mt-2">{{ $material->opis }}</p>
                @endif
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('materials.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Powrót do listy
                </a>
                <a href="{{ route('materials.edit', $material) }}"
                   class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edytuj
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Kolumna główna --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Dane podstawowe --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Dane podstawowe</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Typ surowca</dt>
                            <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $material->typ }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jednostka podstawowa</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $material->jednostka_podstawowa }}</dd>
                        </div>
                        @if($material->waga_opakowania)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Waga opakowania</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($material->waga_opakowania, 3) }} {{ $material->jednostka_podstawowa }}</dd>
                        </div>
                        @endif
                        @if($material->dostawca)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dostawca</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $material->dostawca }}</dd>
                        </div>
                        @endif
                        @if($material->dni_waznosci)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dni ważności</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $material->dni_waznosci }} dni</dd>
                        </div>
                        @endif
                        @if($material->data_ostatniej_dostawy)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ostatnia dostawa</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $material->data_ostatniej_dostawy->format('d.m.Y') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                {{-- Stany magazynowe --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Stany magazynowe</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Stan aktualny</dt>
                            <dd class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($material->stan_aktualny, 3) }}</dd>
                            <dd class="text-sm text-gray-500">{{ $material->jednostka_podstawowa }}</dd>
                        </div>
                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Stan minimalny</dt>
                            <dd class="mt-2 text-2xl font-bold text-yellow-600">{{ number_format($material->stan_minimalny, 3) }}</dd>
                            <dd class="text-sm text-gray-500">{{ $material->jednostka_podstawowa }}</dd>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">Stan optymalny</dt>
                            <dd class="mt-2 text-2xl font-bold text-green-600">{{ number_format($material->stan_optymalny, 3) }}</dd>
                            <dd class="text-sm text-gray-500">{{ $material->jednostka_podstawowa }}</dd>
                        </div>
                    </div>

                    {{-- Pasek stanu --}}
                    <div class="mt-4">
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>Poziom zapasów</span>
                            <span class="font-semibold">{{ number_format(($material->stan_aktualny / $material->stan_optymalny) * 100, 1) }}%</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Brak zapasów</span>
                            <span class="text-yellow-600">Minimalny: {{ number_format($material->stan_minimalny, 1) }}</span>
                            <span class="text-green-600">Optymalny: {{ number_format($material->stan_optymalny, 1) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Ceny i koszty --}}
                @if($material->cena_zakupu_gr)
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Ceny i koszty</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cena zakupu netto</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $material->cena_zakupu }} zł</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Stawka VAT</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $material->stawka_vat }}%</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Wartość magazynu</dt>
                            <dd class="mt-1 text-lg font-semibold text-green-600">{{ number_format($material->wartosc_magazynu, 2) }} zł</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cena za jednostkę</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $material->cena_zakupu }} zł/{{ $material->jednostka_podstawowa }}</dd>
                        </div>
                    </dl>
                </div>
                @endif

                {{-- Uwagi --}}
                @if($material->uwagi)
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Uwagi</h2>
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $material->uwagi }}</p>
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Szybkie akcje --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Szybkie akcje</h3>
                    <div class="space-y-3">
                        <a href="{{ route('materials.edit', $material) }}"
                           class="w-full flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edytuj surowiec
                        </a>

                        <button type="button"
                                class="w-full flex items-center px-3 py-2 border border-green-300 rounded-md shadow-sm text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Dodaj do magazynu
                        </button>

                        <button type="button"
                                class="w-full flex items-center px-3 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                            Wydaj z magazynu
                        </button>
                    </div>
                </div>

                {{-- Informacje systemowe --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informacje systemowe</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Data utworzenia</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $material->created_at->format('d.m.Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ostatnia modyfikacja</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $material->updated_at->format('d.m.Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID w systemie</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $material->id }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Statystyki --}}
        <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statystyki</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Wykorzystanie w recepturach</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $material->recipes->count() }} receptur</dd>
                        </div>
                        @if($material->cena_zakupu_gr)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Średni koszt miesięczny</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($material->wartosc_magazynu * 0.1, 2) }} zł</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dni do wyczerpania</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($material->stan_aktualny > 0)
                                    ~{{ ceil($material->stan_aktualny / max($material->stan_minimalny * 0.1, 0.1)) }} dni
                                @else
                                    Brak zapasów
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
