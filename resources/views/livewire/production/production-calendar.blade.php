<div class="space-y-6">
    {{-- Nagłówek --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kalendarz produkcji</h1>
            <p class="text-gray-600">Przegląd zleceń produkcji w kalendarzu</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('production.orders.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                Lista zleceń
            </a>
            <a href="{{ route('production.orders.create') }}"
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nowe zlecenie
            </a>
        </div>
    </div>

    {{-- Nawigacja kalendarza --}}
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <button wire:click="previousMonth"
                    class="p-2 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <div class="flex items-center space-x-4">
                <h2 class="text-xl font-semibold text-gray-900">{{ $monthNamePl }}</h2>
                <button wire:click="goToToday"
                        class="px-3 py-1 text-sm text-blue-600 hover:text-blue-800 border border-blue-300 rounded">
                    Dziś
                </button>
            </div>

            <button wire:click="nextMonth"
                    class="p-2 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>

        {{-- Nagłówki dni tygodnia --}}
        <div class="grid grid-cols-7 gap-1 mb-2">
            @foreach(['Pon', 'Wt', 'Śr', 'Czw', 'Pt', 'Sob', 'Nie'] as $day)
                <div class="p-2 text-center text-sm font-medium text-gray-500">
                    {{ $day }}
                </div>
            @endforeach
        </div>

        {{-- Kalendarz --}}
        <div class="grid grid-cols-7 gap-1">
            @foreach($calendar as $week)
                @foreach($week as $day)
                    <div class="min-h-[120px] border border-gray-200 p-1 {{ $day['isCurrentMonth'] ? 'bg-white' : 'bg-gray-50' }} {{ $day['isToday'] ? 'ring-2 ring-blue-500' : '' }}">
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-sm {{ $day['isCurrentMonth'] ? 'text-gray-900' : 'text-gray-400' }} {{ $day['isToday'] ? 'font-bold text-blue-600' : '' }}">
                                {{ $day['date']->day }}
                            </span>

                            @if($day['orderCount'] > 0)
                                <button wire:click="showOrdersForDate('{{ $day['date']->format('Y-m-d') }}')"
                                        class="text-xs px-1.5 py-0.5 rounded {{ $day['hasOverdue'] ? 'bg-red-100 text-red-800' : ($day['hasUrgent'] ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $day['orderCount'] }}
                                </button>
                            @endif
                        </div>

                        {{-- Podgląd zleceń --}}
                        @if($day['orderCount'] > 0)
                            <div class="space-y-1">
                                @foreach($day['orders']->take(3) as $order)
                                    <div class="text-xs p-1 rounded {{ $order->isOverdue() ? 'bg-red-100 text-red-800' : ($order->priorytet === 'pilny' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-700') }} truncate"
                                         title="{{ $order->nazwa }}">
                                        {{ Str::limit($order->nazwa, 20) }}
                                    </div>
                                @endforeach

                                @if($day['orderCount'] > 3)
                                    <div class="text-xs text-gray-500 text-center">
                                        +{{ $day['orderCount'] - 3 }} więcej
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            @endforeach
        </div>

        {{-- Legenda --}}
        <div class="mt-4 flex items-center space-x-6 text-sm">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-blue-100 rounded"></div>
                <span class="text-gray-600">Normalne zlecenia</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-orange-100 rounded"></div>
                <span class="text-gray-600">Pilne zlecenia</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-red-100 rounded"></div>
                <span class="text-gray-600">Opóźnione zlecenia</span>
            </div>
        </div>
    </div>

    {{-- Modal ze zleceniami na wybrany dzień --}}
    @if($showOrdersModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg max-w-4xl w-full mx-4 max-h-[80vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">
                            Zlecenia na {{ Carbon\Carbon::parse($selectedDate)->format('d.m.Y') }}
                        </h3>
                        <button wire:click="closeModal"
                                class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6 overflow-y-auto max-h-[60vh]">
                    @if(count($ordersForDate) > 0)
                        <div class="space-y-4">
                            @foreach($ordersForDate as $order)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <h4 class="font-medium text-gray-900">{{ $order->nazwa }}</h4>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                                                    {{ $order->status_label }}
                                                </span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $order->priorytet_color }}-100 text-{{ $order->priorytet_color }}-800">
                                                    {{ $order->priorytet_label }}
                                                </span>
                                            </div>

                                            <div class="mt-1 text-sm text-gray-500">
                                                <span>{{ $order->numer_zlecenia }}</span>
                                                <span class="mx-2">•</span>
                                                <span>{{ $order->user->name }}</span>
                                                @if($order->klient)
                                                    <span class="mx-2">•</span>
                                                    <span>{{ $order->klient }}</span>
                                                @endif
                                                <span class="mx-2">•</span>
                                                <span>{{ $order->items->count() }} pozycji</span>
                                            </div>

                                            @if($order->status === 'w_produkcji')
                                                <div class="mt-2">
                                                    <div class="flex items-center space-x-2">
                                                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                            <div class="bg-blue-600 h-2 rounded-full"
                                                                 style="width: {{ $order->getProgressPercentage() }}%"></div>
                                                        </div>
                                                        <span class="text-xs text-gray-500">{{ $order->getProgressPercentage() }}%</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('production.orders.show', $order) }}"
                                               class="text-blue-600 hover:text-blue-800 text-sm">
                                                Zobacz
                                            </a>

                                            <a href="{{ route('production.orders.edit', $order) }}"
                                               class="text-gray-600 hover:text-gray-800 text-sm">
                                                Edytuj
                                            </a>

                                            <button wire:click="duplicateOrder({{ $order->id }})"
                                                    class="text-green-600 hover:text-green-800 text-sm">
                                                Duplikuj
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Brak zleceń</h3>
                            <p class="mt-1 text-sm text-gray-500">Na ten dzień nie ma zaplanowanych zleceń produkcji.</p>
                            <div class="mt-6">
                                <a href="{{ route('production.orders.create') }}?date={{ $selectedDate }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Dodaj zlecenie
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
