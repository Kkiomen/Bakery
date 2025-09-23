<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Test Livewire</h1>

    <div class="bg-yellow-100 p-4 mb-4 rounded">
        <p><strong>Status:</strong> {{ $testMessage }}</p>
        <p><strong>Modal:</strong> {{ $showModal ? 'OTWARTY' : 'ZAMKNIĘTY' }}</p>
    </div>

    <div class="space-x-4">
        <button wire:click="openModal"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Otwórz Modal
        </button>

        <button wire:click="closeModal"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            Zamknij Modal
        </button>
    </div>

    @if($showModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg max-w-md w-full">
                <h2 class="text-lg font-bold mb-4">Test Modal</h2>
                <p>To jest testowy modal Livewire!</p>
                <div class="mt-4">
                    <button wire:click="closeModal"
                            class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                        Zamknij
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

