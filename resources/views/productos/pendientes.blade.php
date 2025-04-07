<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Validar Productos</h2>
    </x-slot>

    <div class="py-6 mx-auto max-w-7xl">
        <x-success />
        <x-error />

        <div class="p-6 bg-white rounded shadow">
            <table class="w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Descripción</th>
                        <th class="px-4 py-2 text-left">Imagen</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $producto->nombre }}</td>
                            <td class="px-4 py-2">{{ Str::limit($producto->descripcion, 40) }}</td>
                            <td class="px-4 py-2">
                                @if($producto->imagen)
                                    <img src="{{ asset('storage/' . $producto->imagen) }}" class="h-16 rounded">
                                @else
                                    <span class="italic text-gray-400">Sin imagen</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex flex-col space-y-2">
                                    <!-- Aprobar -->
                                    <form action="{{ route('productos.validar', $producto->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full px-3 py-1 text-white bg-green-600 rounded hover:bg-green-700">
                                            Aprobar
                                        </button>
                                    </form>

                                    <!-- Rechazar -->
                                    <button onclick="document.getElementById('rechazar-form-{{ $producto->id }}').classList.toggle('hidden')" 
                                            class="w-full px-3 py-1 text-white bg-red-600 rounded hover:bg-red-700">
                                        Rechazar
                                    </button>

                                    <!-- Formulario oculto de rechazo -->
                                    <form id="rechazar-form-{{ $producto->id }}" class="hidden mt-2" method="POST" 
                                          action="{{ route('productos.rechazar', $producto->id) }}" onsubmit="return confirm('¿Estás seguro de rechazar este producto?');">
                                        @csrf
                                        <textarea name="observaciones" class="w-full p-2 mt-1 border rounded" placeholder="Motivo del rechazo..." required>{{ old('observaciones') }}</textarea>
                                        <button type="submit" class="w-full px-3 py-1 mt-2 text-white bg-red-700 rounded hover:bg-red-800">
                                            Confirmar Rechazo
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-gray-500">No hay productos pendientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
