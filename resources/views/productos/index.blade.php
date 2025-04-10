<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Mis Productos</h2></x-slot>

<!-- actualizacion 09/04/2025 -->

    <div class="py-6 mx-auto max-w-7xl">
        <a href="{{ route('productos.create') }}" class="inline-block px-4 py-2 mb-4 text-white bg-green-600 rounded hover:bg-green-700">+ Nuevo Producto</a>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


        <div class="p-6 bg-white rounded shadow">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2">Nombre</th>
                        <th class="px-4 py-2">Estado</th>
                        <th class="px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                        <tr>
                            <td class="px-4 py-2 border">{{ $producto->nombre }}</td>
                            <td class="px-4 py-2 border">
                            @php
                                $estadoColor = match($producto->estado) {
                                    'pendiente' => 'bg-yellow-500',
                                    'validado' => 'bg-green-600',
                                    'rechazado' => 'bg-red-600',
                                    default => 'bg-gray-400',
                                };
                            @endphp

                            @role('administrador')
                                <p class="text-green-500">Sos administrador </p>
                            @else
                                <p class="text-red-500">NO sos administrador </p>
                            @endrole


                            <span class="px-2 py-1 text-white rounded {{ $estadoColor }}">
                                {{ ucfirst($producto->estado) }}
                            </span>
                            </td>
                            <td class="px-4 py-2 space-x-2 border">
                                <a href="{{ route('productos.show', $producto) }}" class="text-blue-600 hover:underline">Ver</a>
                                <a href="{{ route('productos.edit', $producto) }}" class="text-yellow-600 hover:underline">Editar</a>
                                <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar este producto?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-4 text-center">No tienes productos aún.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
