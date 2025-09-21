<div class="min-h-screen bg-gray-50">
    <!-- Nagłówek -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🔔 Powiadomienia</h1>
                    <p class="text-gray-600">{{ Auth::guard('b2b')->user()->company_name }}</p>
                </div>
                <div class="flex space-x-4">
                    @if($stats['unread'] > 0)
                        <button wire:click="markAllAsRead"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            ✅ Oznacz wszystkie jako przeczytane
                        </button>
                    @endif
                    <a href="{{ route('b2b.dashboard') }}"
                       class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        ← Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statystyki -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📊</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Wszystkie</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">🔴</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Nieprzeczytane</dt>
                                <dd class="text-lg font-medium text-red-600">{{ $stats['unread'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">✅</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Przeczytane</dt>
                                <dd class="text-lg font-medium text-green-600">{{ $stats['read'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📅</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Dzisiaj</dt>
                                <dd class="text-lg font-medium text-blue-600">{{ $stats['today'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtry i akcje -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                    <!-- Filtry -->
                    <div class="flex space-x-4">
                        <button wire:click="$set('filter', 'all')"
                                class="px-4 py-2 rounded-md text-sm font-medium transition-colors
                                    {{ $filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                            Wszystkie ({{ $stats['total'] }})
                        </button>
                        <button wire:click="$set('filter', 'unread')"
                                class="px-4 py-2 rounded-md text-sm font-medium transition-colors
                                    {{ $filter === 'unread' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                            Nieprzeczytane ({{ $stats['unread'] }})
                        </button>
                        <button wire:click="$set('filter', 'read')"
                                class="px-4 py-2 rounded-md text-sm font-medium transition-colors
                                    {{ $filter === 'read' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                            Przeczytane ({{ $stats['read'] }})
                        </button>
                    </div>

                    <!-- Akcje grupowe -->
                    @if($notifications->count() > 0)
                        <div class="flex space-x-2">
                            @if(count($selectedNotifications) > 0)
                                <button wire:click="markSelectedAsRead"
                                        class="bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">
                                    ✅ Oznacz jako przeczytane ({{ count($selectedNotifications) }})
                                </button>
                                <button wire:click="deleteSelected"
                                        onclick="return confirm('Czy na pewno chcesz usunąć wybrane powiadomienia?')"
                                        class="bg-red-600 text-white px-3 py-2 rounded text-sm hover:bg-red-700">
                                    🗑️ Usuń wybrane ({{ count($selectedNotifications) }})
                                </button>
                            @endif
                            <button wire:click="deleteAll"
                                    onclick="return confirm('Czy na pewno chcesz usunąć wszystkie {{ $filter === 'all' ? '' : $filter }} powiadomienia?')"
                                    class="bg-gray-600 text-white px-3 py-2 rounded text-sm hover:bg-gray-700">
                                🗑️ Usuń wszystkie
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Lista powiadomień -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            @if($notifications->count() > 0)
                <div class="px-6 py-4 border-b border-gray-200">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model.live="selectAll"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Zaznacz wszystkie</span>
                    </label>
                </div>
            @endif

            @forelse($notifications as $notification)
                <div class="border-b border-gray-200 last:border-b-0
                    {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}">
                    <div class="px-6 py-4">
                        <div class="flex items-start space-x-4">
                            <!-- Checkbox -->
                            <div class="flex-shrink-0 pt-1">
                                <input type="checkbox"
                                       wire:model.live="selectedNotifications"
                                       value="{{ $notification->id }}"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>

                            <!-- Ikona i status -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    {{ $notification->read_at ? 'bg-gray-100' : 'bg-blue-100' }}">
                                    <span class="text-xl">{{ $notification->data['icon'] ?? '🔔' }}</span>
                                </div>
                            </div>

                            <!-- Treść powiadomienia -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900 truncate">
                                        {{ $notification->data['title'] ?? 'Powiadomienie' }}
                                    </h3>
                                    <div class="flex items-center space-x-2">
                                        @if($notification->data['priority'] ?? 'normal' === 'high')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                Ważne
                                            </span>
                                        @endif
                                        @if(!$notification->read_at)
                                            <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                                        @endif
                                    </div>
                                </div>

                                <p class="text-gray-700 mb-3">{{ $notification->data['message'] ?? '' }}</p>

                                <!-- Szczegóły -->
                                @if(isset($notification->data['order_number']))
                                    <div class="bg-gray-50 p-3 rounded-lg mb-3 text-sm">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                            <div>
                                                <span class="font-medium">Zamówienie:</span>
                                                <span class="ml-1">{{ $notification->data['order_number'] }}</span>
                                            </div>
                                            @if(isset($notification->data['total_amount']))
                                                <div>
                                                    <span class="font-medium">Wartość:</span>
                                                    <span class="ml-1">{{ number_format($notification->data['total_amount'], 2) }} zł</span>
                                                </div>
                                            @endif
                                            @if(isset($notification->data['delivery_date']))
                                                <div>
                                                    <span class="font-medium">Dostawa:</span>
                                                    <span class="ml-1">{{ \Carbon\Carbon::parse($notification->data['delivery_date'])->format('d.m.Y') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Czas -->
                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                                    <span>{{ $notification->created_at->format('d.m.Y H:i') }}</span>
                                </div>
                            </div>

                            <!-- Akcje -->
                            <div class="flex-shrink-0 flex flex-col space-y-2">
                                @if(isset($notification->data['action_url']))
                                    <a href="{{ $notification->data['action_url'] }}"
                                       class="bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700 text-center">
                                        👁️ Zobacz
                                    </a>
                                @endif

                                @if(!$notification->read_at)
                                    <button wire:click="markAsRead('{{ $notification->id }}')"
                                            class="bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700">
                                        ✅ Przeczytane
                                    </button>
                                @else
                                    <button wire:click="markAsUnread('{{ $notification->id }}')"
                                            class="bg-yellow-600 text-white px-3 py-2 rounded text-sm hover:bg-yellow-700">
                                        🔄 Nieprzeczytane
                                    </button>
                                @endif

                                <button wire:click="deleteNotification('{{ $notification->id }}')"
                                        onclick="return confirm('Czy na pewno chcesz usunąć to powiadomienie?')"
                                        class="bg-red-600 text-white px-3 py-2 rounded text-sm hover:bg-red-700">
                                    🗑️
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <span class="text-gray-400 text-6xl">🔔</span>
                    <h3 class="text-lg font-medium text-gray-900 mt-4">
                        @if($filter === 'unread')
                            Brak nieprzeczytanych powiadomień
                        @elseif($filter === 'read')
                            Brak przeczytanych powiadomień
                        @else
                            Brak powiadomień
                        @endif
                    </h3>
                    <p class="text-gray-600">
                        @if($filter === 'unread')
                            Wszystkie powiadomienia zostały przeczytane.
                        @elseif($filter === 'read')
                            Nie ma jeszcze przeczytanych powiadomień.
                        @else
                            Nie masz jeszcze żadnych powiadomień.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Paginacja -->
        @if($notifications->hasPages())
            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
