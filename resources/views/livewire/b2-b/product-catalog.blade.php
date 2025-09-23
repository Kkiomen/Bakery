<div class="min-h-screen bg-gray-50">
    <!-- Nagłówek -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🛒 Katalog Produktów</h1>
                    <p class="text-gray-600">{{ Auth::guard('b2b')->user()->company_name }} - {{ Auth::guard('b2b')->user()->pricing_tier_label }}</p>
                </div>
                <div class="flex space-x-4">
                    <button wire:click="toggleCart"
                            class="relative bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                        🛒 Koszyk ({{ $this->getCartItemsCount() }})
                        @if($this->getCartItemsCount() > 0)
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 text-xs flex items-center justify-center font-bold animate-pulse">
                                {{ $this->getCartItemsCount() }}
                            </span>
                        @endif
                    </button>
                    <a href="{{ route('b2b.dashboard') }}"
                       class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        ← Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filtry -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Szukaj</label>
                        <input type="text" wire:model.live="search"
                               placeholder="Nazwa produktu..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategoria</label>
                        <select wire:model.live="categoryFilter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Wszystkie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->nazwa }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sortuj według</label>
                        <select wire:model.live="sortBy"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="nazwa">Nazwy</option>
                            <option value="kategoria_id">Kategorii</option>
                            <option value="created_at">Daty dodania</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kierunek</label>
                        <select wire:model.live="sortDirection"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="asc">Rosnąco</option>
                            <option value="desc">Malejąco</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista produktów -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($products as $product)
                <div class="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Zdjęcie produktu -->
                    <div class="aspect-w-16 aspect-h-9 bg-gray-200">
                        @php
                            $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                        @endphp
                        @if($primaryImage)
                            <img src="{{ $primaryImage->url }}"
                                 alt="{{ $primaryImage->alt_text ?? $product->nazwa }}"
                                 class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                <div class="text-center">
                                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-sm text-gray-500">Brak zdjęcia</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="p-4">
                        <!-- Nazwa i kategoria -->
                        <div class="mb-2">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $product->nazwa }}</h3>
                            <p class="text-sm text-gray-600">{{ $product->category->nazwa ?? 'Bez kategorii' }}</p>
                        </div>

                        <!-- Opis -->
                        @if($product->opis)
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $product->opis }}</p>
                        @endif

                        <!-- Ceny -->
                        @php
                            $pricing = Auth::guard('b2b')->user()->getPriceForProduct($product, 1);
                        @endphp

                        @if($pricing)
                            <div class="mb-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-green-600">
                                        {{ number_format($pricing->price_net, 2) }} zł
                                    </span>
                                    <span class="text-sm text-gray-500">netto</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">
                                        {{ number_format($pricing->price_gross, 2) }} zł brutto
                                    </span>
                                    @if($pricing->discount_percent > 0)
                                        <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">
                                            -{{ $pricing->discount_percent }}%
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Dodaj do koszyka -->
                            <div class="space-y-2">
                                <!-- Szybkie dodawanie 1 sztuki -->
                                <button data-product-id="{{ $product->id }}"
                                        onclick="quickAddToCart(this.dataset.productId, this)"
                                        class="w-full bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700 transition-colors font-medium">
                                    ⚡ Szybko dodaj (1 szt)
                                </button>

                                <!-- Dodawanie z wyborem ilości -->
                                <div class="flex items-center space-x-2">
                                    <input type="number"
                                           min="1"
                                           value="1"
                                           class="w-16 px-2 py-1 border border-gray-300 rounded text-center"
                                           id="qty-{{ $product->id }}">
                                    <button wire:click="addToCart({{ $product->id }}, document.getElementById('qty-{{ $product->id }}').value)"
                                            class="flex-1 bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition-colors">
                                        ➕ Dodaj
                                    </button>
                                </div>
                            </div>

                            <!-- Rabaty ilościowe -->
                            @php
                                $quantityPricing = \App\Models\B2BPricing::where('product_id', $product->id)
                                    ->where('pricing_tier', Auth::guard('b2b')->user()->pricing_tier)
                                    ->whereNull('b2_b_client_id')
                                    ->where('is_active', true)
                                    ->where('min_quantity', '>', 1)
                                    ->orderBy('min_quantity')
                                    ->limit(3)
                                    ->get();
                            @endphp

                            @if($quantityPricing->count() > 0)
                                <div class="mt-2 text-xs text-gray-500">
                                    <p class="font-medium text-green-600">💰 Rabaty ilościowe:</p>
                                    @foreach($quantityPricing as $qtyPrice)
                                        <p class="flex justify-between">
                                            <span>{{ $qtyPrice->min_quantity }}{{ $qtyPrice->max_quantity ? '-'.$qtyPrice->max_quantity : '+' }} szt:</span>
                                            <span class="font-medium text-green-600">
                                                {{ number_format($qtyPrice->price_net, 2) }} zł
                                                @if($qtyPrice->discount_percent > 0)
                                                    <span class="text-red-500">(-{{ $qtyPrice->discount_percent }}%)</span>
                                                @endif
                                            </span>
                                        </p>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="mb-4">
                                <span class="text-sm text-red-600">Cena niedostępna</span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <span class="text-gray-400 text-6xl">🔍</span>
                    <h3 class="text-lg font-medium text-gray-900 mt-4">Brak produktów</h3>
                    <p class="text-gray-600">Spróbuj zmienić filtry wyszukiwania.</p>
                </div>
            @endforelse
        </div>

        <!-- Paginacja -->
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Sticky Koszyk - zawsze widoczny na dole -->
    @if($this->getCartItemsCount() > 0)
        <div class="fixed bottom-0 left-0 right-0 bg-green-600 text-white shadow-lg border-t-2 border-green-700 z-40">
            <div class="max-w-7xl mx-auto px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <span class="text-2xl">🛒</span>
