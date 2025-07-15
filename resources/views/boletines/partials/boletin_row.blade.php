<tr id="boletin-row-{{ $boletin->id }}" class="bg-white hover:bg-gray-200">
    <td class="px-6 py-4">
        {{ Str::limit($boletin->nombre, 40, '...') }}
    </td>

    <td class="px-6 py-4">
        {{ Str::limit($boletin->descripcion, 60, '...') }}
    </td>

    <td class="px-6 py-4">
        {{ $boletin->created_at->locale('es')->translatedFormat('d \d\e F \d\e\l Y h:i a') }}
        <span class="text-xs text-gray-500">
            ({{ $boletin->created_at->diffForHumans() }})
        </span>
    </td>

    <td class="px-6 py-4">
        @if ($boletin->precio_mas_alto)
            <p class="flex items-center text-green-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18">
                    </path>
                </svg>
                {{ $boletin->precio_mas_alto_formatted }}
            </p>
            @if ($boletin->lugar_precio_mas_alto)
                <span class="block text-xs text-gray-500">({{ $boletin->lugar_precio_mas_alto }})</span>
            @endif
        @else
            N/A
        @endif
    </td>

    <td class="px-6 py-4">
        @if ($boletin->precio_mas_bajo)
            <p class="flex items-center text-red-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
                {{ $boletin->precio_mas_bajo_formatted }}
            </p>
            @if ($boletin->lugar_precio_mas_bajo)
                <span class="block text-xs text-gray-500">({{ $boletin->lugar_precio_mas_bajo }})</span>
            @endif
        @else
            N/A
        @endif
    </td>

    <td class="px-6 py-4">
        <span
            class="inline-block px-3 py-2 text-md font-semibold text-white rounded-xl
            {{ $boletin->estado === 'aprobado' ? 'bg-green-600' : ($boletin->estado === 'pendiente' ? 'bg-yellow-500' : 'bg-red-600') }}">
            {{ ucfirst($boletin->estado) }}
        </span>
    </td>

    <td class="px-6 py-4">
        <div class="flex items-center space-x-2">
            @can('crear boletin')
                <button type="button" onclick="mostrarModal('ver', '{{ $boletin->id }}')"
                    class="px-3 py-2 text-center text-white bg-green-600 rounded-xl hover:bg-green-700">
                    Ver
                </button>
            @endcan

            @can('editar boletin')
                <button type="button" onclick="mostrarModal('editar', '{{ $boletin->id }}')"
                    class="px-3 py-2 text-center text-white bg-yellow-600 rounded-xl hover:bg-yellow-700">
                    Editar
                </button>
            @endcan

            @can('eliminar boletin')
                <button type="button" onclick="mostrarModal('boletines', '{{ $boletin->id }}')"
                    class="px-3 py-2 text-center text-white bg-red-600 rounded-xl hover:bg-red-700">
                    Eliminar
                </button>
            @endcan

            @can('validar boletin')
                <button type="button" onclick="mostrarModal('validar-boletines', '{{ $boletin->id }}')"
                    class="px-3 py-2 text-center text-white bg-blue-600 rounded-xl hover:bg-blue-700">
                    Validar
                </button>
            @endcan

            @can('validar boletin')
                <button type="button" onclick="mostrarModal('rechazar-boletines', '{{ $boletin->id }}')"
                    class="px-3 py-2 text-center text-white bg-orange-600 rounded-xl hover:bg-orange-700">
                    Rechazar
                </button>
            @endcan
        </div>
    </td>
</tr>

