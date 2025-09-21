<div class="min-h-screen bg-gray-100 p-4 md:p-6" x-data="{ showRecipeModal: @entangle('showRecipeModal') }">
    {{-- Nagłówek --}}
    <div class="mb-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-4 lg:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">🧑‍🍳 Panel Piekarza</h1>
                    <div class="flex items-center space-x-4 text-lg">
                        <span class="text-gray-600">
                            {{ Carbon\Carbon::parse($selectedDate)->format('d.m.Y') }}
                            ({{ Carbon\Carbon::parse($selectedDate)->isToday() ? 'Dziś' : Carbon\Carbon::parse($selectedDate)->format('l') }})
                        </span>
                        @if($stats['total_products'] > 0)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                {{ $stats['total_products'] }} produktów
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Przycisk listy zakupów --}}
{{--                @if($stats['total_products'] > 0)--}}
{{--                    <div class="mt-4 lg:mt-0">--}}
{{--                        <button wire:click="showIngredientsSummary"--}}
{{--                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 active:bg-green-800 transition-colors touch-manipulation">--}}
{{--                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>--}}
{{--                            </svg>--}}
{{--                            📋 Lista zakupów na dziś--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                @endif--}}

                {{-- Statystyki --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $stats['total_quantity'] }}</div>
                        <div class="text-sm text-gray-500">Do wyprodukowania</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $stats['completed_quantity'] }}</div>
                        <div class="text-sm text-gray-500">Wyprodukowano</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $stats['in_progress'] }}</div>
                        <div class="text-sm text-gray-500">W produkcji</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-600">{{ $stats['waiting'] }}</div>
                        <div class="text-sm text-gray-500">Oczekuje</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Nawigacja dat --}}
    <div class="mb-6">
        <div class="bg-white rounded-xl shadow-lg p-4">
            <div class="flex space-x-2 overflow-x-auto pb-2">
                @foreach($availableDates as $dateInfo)
                    <button wire:click="changeDate('{{ $dateInfo['date'] }}')"
                            class="flex-shrink-0 px-4 py-3 rounded-lg text-center transition-all duration-200 {{ $selectedDate === $dateInfo['date'] ? 'bg-blue-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} {{ $dateInfo['is_past'] ? 'opacity-60' : '' }}">
                        <div class="font-semibold">{{ $dateInfo['label'] }}</div>
                        <div class="text-xs">{{ $dateInfo['day_name'] }}</div>
                        @if($dateInfo['orders_count'] > 0)
                            <div class="text-xs mt-1 px-2 py-0.5 rounded-full {{ $selectedDate === $dateInfo['date'] ? 'bg-blue-500' : 'bg-blue-100 text-blue-800' }}">
                                {{ $dateInfo['orders_count'] }}
                            </div>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    @include('livewire.baker.partials.product-cards')
    @include('livewire.baker.partials.recipe-modal')
    @include('livewire.baker.partials.ingredients-summary-modal')
    @include('livewire.baker.partials.step-ingredients-modal')
</div>

@script
<script>
    $wire.on('item-updated', () => {
        $wire.$refresh();
    });
</script>
@endscript
