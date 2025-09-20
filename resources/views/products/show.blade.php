<x-layouts.app title="Szczegóły produktu">
    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Nagłówek --}}
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $product->nazwa }}</h1>
                <p class="text-gray-600">SKU: {{ $product->sku }}</p>
                @if($product->ean)
                    <p class="text-gray-600">EAN: {{ $product->ean }}</p>
                @endif
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('products.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Wróć do listy
                </a>
                <a href="{{ route('products.edit', $product) }}"
                   class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                    Edytuj
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Główne informacje --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Podstawowe dane --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Podstawowe informacje</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Kategoria</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->category->nazwa }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->aktywny ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->aktywny ? 'Aktywny' : 'Nieaktywny' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Waga</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->waga_g }} g ({{ $product->waga_kg }})</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jednostka sprzedaży</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $product->jednostka_sprzedazy }}
                                @if($product->zawartosc_opakowania)
                                    ({{ $product->zawartosc_opakowania }} szt./opak.)
                                @endif
                            </dd>
                        </div>
                    </dl>

                    @if($product->opis)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500">Opis</dt>
                            <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $product->opis }}</dd>
                        </div>
                    @endif
                </div>

                {{-- Alergeny --}}
                @if($product->alergeny && count($product->alergeny) > 0)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Alergeny</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->alergeny as $allergen)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    {{ ucfirst($allergen) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Wartości odżywcze --}}
                @if($product->wartosci_odzywcze)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Wartości odżywcze (na 100g)</h2>
                        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @if(isset($product->wartosci_odzywcze['kcal']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Kalorie</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $product->wartosci_odzywcze['kcal'] }} kcal</dd>
                                </div>
                            @endif
                            @if(isset($product->wartosci_odzywcze['bialko_g']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Białko</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $product->wartosci_odzywcze['bialko_g'] }} g</dd>
                                </div>
                            @endif
                            @if(isset($product->wartosci_odzywcze['tluszcz_g']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tłuszcz</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $product->wartosci_odzywcze['tluszcz_g'] }} g</dd>
                                </div>
                            @endif
                            @if(isset($product->wartosci_odzywcze['wegle_g']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Węglowodany</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $product->wartosci_odzywcze['wegle_g'] }} g</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                @endif

                {{-- Zamienniki --}}
                @if($product->substitutes->count() > 0)
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Zamienniki</h2>
                            <a href="{{ route('products.substitutes', $product) }}"
                               class="text-sm text-blue-600 hover:text-blue-900">
                                Zarządzaj zamiennikami
                            </a>
                        </div>
                        <div class="space-y-3">
                            @foreach($product->substitutes->sortBy('pivot.priorytet') as $substitute)
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">{{ $substitute->nazwa }}</h4>
                                            <p class="text-sm text-gray-500">SKU: {{ $substitute->sku }} | {{ $substitute->waga_kg }}</p>
                                            @if($substitute->pivot->uwagi)
                                                <p class="text-sm text-gray-600">{{ $substitute->pivot->uwagi }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Priorytet: {{ $substitute->pivot->priorytet }}
                                        </span>
                                        <p class="text-sm text-gray-500 mt-1">{{ $substitute->cena_netto }} zł</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Panel boczny --}}
            <div class="space-y-6">
                {{-- Ceny i VAT --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Ceny i VAT</h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cena netto</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $product->cena_netto }} zł</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Stawka VAT</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->stawka_vat }}%</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cena brutto</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ number_format($product->cena_netto_zl * (1 + $product->stawka_vat / 100), 2, ',', '') }} zł
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Obrazy --}}
                @if($product->images->count() > 0)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Zdjęcia</h2>
                        <div class="grid grid-cols-1 gap-3">
                            @foreach($product->images as $image)
                                <div class="relative">
                                    <img src="{{ $image->url }}"
                                         alt="{{ $image->alt_text ?: $product->nazwa }}"
                                         class="w-full h-32 object-cover rounded-lg">
                                    @if($image->is_primary)
                                        <span class="absolute top-2 left-2 inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-600 text-white">
                                            Główne
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- SEO --}}
                @if($product->meta_title || $product->meta_description)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">SEO</h2>
                        <dl class="space-y-3">
                            @if($product->meta_title)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Meta title</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $product->meta_title }}</dd>
                                </div>
                            @endif
                            @if($product->meta_description)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Meta description</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $product->meta_description }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                @endif

                {{-- Metadane --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Informacje systemowe</h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Utworzono</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->created_at->format('d.m.Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ostatnia aktualizacja</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->updated_at->format('d.m.Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
