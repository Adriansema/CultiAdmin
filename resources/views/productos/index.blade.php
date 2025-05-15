@extends('layouts.app')

@section('title', 'Productos')

@section('content')
    <div class="container max-w-6xl py-6 mx-auto">
        <div class="flex justify-between mb-4">
            <a href="{{ route('productos.create') }}" class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                Nuevo Producto
            </a>
        </div>
        <div class="inline-block px-8 py-10">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
                <h1 class="text-3xl whitespace-nowrap font-bold">Productos</h1>
            </div>
            {!! Breadcrumbs::render('productos.index') !!}
        </div>

        @if (session('success'))
            <div class="p-3 mb-4 text-green-700 bg-green-100 rounded shadow">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden bg-white rounded shadow">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">Nombre</th>
                        <th class="px-4 py-2">Estado</th>
                        <th class="px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $producto->nombre }}</td>
                            <td class="px-4 py-2 capitalize">{{ $producto->estado }}</td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="{{ route('productos.show', $producto) }}"
                                    class="text-blue-600 hover:underline">Ver</a>
                                <a href="{{ route('productos.edit', $producto) }}"
                                    class="text-yellow-600 hover:underline">Editar</a>
                                <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-gray-500">No hay productos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
