<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 antialiased dark:bg-gradient-to-br dark:from-amber-950 dark:via-orange-950 dark:to-yellow-950">
        <!-- Tło z delikatnym wzorem piekarni -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml;utf8,<svg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;><g fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;><g fill=&quot;%23d97706&quot; fill-opacity=&quot;0.1&quot;><circle cx=&quot;30&quot; cy=&quot;30&quot; r=&quot;4&quot;/></g></g></svg>'); background-size: 60px 60px;"></div>
        </div>

        <div class="relative flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-md flex-col gap-2 bg-white/90 dark:bg-gray-900/90 backdrop-blur-sm rounded-2xl shadow-2xl border border-amber-200/50 dark:border-amber-800/50 p-8">
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>

                <!-- Stopka z informacjami o systemie -->
                <div class="mt-8 pt-6 border-t border-amber-200/50 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        © 2024 System Zarządzania Piekarnią
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        Wersja 1.0 • Bezpieczne logowanie
                    </p>
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
