<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">🚚 Zarządzanie Dostawami - Uproszczona Wersja</h1>

    <!-- Debug info -->
    <div class="bg-yellow-100 p-4 mb-4 rounded">
        <div class="text-sm">
            <strong>Debug:</strong><br>
            showCreateForm = {{ $showCreateForm ? 'true' : 'false' }}<br>
            testMessage = {{ $testMessage }}
        </div>
    </div>

    <!-- Statystyki -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="font-semibold text-gray-700">Wszystkie dostawy</h3>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="font-semibold text-gray-700">Oczekujące</h3>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['oczekujace'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="font-semibold text-gray-700">Dostarczone</h3>
            <p class="text-2xl font-bold text-green-600">{{ $stats['dostarczone'] }}</p>
        </div>
    </div>

    <!-- Test przycisku -->
    <div class="mb-4 space-y-2">
        <button wire:click="openModal"
                class="flex items-center space-x-2 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:bg-blue-800 transition-colors touch-manipulation">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span class="font-medium">Nowa Dostawa</span>
        </button>

        <!-- Bardzo prosty test przycisk -->
        <button onclick="alert('Zwykły JS działa!')"
                class="px-4 py-2 bg-green-600 text-white rounded">
            Test zwykłego JS
        </button>
    </div>

    <!-- Lista dostaw -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Numer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kierowca</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($deliveries as $delivery)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $delivery->numer_dostawy }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $delivery->driver ? $delivery->driver->name : 'Nieprzypisany' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $delivery->status === 'dostarczona' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $delivery->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $delivery->data_dostawy ? \Carbon\Carbon::parse($delivery->data_dostawy)->format('d.m.Y') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            Brak dostaw
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal nowej dostawy -->
    @if($showCreateForm)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Nowa Dostawa</h3>
                    <button wire:click="closeModal"
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <p>To jest uproszczony modal dostaw!</p>
                    <p>Jeśli to działa, to problem jest w oryginalnym komponencie DeliveryManagement.</p>

                    <div class="mt-4 space-x-2">
                        <button wire:click="closeModal"
                                class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                            Anuluj
                        </button>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Zapisz
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
