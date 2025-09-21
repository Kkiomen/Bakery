<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Test Dostaw</h1>

    <div class="bg-yellow-100 p-4 mb-4 rounded">
        <p><strong>Status:</strong> {{ $testMessage }}</p>
        <p><strong>Modal:</strong> {{ $showCreateForm ? 'OTWARTY' : 'ZAMKNIĘTY' }}</p>
    </div>

    <button wire:click="showCreateForm"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 mr-4">
        🚚 Nowa Dostawa
    </button>

    @if($showCreateForm)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Nowa Dostawa</h3>
                    <button wire:click="hideCreateForm"
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <p>To jest testowy modal dostaw!</p>
                    <p>Jeśli to działa, to problem jest w oryginalnym komponencie DeliveryManagement.</p>

                    <div class="mt-4">
                        <button wire:click="hideCreateForm"
                                class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                            Zamknij
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
