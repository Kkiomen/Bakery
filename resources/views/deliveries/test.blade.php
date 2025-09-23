@extends('components.layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-4">Test Dostawy</h1>
                <p>Strona testowa dla systemu dostaw</p>

                <div class="mt-6">
                    <h2 class="text-lg font-semibold mb-2">Statystyki dostaw:</h2>
                    <ul class="list-disc list-inside">
                        <li>Wszystkie dostawy: {{ App\Models\Delivery::count() }}</li>
                        <li>Dostawy oczekujące: {{ App\Models\Delivery::where('status', 'oczekujaca')->count() }}</li>
                        <li>Dostawy w drodze: {{ App\Models\Delivery::where('status', 'w_drodze')->count() }}</li>
                        <li>Dostawy dostarczone: {{ App\Models\Delivery::where('status', 'dostarczona')->count() }}</li>
                    </ul>
                </div>

                <div class="mt-6">
                    <a href="{{ route('deliveries.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Powrót do zarządzania dostawami
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

