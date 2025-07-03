<div class="overflow-x-auto rounded-2xl w-full">
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
            @if ($productos->total() === 0)
                <tr>
                    {{-- Ajustado el colspan a 9 para cubrir todas las columnas --}}
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                        @if (request()->has('q') && !empty(request()->get('q')))
                            No se encontraron productos que coincidan con
                            "{{ htmlspecialchars(request()->get('q')) }}".
                        @else
                            No hay productos registrados.
                        @endif
                    </td>
                </tr>
            @else
                @forelse($productos as $producto)
                    <tr class="bg-white hover:bg-gray-300">
                        <td class="px-4 py-2 flex items-center">
                            <span>{{ $producto->tipo }}</span>
                        </td>

                        <td class="px-4 py-2 text-gray-600 break-words whitespace-normal align-top">
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
                            @can('crear producto')
                                <a href="{{ route('productos.show', $producto) }}"
                                    class="px-3 py-2 text-sm text-center text-white bg-green-600 rounded hover:bg-green-700">
                                    Ver
                                </a>
                            @endcan

                            @can('editar producto')
                                <a href="{{ route('productos.edit', $producto) }}"
                                    class="px-3 py-2 text-sm text-center text-white bg-yellow-600 rounded hover:bg-yellow-700">
                                    Editar
                                </a>
                            @endcan

                            @can('eliminar producto')
                                <button type="button" onclick="mostrarModal('producto', '{{ $producto->id }}')"
                                    class="px-3 py-2 text-sm text-center text-white bg-red-600 rounded hover:bg-red-700">
                                    Eliminar
                                </button>
                            @endcan

                            {{-- Botones de Validar y Rechazar, visibles solo si el estado es 'pendiente' --}}
                            @if ($producto->estado === 'pendiente')
                                @can('validar producto')
                                    <button type="button" onclick="mostrarModal('validar-producto', '{{ $producto->id }}')"
                                        class="px-3 py-2 text-sm text-center text-white bg-blue-600 rounded hover:bg-blue-700">
                                        Validar
                                    </button>
                                    @include('pendientes.partials.modal-producto-validar')
                                @endcan

                                @can('validar producto')
                                    <button type="button"
                                        onclick="mostrarModal('rechazar-producto', '{{ $producto->id }}')"
                                        class="px-3 py-2 text-sm text-center text-white bg-orange-600 rounded hover:bg-orange-700">
                                        Rechazar
                                    </button>
                                    @include('pendientes.partials.modal-producto-rechazar')
                                @endcan
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">No hay productos registrados.
                        </td>
                    </tr>
                @endforelse
            @endif
        </tbody>
    </table>
</div>
