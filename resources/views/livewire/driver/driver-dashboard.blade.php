<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Panel Kierowcy</h1>
            <p class="text-sm text-gray-600 mt-1">
                Zarządzaj swoimi dostawami na {{ \Carbon\Carbon::parse($selectedDate)->format('d.m.Y') }}
            </p>
        </div>

        <div class="flex space-x-3 mt-4 sm:mt-0">
            <flux:button wire:click="refreshDeliveries" variant="outline">
                🔄 Odśwież
            </flux:button>
            <flux:button wire:click="toggleCompleted" variant="outline">
                {{ $showCompleted ? 'Ukryj zakończone' : 'Pokaż zakończone' }}
            </flux:button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        🚚
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Wszystkie</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        ⏰
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Przypisane</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['przypisane'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        🚛
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">W drodze</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['w_drodze'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        ✅
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Dostarczone</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['dostarczone'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                <div class="flex space-x-2">
                    <flux:input type="date" wire:model.live="selectedDate" class="flex-1" />
                    <flux:button wire:click="showToday" variant="outline" size="sm">
                        Dzisiaj
                    </flux:button>
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    Wybrana: {{ \Carbon\Carbon::parse($selectedDate)->format('d.m.Y') }}
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="statusFilter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Wszystkie</option>
                    <option value="przypisana">Przypisane</option>
                    <option value="w_drodze">W drodze</option>
                    <option value="dostarczona">Dostarczone</option>
                    <option value="problem">Problemy</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Deliveries List -->
    <div class="space-y-4">
        @forelse($deliveries as $delivery)
        <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200">
            <!-- Delivery Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <span class="text-lg font-bold text-blue-600">
                                    {{ $delivery->kolejnosc_dostawy }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $delivery->numer_dostawy }}</h3>
                            <p class="text-sm text-gray-500">{{ $delivery->klient_nazwa }}</p>
                        </div>

                        <flux:badge :color="$delivery->status_color">
                            {{ $delivery->status_label }}
                        </flux:badge>

                        @if($delivery->priorytet !== 'normalny')
                            <flux:badge :color="$delivery->priorytet_color" size="sm">
                                {{ $delivery->priorytet_label }}
                            </flux:badge>
                        @endif
                    </div>

                    <div class="flex items-center space-x-2">
                        @if($delivery->godzina_planowana)
                            <div class="text-sm text-gray-500">
                                ⏰
                                {{ $delivery->godzina_planowana->format('H:i') }}
                            </div>
                        @endif

                        <flux:button wire:click="viewDelivery({{ $delivery->id }})" variant="outline" size="sm">
                            Szczegóły
                        </flux:button>
                    </div>
                </div>
            </div>

            <!-- Delivery Content -->
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <!-- Address -->
                    <div class="lg:col-span-2">
                        <div class="flex items-start space-x-2">
                            📍
                            <div>
                                <div class="font-medium text-gray-900">{{ $delivery->klient_adres }}</div>
                                @if($delivery->kod_pocztowy || $delivery->miasto)
                                    <div class="text-sm text-gray-500">
                                        {{ $delivery->kod_pocztowy }} {{ $delivery->miasto }}
                                    </div>
                                @endif

                                @if($delivery->osoba_kontaktowa || $delivery->telefon_kontaktowy)
                                    <div class="text-sm text-gray-500 mt-1">
                                        @if($delivery->osoba_kontaktowa)
                                            <span>{{ $delivery->osoba_kontaktowa }}</span>
                                        @endif
                                        @if($delivery->telefon_kontaktowy)
                                            <a href="tel:{{ $delivery->telefon_kontaktowy }}"
                                               class="text-blue-600 hover:text-blue-800 ml-2">
                                                📞
                                                {{ $delivery->telefon_kontaktowy }}
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col space-y-2">
                        @if($delivery->canBeStarted())
                            <flux:button wire:click="startDelivery({{ $delivery->id }})"
                                       variant="primary" size="sm">
                                Rozpocznij dostawę
                            </flux:button>
                        @endif

                        @if($delivery->status === 'w_drodze')
                            <flux:button wire:click="openPhotoUpload({{ $delivery->id }})"
                                       variant="outline" size="sm">
                                Dodaj zdjęcia
                            </flux:button>

                            <flux:button wire:click="openSignatureModal({{ $delivery->id }})"
                                       variant="outline" size="sm">
                                Podpis odbiorcy
                            </flux:button>
                        @endif

                        @if($delivery->canBeCompleted())
                            <flux:button wire:click="completeDelivery({{ $delivery->id }})"
                                       variant="primary" size="sm"
                                       wire:confirm="Czy na pewno chcesz zakończyć tę dostawę?">
                                Zakończ dostawę
                            </flux:button>
                        @endif

                        <a href="{{ $delivery->google_maps_url }}"
                           target="_blank"
                           class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100">
                            🗺️
                            Nawigacja
                        </a>
                    </div>
                </div>

                <!-- Items Summary -->
                @if($delivery->items->count() > 0)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">
                        Produkty do dostawy ({{ $delivery->items->count() }})
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach($delivery->items->take(6) as $item)
                        <div class="flex items-center justify-between bg-gray-50 rounded px-3 py-2">
                            <span class="text-sm text-gray-900">{{ $item->nazwa_produktu }}</span>
                            <span class="text-sm font-medium text-gray-600">
                                {{ $item->ilosc }} {{ $item->jednostka }}
                            </span>
                        </div>
                        @endforeach

                        @if($delivery->items->count() > 6)
                        <div class="flex items-center justify-center bg-gray-50 rounded px-3 py-2">
                            <span class="text-sm text-gray-500">
                                +{{ $delivery->items->count() - 6 }} więcej
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Notes -->
                @if($delivery->uwagi_dostawy)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-1">Uwagi do dostawy:</h4>
                    <p class="text-sm text-gray-600">{{ $delivery->uwagi_dostawy }}</p>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <div class="text-6xl mb-4">🚚</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Brak dostaw</h3>
            <p class="text-gray-500">
                Nie masz przypisanych dostaw na wybrany dzień.
            </p>
        </div>
        @endforelse
    </div>

    <!-- Delivery Details Modal -->
    @if($showDeliveryDetails && $currentDelivery)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Szczegóły dostawy {{ $currentDelivery->numer_dostawy }}</h3>
                    <button wire:click="closeModal"
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    @livewire('driver.delivery-details', ['delivery' => $currentDelivery], key($currentDelivery->id))
                </div>
            </div>
        </div>
    @endif

    <!-- Photo Upload Modal -->
    @if($showPhotoUpload && $currentDelivery)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-lg w-full">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Dodaj zdjęcia - {{ $currentDelivery->numer_dostawy }}</h3>
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
                                   wire:model="photos"
                                   multiple
                                   accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('photos')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            @error('photos.*')
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
    @if($showSignatureModal && $currentDelivery)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Podpis odbiorcy - {{ $currentDelivery->numer_dostawy }}</h3>
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
                                <canvas id="signature-pad"
                                        class="w-full h-48 border border-gray-200 rounded cursor-crosshair"
                                        style="touch-action: none;">
                                </canvas>
                                <div class="flex justify-between mt-2">
                                    <button type="button" onclick="clearSignature()"
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
</div>

@script
<script>
    let signaturePad;

    // Initialize signature pad when modal opens
    $wire.on('showSignatureModal', () => {
        console.log('showSignatureModal event received (dashboard)');
        setTimeout(() => {
            const canvas = document.getElementById('signature-pad');
            console.log('Canvas element:', canvas);

            if (canvas && typeof SignaturePad !== 'undefined') {
                console.log('Initializing SignaturePad (dashboard)');
                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)',
                    minWidth: 1,
                    maxWidth: 3,
                    throttle: 16,
                    minDistance: 5
                });

                signaturePad.addEventListener('endStroke', () => {
                    console.log('Signature stroke ended (dashboard)');
                    $wire.updateSignatureData(signaturePad.toDataURL());
                });

                // Resize canvas
                const resizeCanvas = () => {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    const rect = canvas.getBoundingClientRect();
                    canvas.width = rect.width * ratio;
                    canvas.height = rect.height * ratio;
                    canvas.getContext('2d').scale(ratio, ratio);
                    canvas.style.width = rect.width + 'px';
                    canvas.style.height = rect.height + 'px';
                    if (signaturePad) {
                        signaturePad.clear();
                    }
                };

                window.addEventListener('resize', resizeCanvas);
                resizeCanvas();

                // Load existing signature if available
                setTimeout(() => {
                    loadExistingSignatureDashboard();
                }, 100);
            } else {
                console.error('Canvas not found or SignaturePad not loaded (dashboard)');
            }
        }, 200);
    });

    // Function to load existing signature (dashboard)
    function loadExistingSignatureDashboard() {
        // Get signature data from Livewire component
        const signatureData = @json($signatureData ?? '');
        console.log('Loading existing signature (dashboard):', signatureData ? 'Found' : 'None');

        if (signatureData && signaturePad && signatureData.startsWith('data:image')) {
            console.log('Loading signature onto canvas (dashboard)');
            signaturePad.fromDataURL(signatureData);
        }
    }

    // Clear signature function
    window.clearSignature = () => {
        console.log('Clear signature called (dashboard)');
        if (signaturePad) {
            signaturePad.clear();
            $wire.updateSignatureData('');
            console.log('Signature cleared (dashboard)');
        } else {
            console.error('SignaturePad not initialized (dashboard)');
        }
    };

    // Auto-refresh every 30 seconds
    setInterval(() => {
        $wire.$refresh();
    }, 30000);

    // Event listeners
    $wire.on('delivery-started', (data) => {
        // Show success notification
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

    // Get current location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
            $wire.getLocationAndUpdateSignature(
                position.coords.latitude,
                position.coords.longitude
            );
        });
    }
</script>
@endscript

@push('scripts')
<!-- Signature Pad Library -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
@endpush
