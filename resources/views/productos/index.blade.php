<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Productos Agrícolas</h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <a href="{{ route('productos.create') }}" class="inline-block px-4 py-2 mb-4 text-white bg-green-600 rounded hover:bg-green-700">
                + Nuevo Producto
            </a>

            <x-success />
            
            <div class="p-6 bg-white rounded-lg shadow">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2">Nombre</th>
                            <th class="px-4 py-2">Estado</th>
                            <th class="px-4 py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productos as $producto)
                            <tr>
                                <td class="px-4 py-2 border">{{ $producto->nombre }}</td>
                                <td class="px-4 py-2 capitalize border">{{ $producto->estado }}</td>
                                <td class="px-4 py-2 space-x-2 border">
                                    <a href="{{ route('productos.show', $producto) }}" class="text-blue-600 hover:underline">Ver</a>
                                    <a href="{{ route('productos.edit', $producto) }}" class="text-yellow-600 hover:underline">Editar</a>
                                    <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar este producto?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center">Sin productos aún.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
