@extends('layouts.app')

@section('content')
<div class="py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <h2 class="mb-6 text-2xl font-bold">Historial de Productos y Boletines</h2>

    {{-- Tabs --}}
    <div class="mb-4 border-b border-gray-200">
        <ul class="flex space-x-4 text-sm font-medium text-center text-gray-500">
            <li>
                <a href="{{ route('historial.index', ['tipo' => 'producto']) }}"
                   class="inline-block px-4 py-2 rounded-t-lg {{ $tipo === 'producto' ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                    Productos
                </a>
            </li>
            <li>
                <a href="{{ route('historial.index', ['tipo' => 'boletin']) }}"
                   class="inline-block px-4 py-2 rounded-t-lg {{ $tipo === 'boletin' ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                    Boletines
                </a>
            </li>
        </ul>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('historial.index') }}" class="flex flex-wrap items-end gap-4 mb-6">
        <input type="hidden" name="tipo" value="{{ $tipo }}">

        <div>
            <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
            <select name="estado" id="estado" class="block w-48 mt-1 border-gray-300 rounded-md shadow-sm">
                <option value="">Todos</option>
                <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="aprobado" {{ request('estado') == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                <option value="rechazado" {{ request('estado') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
            </select>
        </div>

        <div>
            <label for="desde" class="block text-sm font-medium text-gray-700">Desde</label>
            <input type="date" name="desde" id="desde" value="{{ request('desde') }}" class="block w-48 mt-1 border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <label for="hasta" class="block text-sm font-medium text-gray-700">Hasta</label>
            <input type="date" name="hasta" id="hasta" value="{{ request('hasta') }}" class="block w-48 mt-1 border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <button type="submit" class="px-4 py-2 mt-5 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                Filtrar
            </button>
        </div>
    </form>

    {{-- Tabla --}}
    @if($tipo === 'producto')
        @include('historial.partials.productos', ['productos' => $items])
    @elseif($tipo === 'boletin')
        @include('historial.partials.boletines', ['boletines' => $items])
    @endif
</div>
@endsection
