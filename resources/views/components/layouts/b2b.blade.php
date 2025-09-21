<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Portal B2B' }} - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Admin impersonation bar -->
    @if(session()->has('impersonating'))
        <div class="bg-yellow-500 text-black">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-2">
                    <div class="flex items-center space-x-3">
                        <span class="text-lg">⚠️</span>
                        <div class="text-sm">
                            <strong>Tryb testowy administratora</strong>
                            - testujesz jako: {{ Auth::guard('b2b')->user()->company_name }}
                        </div>
                    </div>
                    <a href="{{ route('admin.impersonate') }}"
                       class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm">
                        🔄 Wróć do panelu administratora
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Nawigacja -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('b2b.dashboard') }}" class="flex items-center space-x-2">
                        <span class="text-2xl">🍞</span>
                        <span class="font-bold text-xl text-gray-900">Portal B2B</span>
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    @auth('b2b')
                        <div class="flex items-center space-x-6">
                            <a href="{{ route('b2b.catalog') }}"
                               class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                                Katalog
                            </a>
                            <a href="{{ route('b2b.orders.index') }}"
                               class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                                Zamówienia
                            </a>
                            <a href="{{ route('b2b.recurring-orders') }}"
                               class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                                Zamówienia cykliczne
                            </a>
                            <a href="{{ route('b2b.notifications') }}"
                               class="relative text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                                Powiadomienia
                                @if(Auth::guard('b2b')->user()->unreadNotifications()->count() > 0)
                                    <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center">
                                        {{ Auth::guard('b2b')->user()->unreadNotifications()->count() }}
                                    </span>
                                @endif
                            </a>

                            <!-- Dropdown użytkownika -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                        class="flex items-center space-x-2 text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                                    <span>{{ Auth::guard('b2b')->user()->company_name }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <div x-show="open"
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <a href="{{ route('b2b.profile') }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Profil firmy
                                    </a>
                                    <form method="POST" action="{{ route('b2b.logout') }}">
                                        @csrf
                                        <button type="submit"
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Wyloguj
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Zawartość -->
    <main>
        {{ $slot }}
    </main>

    @livewireScripts

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
