<x-layouts.app title="{{ $recipe->nazwa }} - Szczegóły receptury">
<div class="max-w-6xl mx-auto py-6">
    {{-- Header --}}
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $recipe->nazwa }}</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $recipe->aktywny ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $recipe->aktywny ? 'Aktywna' : 'Nieaktywna' }}
                    </span>
                    @if($recipe->testowany)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Testowana
                        </span>
                    @endif
                </div>
                <p class="text-gray-600 mb-4">{{ $recipe->opis }}</p>
                <div class="flex items-center space-x-6 text-sm text-gray-500">
                    <span><strong>Kod:</strong> {{ $recipe->kod }}</span>
                    <span><strong>Kategoria:</strong> {{ $recipe->kategoria }}</span>
                    <span><strong>Poziom:</strong> {{ $recipe->poziom_trudnosci }}</span>
                    <span><strong>Autor:</strong> {{ $recipe->autor }}</span>
                    <span><strong>Wersja:</strong> {{ $recipe->wersja }}</span>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('recipes.edit', $recipe) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edytuj
                </a>
                <a href="{{ route('recipes.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Powrót
                </a>
            </div>
        </div>
    </div>

    @include('recipes.partials.basic-info')
    @include('recipes.partials.processes')
    @include('recipes.partials.summary')
    @include('recipes.partials.cost-analysis')
</div>
</x-layouts.app>
