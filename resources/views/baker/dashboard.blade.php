<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Panel Piekarza - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Optymalizacja dla tabletów */
        body {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }

        /* Większe przyciski dla dotykowych urządzeń */
        .touch-button {
            min-height: 48px;
            min-width: 48px;
        }

        /* Animacje dla lepszego UX */
        .card-hover {
            transition: all 0.2s ease-in-out;
        }

        .card-hover:active {
            transform: scale(0.98);
        }

        /* Ukryj scrollbary ale zachowaj funkcjonalność */
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-100 overflow-x-hidden">
    <livewire:baker.baker-dashboard />

    @livewireScripts

    <script>
        // Zapobiegaj zoomowaniu na podwójne kliknięcie
        document.addEventListener('dblclick', function(e) {
            e.preventDefault();
        });

        // Obsługa gestów dotykowych
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function (event) {
            var now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);

        // Auto-refresh co 30 sekund
        setInterval(function() {
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('refresh');
            }
        }, 30000);
    </script>
</body>
</html>

