<div class="overflow-hidden bg-white rounded shadow">
    <table class="min-w-full text-sm text-left bg-red-200 hover:bg-red-300">
        <thead class="bg-[var(--color-tabla)]">
            <tr>
                <th class="px-4 py-2">Contenido</th>
                <th class="px-4 py-2">Fecha</th>
                <th class="px-4 py-2">Estado</th>
                <th class="px-4 py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($boletines as $boletin)
                <tr class="bg-white hover:bg-gray-300">
                    <td class="px-4 py-2">{{ Str::limit(strip_tags($boletin->contenido), 50) }}</td>
                    <td class="max-w-xs px-4 py-2 text-gray-600 break-words whitespace-normal align-top">
                        {{ $boletin->created_at->locale('es')->translatedFormat('d \d\e F \d\e\l Y h:i a') }}
                        <span class="block text-xs text-gray-500">
                            ({{ $boletin->created_at->diffForHumans() }})
                        </span>
                    </td>
                    <td class="px-4 py-2">
                        <span
                            class="inline-block px-3 py-1 text-sm font-semibold text-white rounded
                                        {{ $boletin->estado === 'aprobado' ? 'bg-green-600' : ($boletin->estado === 'pendiente' ? 'bg-yellow-500' : 'bg-red-600') }}">
                            {{ ucfirst($boletin->estado) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 space-x-2">
                        <button type="button" onclick="mostrarModal('view', '{{ $boletin->id }}')"
                            class="text-blue-600 hover:underline">
                            Ver
                        </button>
                        @include('operador.partials.modal-boletin-views')


                        <button type="button" onclick="mostrarModal('validar-boletin', '{{ $boletin->id }}')"
                            class="text-green-600 hover:underline">
                            Validar
                        </button>
                        @include('operador.partials.modal-boletin-validar')

                        <!-- Botón que abre el modal -->
                        <button type="button" onclick="mostrarModal('rechazar-boletin', '{{ $boletin->id }}')"
                            class="text-red-600 hover:underline">
                            Rechazar
                        </button>
                        @include('operador.partials.modal-boletin-rechazar')
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="py-4 text-center text-gray-500">No hay boletines pendientes.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
