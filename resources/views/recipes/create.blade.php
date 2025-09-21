<x-layouts.app title="Dodaj recepturę">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Dodaj nową recepturę</h1>
            <p class="text-gray-600 mb-8">Tutaj będzie formularz do tworzenia receptur z procesami technologicznymi</p>

            <div class="text-center py-12">
                <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Formularz w budowie</h3>
                <p class="text-gray-500">Sekcje: dane podstawowe, składniki, procesy technologiczne, parametry wypiekania</p>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('recipes.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Anuluj
                </a>
                <button disabled
                        class="px-4 py-2 bg-gray-400 border border-transparent rounded-md shadow-sm text-sm font-medium text-white cursor-not-allowed">
                    Zapisz recepturę
                </button>
            </div>
        </div>
    </div>
</x-layouts.app>

