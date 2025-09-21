<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie Dostawami - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="p-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <strong>Debug:</strong> Sprawdzanie czy Livewire działa...
        </div>
    </div>

    <livewire:deliveries.simple-delivery-management />

    @livewireScripts

    <script>
        console.log('Livewire loaded:', typeof Livewire !== 'undefined');
        console.log('Alpine loaded:', typeof Alpine !== 'undefined');

        // Test czy Livewire działa
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded');
            setTimeout(() => {
                console.log('Livewire after timeout:', typeof Livewire !== 'undefined');
            }, 1000);
        });
    </script>
</body>
</html>
