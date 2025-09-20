<x-layouts.app title="Kartoteka receptur">
    <div class="max-w-7xl mx-auto space-y-6">
        {{-- Nagłówek --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kartoteka receptur</h1>
                <p class="text-gray-600">Zarządzaj przepisami i procesami produkcyjnymi</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('recipes.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Dodaj recepturę
                </a>
            </div>
        </div>

        {{-- Statystyki --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <span class="text-white font-semibold">📋</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Wszystkie receptury</dt>
                                <dd class="text-lg font-medium text-gray-900">0</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <span class="text-white font-semibold">✅</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Przetestowane</dt>
                                <dd class="text-lg font-medium text-gray-900">0</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <span class="text-white font-semibold">⏱️</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Średni czas</dt>
                                <dd class="text-lg font-medium text-gray-900">0 min</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <span class="text-white font-semibold">🏷️</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Kategorie</dt>
                                <dd class="text-lg font-medium text-gray-900">0</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Placeholder dla listy receptur --}}
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Kartoteka receptur</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Tutaj będą przepisy z procesami technologicznymi:<br>
                    • Mieszanie składników<br>
                    • Wyrastanie ciasta<br>
                    • Formowanie<br>
                    • Wypiekanie<br>
                    • Chłodzenie i pakowanie
                </p>
                <div class="mt-6">
                    <a href="{{ route('recipes.create') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Dodaj pierwszą recepturę
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
