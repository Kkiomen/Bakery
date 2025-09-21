<div class="min-h-screen bg-gray-50">
    <!-- Nagłówek -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🔄 Zamówienia Cykliczne</h1>
                    <p class="text-gray-600">{{ Auth::guard('b2b')->user()->company_name }}</p>
                </div>
                <div class="flex space-x-4">
                    <button wire:click="openCreateModal" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        ➕ Nowe zamówienie cykliczne
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
                               placeholder="Nazwa zamówienia..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model.live="statusFilter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Wszystkie</option>
                            <option value="active">Aktywne</option>
                            <option value="inactive">Nieaktywne</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Częstotliwość</label>
                        <select wire:model.live="frequencyFilter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Wszystkie</option>
                            <option value="daily">Codziennie</option>
                            <option value="weekly">Tygodniowo</option>
                            <option value="monthly">Miesięcznie</option>
                            <option value="custom">Niestandardowo</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista zamówień cyklicznych -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            @forelse($recurringOrders as $recurringOrder)
                <div class="border-b border-gray-200 last:border-b-0">
                    <div class="px-6 py-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-4">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $recurringOrder->name }}
                                        </h3>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $recurringOrder->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $recurringOrder->status_label }}
                                        </span>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $recurringOrder->frequency_label }}
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">Wygenerowano: {{ $recurringOrder->total_generated }} zamówień</p>
                                        <p class="text-lg font-bold text-gray-900">
                                            ~{{ number_format($recurringOrder->estimated_total, 2) }} zł
                                        </p>
                                    </div>
                                </div>

                                @if($recurringOrder->description)
                                    <p class="text-gray-600 mb-3">{{ $recurringOrder->description }}</p>
                                @endif

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm text-gray-600 mb-3">
                                    <div>
                                        <span class="font-medium">Data rozpoczęcia:</span><br>
                                        {{ $recurringOrder->start_date->format('d.m.Y') }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Następna dostawa:</span><br>
                                        {{ $recurringOrder->next_generation_at ? $recurringOrder->next_generation_at->format('d.m.Y H:i') : 'Brak' }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Adres dostawy:</span><br>
                                        {{ $recurringOrder->delivery_address }}, {{ $recurringOrder->delivery_city }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Auto-potwierdzanie:</span><br>
                                        {{ $recurringOrder->auto_confirm ? 'Tak' : 'Nie' }}
                                    </div>
                                </div>

                                <!-- Produkty w zamówieniu -->
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <h4 class="font-medium text-gray-700 mb-2">Produkty w zamówieniu:</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                        @foreach($recurringOrder->order_items as $item)
                                            <div class="flex justify-between">
                                                <span>{{ $item['product_name'] }} ({{ $item['quantity'] }} szt.)</span>
                                                <span class="font-medium">{{ number_format($item['quantity'] * $item['unit_price_gross'], 2) }} zł</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="ml-6 flex flex-col space-y-2">
                                <button wire:click="openEditModal({{ $recurringOrder->id }})"
                                        class="bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">
                                    ✏️ Edytuj
                                </button>
                                
                                @if($recurringOrder->is_active)
                                    <button wire:click="toggleStatus({{ $recurringOrder->id }})"
                                            class="bg-yellow-600 text-white px-3 py-2 rounded text-sm hover:bg-yellow-700">
                                        ⏸️ Wstrzymaj
                                    </button>
                                @else
                                    <button wire:click="toggleStatus({{ $recurringOrder->id }})"
                                            class="bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700">
                                        ▶️ Wznów
                                    </button>
                                @endif

                                <button wire:click="generateNow({{ $recurringOrder->id }})"
                                        onclick="return confirm('Czy na pewno chcesz wygenerować zamówienie teraz?')"
                                        class="bg-purple-600 text-white px-3 py-2 rounded text-sm hover:bg-purple-700">
                                    🚀 Generuj teraz
                                </button>

                                <button wire:click="deleteRecurringOrder({{ $recurringOrder->id }})"
                                        onclick="return confirm('Czy na pewno chcesz usunąć to zamówienie cykliczne?')"
                                        class="bg-red-600 text-white px-3 py-2 rounded text-sm hover:bg-red-700">
                                    🗑️ Usuń
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <span class="text-gray-400 text-6xl">🔄</span>
                    <h3 class="text-lg font-medium text-gray-900 mt-4">Brak zamówień cyklicznych</h3>
                    <p class="text-gray-600">Nie masz jeszcze żadnych zamówień cyklicznych.</p>
                    <div class="mt-6">
                        <button wire:click="openCreateModal"
                                class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                            ➕ Utwórz pierwsze zamówienie cykliczne
                        </button>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Paginacja -->
        @if($recurringOrders->hasPages())
            <div class="mt-8">
                {{ $recurringOrders->links() }}
            </div>
        @endif
    </div>

    <!-- Modal tworzenia zamówienia cyklicznego -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">➕ Nowe zamówienie cykliczne</h3>
                    <button wire:click="closeCreateModal"
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form wire:submit="createRecurringOrder" class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Podstawowe informacje -->
                        <div class="md:col-span-2">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">📋 Podstawowe informacje</h4>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nazwa zamówienia <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Częstotliwość</label>
                            <select wire:model.live="frequency"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="daily">Codziennie</option>
                                <option value="weekly">Tygodniowo</option>
                                <option value="monthly">Miesięcznie</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Opis</label>
                            <textarea wire:model="description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <!-- Harmonogram -->
                        <div class="md:col-span-2">
                            <h4 class="text-lg font-medium text-gray-900 mb-4 mt-6">📅 Harmonogram</h4>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data rozpoczęcia</label>
                            <input type="date" wire:model="start_date"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data zakończenia (opcjonalna)</label>
                            <input type="date" wire:model="end_date"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Ustawienia -->
                        <div class="md:col-span-2">
                            <h4 class="text-lg font-medium text-gray-900 mb-4 mt-6">⚙️ Ustawienia</h4>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" wire:model="auto_confirm" id="auto_confirm"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="auto_confirm" class="ml-2 text-sm text-gray-700">
                                Automatyczne potwierdzanie zamówień
                            </label>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dni przed powiadomieniem</label>
                            <input type="number" wire:model="days_before_notification" min="0" max="7"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Produkty -->
                    <div class="mt-8">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">🛒 Produkty w zamówieniu</h4>
                        
                        @if(empty($order_items))
                            <div class="text-center py-8 bg-gray-50 rounded-lg">
                                <span class="text-gray-400 text-4xl">📦</span>
                                <p class="text-gray-600 mt-2">Brak produktów w zamówieniu</p>
                                <p class="text-sm text-gray-500">Wybierz produkty z listy poniżej</p>
                            </div>
                        @else
                            <div class="space-y-3 mb-6">
                                @foreach($order_items as $index => $item)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <span class="font-medium">{{ $item['product_name'] }}</span>
                                            <span class="text-gray-500 ml-2">{{ number_format($item['unit_price_gross'], 2) }} zł/szt</span>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <input type="number" 
                                                   wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                                   value="{{ $item['quantity'] }}" min="1"
                                                   class="w-20 px-2 py-1 border border-gray-300 rounded text-center">
                                            <span class="font-medium">{{ number_format($item['quantity'] * $item['unit_price_gross'], 2) }} zł</span>
                                            <button type="button" wire:click="removeProduct({{ $index }})"
                                                    class="text-red-600 hover:text-red-800">
                                                🗑️
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Lista dostępnych produktów -->
                        <div class="border-t pt-4">
                            <h5 class="font-medium text-gray-700 mb-3">Dodaj produkty:</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($availableProducts as $product)
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                        <div>
                                            <span class="font-medium">{{ $product->nazwa }}</span>
                                            <span class="text-gray-500 text-sm block">{{ $product->kategoria }}</span>
                                        </div>
                                        <button type="button" wire:click="addProduct({{ $product->id }})"
                                                class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                            ➕ Dodaj
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeCreateModal"
                                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                            Anuluj
                        </button>
                        <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            💾 Utwórz zamówienie cykliczne
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>