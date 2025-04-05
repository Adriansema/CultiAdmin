@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Productos Agrícolas</h1>
    <h5 class="mb-4 text-muted">Información de producto agrícola</h5>

    @role('administrador')
        <a href="{{ route('productos-agricolas.create') }}" class="mb-3 btn btn-primary">+ Agregar Producto Agrícola</a>
    @endrole

    <table class="min-w-full overflow-hidden bg-white rounded-lg shadow">
    <thead class="text-white bg-green-600">
        <tr>
            <th class="px-6 py-3 text-sm font-semibold text-left">Nombre</th>
            <th class="px-6 py-3 text-sm font-semibold text-left">Tipo</th>
            <th class="px-6 py-3 text-sm font-semibold text-left">Suelo</th>
            <th class="px-6 py-3 text-sm font-semibold text-left">Acciones</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
        @foreach ($productos as $producto)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-900">{{ $producto->nombre }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $producto->tipo }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $producto->suelo }}</td>
                <td class="flex flex-wrap gap-2 px-6 py-4">

                    @can('editar cultivos')
                        <a href="{{ route('productos-agricolas.edit', $producto->id) }}"
                           class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition">
                            Editar
                        </a>
                    @endcan

                    @can('eliminar cultivos')
                        <form action="{{ route('productos-agricolas.destroy', $producto->id) }}" method="POST"
                              onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition">
                                Eliminar
                            </button>
                        </form>
                    @endcan

                    @role('operador')
                        <form action="{{ route('productos.validar', $producto->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition">
                                Validar
                            </button>
                        </form>

                        <form action="{{ route('productos.rechazar', $producto->id) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            <input type="text" name="observacion" placeholder="Motivo del rechazo"
                                   class="w-48 px-2 py-1 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-yellow-500"
                                   required>
                                   <button
                                        @click="open = true; selectedProductId = {{ $producto->id }}"
                                         class="text-red-600 hover:underline"
                                type="submit">
                                Rechazar
                            </button>
                        </form>
                    @endrole

                </td>
            </tr>
        @endforeach
    </tbody>
</table>
