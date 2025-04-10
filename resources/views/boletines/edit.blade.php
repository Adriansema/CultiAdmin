@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold">Editar Boletín</h2>
@endsection

@section('content')
    <div class="max-w-4xl py-6 mx-auto">
        <form action="{{ route('boletines.update', $boletin) }}" method="POST">
            @csrf
            @method('PUT')
            @include('boletines._form', ['boletin' => $boletin])
            <x-button class="mt-4">Actualizar</x-button>
        </form>
    </div>
@endsection
