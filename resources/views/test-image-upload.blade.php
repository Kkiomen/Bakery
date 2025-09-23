<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Upload Zdjęć</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold mb-6">Test Upload Zdjęć Produktów</h1>

            <div class="bg-white shadow rounded-lg p-6">
                @if(auth()->check())
                    @livewire('products.product-form')
                @else
                    <div class="text-center">
                        <p class="text-gray-600 mb-4">Musisz być zalogowany aby testować upload zdjęć.</p>
                        <a href="{{ route('login') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Zaloguj się</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
