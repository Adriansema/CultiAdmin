@extends('layouts.app')

@section('title', 'Productos Agrícolas')

@section('content')
<div class="container max-w-6xl py-6 mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800 flex items-center space-x-2">
            <img src="{{ asset('images/reverse.svg') }}" alt="icono" class="w-5 h-5">
            <span>Productos Agrícolas</span>
        </h1>
        <a href="{{ route('productos.create') }}"
            class="px-4 py-2 text-white bg-green-600 rounded-3xl hover:bg-green-700">
            + Nuevo Producto
        </a>
    </div>

    {!! Breadcrumbs::render('productos.index') !!}

    @if (session('success'))
        <div class="p-3 mb-4 text-green-700 bg-green-100 rounded shadow">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden bg-white rounded shadow">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Tipo</th>
                    <th class="px-4 py-2">Estado</th>
                    <th class="px-4 py-2">Última modificación</th>
                    <th class="px-4 py-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $producto)
                    <tr class="border-t bg-blue-200 hover:bg-gray-100">
                        <td class="px-4 py-2 capitalize">{{ $producto->tipo }}</td>
                        <td class="px-4 py-2">
                            <span class="inline-block px-3 py-1 text-sm font-semibold text-white rounded
                                {{ $producto->estado === 'aprobado' ? 'bg-green-600' : ($producto->estado === 'pendiente' ? 'bg-yellow-500' : 'bg-red-600') }}">
                                {{ ucfirst($producto->estado) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-gray-600">{{ $producto->updated_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2 space-x-2">
                            <a href="{{ route('productos.show', $producto) }}" class="text-blue-600 hover:underline">Ver</a>
                            <a href="{{ route('productos.edit', $producto) }}" class="text-yellow-600 hover:underline">Editar</a>
                            <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="inline-block"
                                onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">No hay productos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
