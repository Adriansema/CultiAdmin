<div class="overflow-hidden bg-white rounded shadow">
    <table class="min-w-full text-sm text-left bg-red-200 hover:bg-red-300">
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
                        <a href="{{ route('operador.productos.show', $producto->id) }}"
                            class="text-blue-600 hover:underline">Ver</a>

                        <button type="button" onclick="mostrarModal('validar-producto', '{{ $producto->id }}')"
                            class="text-green-600 hover:underline">
                            Validar
                        </button>
                        @include('operador.partials.modal-producto-validar')

                        <button type="button" onclick="mostrarModal('rechazar-producto', '{{ $producto->id }}')"
                            class="text-red-600 hover:underline">
                            Rechazar
                        </button>
                        @include('operador.partials.modal-producto-rechazar')
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="py-4 text-center text-gray-500">No hay productos pendientes.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
