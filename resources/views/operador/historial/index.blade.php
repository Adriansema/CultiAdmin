@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
    <h2 class="mb-4 text-xl font-semibold">Historial de Productos y Boletines</h2>

    {{-- Tabs --}}
    <div class="mb-4 border-b border-gray-200">
        <ul class="flex -mb-px text-sm font-medium text-center">
            <li>
                <a href="{{ route('operador.historial.index', array_merge(request()->except('tipo'), ['tipo' => 'producto'])) }}"
                   class="inline-block p-4 border-b-2 {{ $tipo === 'producto' ? 'border-blue-300 text-blue-700' : 'border-transparent' }}">
                    Productos
                </a>
            </li>
            <li>
                <a href="{{ route('operador.historial.index', array_merge(request()->except('tipo'), ['tipo' => 'boletin'])) }}"
                   class="inline-block p-4 border-b-2 {{ $tipo === 'boletin' ? 'border-blue-300 text-blue-700' : 'border-transparent' }}">
                    Boletines
                </a>
            </li>
        </ul>
    </div>

    {{-- Filtro --}}
    <form method="GET" action="{{ route('operador.historial.index') }}" class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-4">
        <input type="hidden" name="tipo" value="{{ $tipo }}">

        <select name="estado" class="form-select">
            <option value="">Todos</option>
            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
            <option value="aprobado" {{ request('estado') == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
            <option value="rechazado" {{ request('estado') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
        </select>

        <input type="date" name="desde" value="{{ request('desde') }}" class="form-input" placeholder="Desde">
        <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-input" placeholder="Hasta">
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>

    {{-- Resultados --}}
    @if($tipo === 'producto')
        @include('operador.partials.productos', ['productos' => $items])
    @elseif($tipo === 'boletin')
        @include('operador.partials.boletines', ['boletines' => $items])
    @endif
</div>
@endsection
