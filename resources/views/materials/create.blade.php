<x-layouts.app title="Dodaj surowiec">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Dodaj nowy surowiec</h1>
            <p class="text-gray-600 mb-8">Tutaj będzie formularz do dodawania surowców (mąka, cukier, drożdże, etc.)</p>

            <div class="text-center py-12">
                <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Formularz w budowie</h3>
                <p class="text-gray-500">Pola: kod, nazwa, typ, jednostka, dostawca, stany magazynowe, ceny</p>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('materials.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Anuluj
                </a>
                <button disabled
                        class="px-4 py-2 bg-gray-400 border border-transparent rounded-md shadow-sm text-sm font-medium text-white cursor-not-allowed">
                    Zapisz surowiec
                </button>
            </div>
        </div>
    </div>
</x-layouts.app>

