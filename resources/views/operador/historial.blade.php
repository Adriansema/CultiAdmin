@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold">Historial de Validaciones</h2>
@endsection

@section('content')
<div class="py-6 mx-auto max-w-7xl">

    {{-- Filtros --}}
    <div class="p-6 mb-6 bg-white rounded shadow">
        <form method="GET" action="{{ route('operador.historial') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <x-label for="tipo" value="Tipo" />
                <select name="tipo" id="tipo" class="w-full border-gray-300 rounded">
                    <option value="">Todos</option>
                    <option value="producto" {{ request('tipo') == 'producto' ? 'selected' : '' }}>Productos</option>
                    <option value="boletin" {{ request('tipo') == 'boletin' ? 'selected' : '' }}>Boletines</option>
                </select>
            </div>
            <div>
                <x-label for="estado" value="Estado" />
                <select name="estado" id="estado" class="w-full border-gray-300 rounded">
                    <option value="">Todos</option>
                    <option value="aprobado" {{ request('estado') == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                    <option value="rechazado" {{ request('estado') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                </select>
            </div>
            <div>
                <x-label for="fecha_inicio" value="Desde" />
                <x-input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" />
            </div>
            <div>
                <x-label for="fecha_fin" value="Hasta" />
                <x-input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" />
            </div>
            <div class="md:col-span-4">
                <x-button class="w-full">Filtrar</x-button>
            </div>
        </form>
    </div>

    {{-- Productos Historial --}}
    @if ($historialProductos->count())
        <div class="mb-10">
            <h3 class="mb-3 text-lg font-bold">Productos</h3>
            <div class="p-6 bg-white rounded shadow">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2">Nombre</th>
                            <th class="px-4 py-2">Estado</th>
                            <th class="px-4 py-2">Fecha</th>
                            <th class="px-4 py-2">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historialProductos as $producto)
                            <tr>
                                <td class="px-4 py-2 border">{{ $producto->nombre }}</td>
                                <td class="px-4 py-2 border">
                                    <span class="px-2 py-1 text-xs font-semibold text-white rounded
                                        {{ $producto->estado == 'aprobado' ? 'bg-green-600' : 'bg-red-600' }}">
                                        {{ ucfirst($producto->estado) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 border">{{ $producto->updated_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-2 border">{{ $producto->observaciones ?? '—' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                </table>
                <div class="mt-4">
                    {{ $historialProductos->withQueryString()->links() }}
                </div>
            </div>
        </div>
    @endif

    {{-- Boletines Historial --}}
    @if ($historialBoletines->count())
        <div>
            <h3 class="mb-3 text-lg font-bold">Boletines</h3>
            <div class="p-6 bg-white rounded shadow">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2">Asunto</th>
                            <th class="px-4 py-2">Estado</th>
                            <th class="px-4 py-2">Fecha</th>
                            <th class="px-4 py-2">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historialBoletines as $boletin)
                            <tr>
                                <td class="px-4 py-2 border">{{ $boletin->asunto }}</td>
                                <td class="px-4 py-2 border">
                                    <span class="px-2 py-1 text-xs font-semibold text-white rounded
                                        {{ $boletin->estado == 'aprobado' ? 'bg-green-600' : 'bg-red-600' }}">
                                        {{ ucfirst($boletin->estado) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 border">{{ $boletin->updated_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-2 border">{{ $boletin->observaciones ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $historialBoletines->withQueryString()->links() }}
                </div>
            </div>
        </div>
    @endif

    @if ($historialProductos->isEmpty() && $historialBoletines->isEmpty())
        <div class="p-6 mt-10 text-center text-gray-500 bg-white rounded shadow">
            No hay registros que coincidan con los filtros aplicados.
        </div>
    @endif

</div>
@endsection
