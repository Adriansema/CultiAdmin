<tr id="boletin-row-{{ $boletin->id }}" class="bg-white hover:bg-gray-200">
    <td class="max-w-xs px-4 py-2 text-gray-600 break-words whitespace-normal align-top boletin-contenido-cell">
        {{ Str::limit($boletin->contenido, 60) }}
    </td>

    <td class="max-w-xs px-4 py-2 text-gray-600 break-words whitespace-normal align-top boletin-fecha-cell">
        {{ $boletin->created_at->locale('es')->translatedFormat('d \d\e F \d\e\l Y h:i a') }}
        <span class="block text-xs text-gray-500">
            ({{ $boletin->created_at->diffForHumans() }})
        </span>
    </td>

    <td class="px-4 py-2 boletin-estado-cell">
        <span
            class="inline-block px-3 py-1 text-sm font-semibold text-white rounded
            {{ $boletin->estado === 'aprobado' ? 'bg-green-600' : ($boletin->estado === 'pendiente' ? 'bg-yellow-500' : 'bg-red-600') }}">
            {{ ucfirst($boletin->estado) }}
        </span>
    </td>

    <td class="flex flex-col px-4 py-2 space-y-1 align-top md:space-y-0 md:space-x-2 md:flex-row boletin-acciones-cell">
        <button type="button" onclick="mostrarModal('ver', '{{ $boletin->id }}')"
            class="px-3 py-1 text-sm text-center text-white bg-green-600 rounded hover:bg-green-700">
            Ver
        </button>

        <button type="button" onclick="mostrarModal('editar', '{{ $boletin->id }}')"
            class="px-3 py-1 text-sm text-center text-white bg-yellow-600 rounded hover:bg-yellow-700">
            Editar
        </button>

        <button type="button" onclick="mostrarModal('boletin', '{{ $boletin->id }}')"
            class="w-20 px-1 py-1 text-sm text-center text-white bg-red-600 rounded hover:bg-red-700">
            Eliminar
        </button>
    </td>
</tr>
