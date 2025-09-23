@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Edytuj Dostawę') }} - {{ $delivery->numer_dostawy }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                @livewire('deliveries.create-delivery', ['delivery' => $delivery])
            </div>
        </div>
    </div>
</div>
@endsection

