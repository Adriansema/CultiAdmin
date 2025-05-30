<div class="overflow-hidden rounded-2xl shadow-sm">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-[var(--color-tabla)]">
            <tr>
                <th class="px-4 py-2">Tipo</th>
                <th class="px-4 py-2">Fecha</th>
                <th class="px-4 py-2">Estado</th>
                <th class="px-4 py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productos as $producto)
                <tr class="bg-white hover:bg-gray-300">
                    <td class="px-6 py-4 flex items-center group relative">
                        <span>{{ $producto->tipo }}</span>
                        <a href="{{ route('productos.show', $producto) }}">
                            <img src="{{ asset('images/ojo-open.svg') }}"
                                class="w-6 h-4 absolute left-[calc(40%+4px)] top-1/2 -translate-y-1/2 
                                        opacity-0 group-hover:opacity-30 
                                        transition-opacity duration-300 pointer-events-none group-hover:pointer-events-auto"
                                alt="editar">
                        </a>
                    </td>

                    <td class="max-w-xs px-4 py-2 text-gray-600 break-words whitespace-normal align-top">
                        {{ $producto->created_at->locale('es')->translatedFormat('d \d\e F \d\e\l Y h:i a') }}
                        <span class="block text-xs text-gray-500">
                            ({{ $producto->created_at->diffForHumans() }})
                        </span>
                    </td>

                    <td class="px-4 py-2">
                        <span
                            class="inline-block px-3 py-1 text-sm font-semibold text-white rounded
                                {{ $producto->estado === 'aprobado' ? 'bg-green-600' : ($producto->estado === 'pendiente' ? 'bg-yellow-500' : 'bg-red-600') }}">
                            {{ ucfirst($producto->estado) }}
                        </span>
                    </td>

                    <td class="px-4 py-2 space-x-2">
                        {{-- <a href="{{ route('productos.show', $producto) }}" class="text-blue-600 hover:underline">Ver</a> --}}
                        <a href="{{ route('productos.edit', $producto) }}"
                            class="text-yellow-600 hover:underline">Editar</a>

                        <!-- Botón que abre el modal -->
                        <button type="button" onclick="mostrarModal('producto', '{{ $producto->id }}')"
                            class="text-red-600 hover:underline">
                            Eliminar
                        </button>

                        <!-- Modal (se mantiene oculto por defecto) -->
                        <div id="modal-producto-{{ $producto->id }}" class="hidden">
                            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                                <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
                                    <h3 class="mb-4 text-lg font-bold text-gray-800">
                                        ¿Estás seguro de eliminar este producto?
                                    </h3>
                                    <p class="mb-4 text-gray-600">
                                        Esta acción no se puede deshacer. El producto será eliminado permanentemente
                                        del sistema.
                                    </p>
                                    <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <div class="flex justify-end mt-4 space-x-2">
                                            <button type="button"
                                                onclick="ocultarModal('producto', '{{ $producto->id }}')"
                                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                                                Cancelar
                                            </button>
                                            <x-button class="bg-red-600 hover:bg-red-700">
                                                Eliminar
                                            </x-button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">No hay productos registrados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
