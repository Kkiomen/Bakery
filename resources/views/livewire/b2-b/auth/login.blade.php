<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-auto flex justify-center">
                <span class="text-4xl">🍞</span>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Portal B2B - Logowanie
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Zaloguj się do swojego konta firmowego
            </p>
        </div>

        <!-- Komunikaty sesji -->
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form wire:submit="login" class="mt-8 space-y-6">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Email</label>
                    <input wire:model="email"
                           id="email"
                           name="email"
                           type="email"
                           autocomplete="email"
                           required
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('email') border-red-500 @enderror"
                           placeholder="Adres email">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="sr-only">Hasło</label>
                    <input wire:model="password"
                           id="password"
                           name="password"
                           type="password"
                           autocomplete="current-password"
                           required
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('password') border-red-500 @enderror"
                           placeholder="Hasło">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input wire:model="remember"
                           id="remember"
                           name="remember"
                           type="checkbox"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Zapamiętaj mnie
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                        Zapomniałeś hasła?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-blue-500 group-hover:text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Zaloguj się
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Nie masz konta?
                    <a href="mailto:kontakt@piekarnia.pl" class="font-medium text-blue-600 hover:text-blue-500">
                        Skontaktuj się z nami
                    </a>
                </p>
            </div>
        </form>

        <!-- Przykładowe konta do testów -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-blue-900 mb-2">Konta testowe:</h3>
            <div class="text-xs text-blue-800 space-y-1">
                <p><strong>Hotel:</strong> zamowienia@grandpalace.pl / password123</p>
                <p><strong>Restauracja:</strong> zamowienia@smakowitka.pl / password123</p>
                <p><strong>Kawiarnia:</strong> zamowienia@aromat.pl / password123</p>
                <p><strong>Sklep:</strong> zamowienia@pieczywo-sklep.pl / password123</p>
                <p><strong>Catering:</strong> zamowienia@catering-premium.pl / password123</p>
            </div>
        </div>
    </div>
</div>
