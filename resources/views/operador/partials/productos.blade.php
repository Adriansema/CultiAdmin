<h3 class="mb-3 text-lg font-bold">Productos Pendientes</h3>
<div class="overflow-hidden bg-white rounded shadow">
    <table class="min-w-full text-sm text-left bg-red-200 hover:bg-red-300">
        <thead>
            <tr class="bg-red-100 cursor-default">
                <th class="px-4 py-2">Tipo</th>
                <th class="px-4 py-2">Fecha</th>
                <th class="px-4 py-2">Estado</th>
                <th class="px-4 py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productos as $producto)
                <tr class="border-t-2 bg-gray-200 hover:bg-gray-300 cursor-pointer">
                    <td class="px-4 py-2 capitalize">{{ $producto->tipo }}</td>
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
    <div class="mt-4">
        {{ $productos->links() }}
    </div>
</div>