<div>
                                <div class="font-bold">{{ $this->getCartItemsCount() }} produktów w koszyku</div>
                                <div class="text-sm text-green-100">Wartość: {{ number_format($this->getCartTotalGross(), 2) }} zł brutto</div>
                            </div>
                        </div>

                        <!-- Miniaturki produktów -->
                        <div class="hidden md:flex items-center space-x-2">
                            @foreach(array_slice($cart, 0, 3) as $item)
                                <div class="bg-green-700 px-2 py-1 rounded text-xs">
                                    {{ $item['product_name'] }} ({{ $item['quantity'] }})
                                </div>
                            @endforeach
                            @if(count($cart) > 3)
                                <div class="bg-green-700 px-2 py-1 rounded text-xs">
                                    +{{ count($cart) - 3 }} więcej
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <button wire:click="toggleCart"
                                class="bg-green-700 hover:bg-green-800 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            👁️ Zobacz koszyk
                        </button>
                        <a href="{{ route('b2b.orders.create') }}"
                           class="bg-yellow-500 hover:bg-yellow-600 text-black px-4 py-2 rounded-md text-sm font-bold transition-colors">
                            ➡️ Zamów teraz
                        </a>
                        <button wire:click="clearCart"
                                onclick="return confirm('Czy na pewno chcesz wyczyścić koszyk?')"
                                class="bg-red-600 hover:bg-red-700 px-3 py-2 rounded-md text-sm transition-colors">
                            🗑️
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Padding na dole, żeby sticky koszyk nie zasłaniał treści -->
        <div class="h-20"></div>
    @else
        <!-- Floating Cart Button - gdy koszyk jest pusty -->
        <div class="fixed bottom-4 right-4 z-40">
            <button wire:click="toggleCart"
                    class="bg-green-600 hover:bg-green-700 text-white p-4 rounded-full shadow-lg transition-colors">
                <div class="flex items-center justify-center">
                    <span class="text-2xl">🛒</span>
                </div>
            </button>
        </div>
    @endif

    <!-- Floating Cart Button - dla niepustego koszyka (mobilne) -->
    @if($this->getCartItemsCount() > 0)
        <div class="fixed bottom-24 right-4 z-40 md:hidden">
            <button wire:click="toggleCart"
                    class="relative bg-green-600 hover:bg-green-700 text-white p-3 rounded-full shadow-lg transition-colors">
                <span class="text-xl">🛒</span>
                <span class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 text-xs flex items-center justify-center font-bold">
                    {{ $this->getCartItemsCount() }}
                </span>
            </button>
        </div>
    @endif

    <!-- Koszyk (modal) -->
    @if($showCart)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">🛒 Twój koszyk</h3>
                    <button wire:click="toggleCart"
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]">
                    @if(!empty($cart))
                        <div class="space-y-4">
                            @foreach($cart as $key => $item)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ $item['product_name'] }}</h4>
                                        <p class="text-sm text-gray-600">
                                            {{ number_format($item['unit_price'], 2) }} zł/szt
                                            @if(isset($item['discount_percent']) && $item['discount_percent'] > 0)
                                                <span class="text-red-600">(-{{ $item['discount_percent'] }}% rabat)</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Ilość: {{ $item['quantity'] }} szt
                                            • Razem: {{ number_format($item['line_total'] ?? 0, 2) }} zł netto
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <input type="number"
                                               wire:model.live="cart.{{ $key }}.quantity"
                                               wire:change="updateCartQuantity('{{ $key }}', $event.target.value)"
                                               min="1"
                                               class="w-16 px-2 py-1 border border-gray-300 rounded text-center">
                                        <span class="font-medium text-gray-900">
                                            {{ number_format($item['line_total'] ?? 0, 2) }} zł
                                        </span>
                                        <button wire:click="removeFromCart('{{ $key }}')"
                                                class="text-red-600 hover:text-red-800">
                                            🗑️
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Podsumowanie -->
                        <div class="mt-6 border-t border-gray-200 pt-6">
                            @php
                                $totalSavings = 0;
                                foreach($cart as $item) {
                                    if(isset($item['discount_percent']) && is_numeric($item['discount_percent']) && $item['discount_percent'] > 0) {
                                        $originalPrice = $item['unit_price'] / (1 - $item['discount_percent'] / 100);
                                        $savings = ($originalPrice - $item['unit_price']) * $item['quantity'];
                                        $totalSavings += $savings;
                                    }
                                }
                            @endphp

                            @if($totalSavings > 0)
                                <div class="flex justify-between items-center mb-2 text-green-600">
                                    <span class="font-medium">💰 Oszczędności (rabaty ilościowe):</span>
                                    <span class="font-medium">-{{ number_format($totalSavings, 2) }} zł</span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600">Suma netto:</span>
                                <span class="font-medium">{{ number_format($this->getCartTotal(), 2) }} zł</span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600">VAT:</span>
                                <span class="font-medium">{{ number_format($this->getCartTotalGross() - $this->getCartTotal(), 2) }} zł</span>
                            </div>
                            <div class="flex justify-between items-center text-lg font-bold">
                                <span>Suma brutto:</span>
                                <span>{{ number_format($this->getCartTotalGross(), 2) }} zł</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <span class="text-gray-400 text-6xl">🛒</span>
                            <h4 class="text-lg font-medium text-gray-900 mt-4">Koszyk jest pusty</h4>
                            <p class="text-gray-600">Dodaj produkty, aby złożyć zamówienie.</p>
                        </div>
                    @endif
                </div>

                @if(!empty($cart))
                    <div class="px-6 py-4 border-t border-gray-200 flex justify-between">
                        <button wire:click="clearCart"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            🗑️ Wyczyść koszyk
                        </button>
                        <div class="space-x-3">
                            <button wire:click="toggleCart"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Kontynuuj zakupy
                            </button>
                            <a href="{{ route('b2b.orders.create') }}"
                               class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                ➡️ Złóż zamówienie
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Toast powiadomienia -->
    <div x-data="{ show: false, message: '' }"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed top-20 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 max-w-sm">
        <div class="flex items-center space-x-2">
            <span class="text-xl">✅</span>
            <span x-text="message" class="font-medium"></span>
        </div>
    </div>

    <!-- Script dla smooth scroll i lepszego UX -->
    <script>
        let toastElement = null;

        // Smooth scroll do góry po kliknięciu w koszyk
        document.addEventListener('livewire:initialized', () => {
            toastElement = document.querySelector('[x-data*="show: false"]');

            Livewire.on('product-added-to-cart', (event) => {
                console.log('Livewire event received:', event);

                // Pobierz dane z eventu
                const data = Array.isArray(event) ? event[0] : event;
                const productName = data?.productName || 'Produkt';
                const quantity = data?.quantity || '1';

                // Pokaż toast
                if (toastElement && toastElement._x_dataStack) {
                    toastElement._x_dataStack[0].message = `Dodano: ${productName} (${quantity} szt.)`;
                    toastElement._x_dataStack[0].show = true;
                    setTimeout(() => {
                        if (toastElement._x_dataStack) {
                            toastElement._x_dataStack[0].show = false;
                        }
                    }, 3000);
                }

                // Animacja pulsowania przycisku koszyka
                const cartButtons = document.querySelectorAll('[wire\\:click="toggleCart"]');
                cartButtons.forEach(button => {
                    button.classList.add('animate-pulse');
                    setTimeout(() => {
                        button.classList.remove('animate-pulse');
                    }, 1000);
                });
            });
        });

        // Funkcja do szybkiego dodawania produktu
        function quickAddToCart(productId, buttonElement) {
            // Animacja przycisku
            const originalText = buttonElement.innerHTML;
            buttonElement.innerHTML = '⏳ Dodawanie...';
            buttonElement.disabled = true;

            // Wywołaj Livewire metodą wire:click
            const wireClick = buttonElement.closest('[wire\\:id]');
            if (wireClick && window.Livewire) {
                const componentId = wireClick.getAttribute('wire:id');
                const component = window.Livewire.find(componentId);
                if (component) {
                    component.call('addToCart', parseInt(productId), 1).then(() => {
                        buttonElement.innerHTML = '✅ Dodano!';
                        setTimeout(() => {
                            buttonElement.innerHTML = originalText;
                            buttonElement.disabled = false;
                        }, 1500);
                    }).catch((error) => {
                        console.error('Error adding to cart:', error);
                        buttonElement.innerHTML = originalText;
                        buttonElement.disabled = false;
                    });
                }
            }
        }
    </script>
</div>
