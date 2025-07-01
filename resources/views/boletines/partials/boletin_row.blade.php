{{-- resources/views/boletines/partials/boletin_row.blade.php --}}

<tr id="boletin-row-{{ $boletin->id }}" class="bg-white hover:bg-gray-200">
    {{-- Columna: Nombre --}}
    <td class="max-w-xs px-4 py-2 text-gray-800 break-words whitespace-normal align-top">
        {{ Str::limit($boletin->nombre_boletin, 40) }} {{-- Mostrar el nombre del boletín --}}
    </td>

    {{-- Columna: Descripción (antes Contenido) --}}
    <td class="max-w-xs px-4 py-2 text-gray-600 break-words whitespace-normal align-top boletin-contenido-cell">
        {{ Str::limit($boletin->contenido, 60) }} {{-- Esto es la descripción --}}
    </td>

    {{-- Columna: Fecha --}}
    <td class="max-w-xs px-4 py-2 text-gray-600 break-words whitespace-normal align-top boletin-fecha-cell">
        {{ $boletin->created_at->locale('es')->translatedFormat('d \d\e F \d\e\l Y h:i a') }}
        <span class="block text-xs text-gray-500">
            ({{ $boletin->created_at->diffForHumans() }})
        </span>
    </td>

    {{-- Columna: Precio Alto --}}
    <td class="px-4 py-2 text-gray-700 whitespace-nowrap align-top">
        @if ($boletin->precio_mas_alto)
            <p class="flex items-center text-green-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                </svg>
                ${{ number_format($boletin->precio_mas_alto, 2) }}
            </p>
            @if ($boletin->lugar_precio_mas_alto)
                <span class="block text-xs text-gray-500">({{ $boletin->lugar_precio_mas_alto }})</span>
            @endif
        @else
            N/A
        @endif
    </td>

    {{-- Columna: Precio Bajo --}}
    <td class="px-4 py-2 text-gray-700 whitespace-nowrap align-top">
        @if ($boletin->precio_mas_bajo)
            <p class="flex items-center text-red-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
                ${{ number_format($boletin->precio_mas_bajo, 2) }}
            </p>
            @if ($boletin->lugar_precio_mas_bajo)
                <span class="block text-xs text-gray-500">({{ $boletin->lugar_precio_mas_bajo }})</span>
            @endif
        @else
            N/A
        @endif
    </td>

    {{-- Columna: Estado --}}
    <td class="px-4 py-2 boletin-estado-cell align-top">
        <span
            class="inline-block px-3 py-1 text-sm font-semibold text-white rounded
            {{ $boletin->estado === 'aprobado' ? 'bg-green-600' : ($boletin->estado === 'pendiente' ? 'bg-yellow-500' : 'bg-red-600') }}">
            {{ ucfirst($boletin->estado) }}
        </span>
    </td>

    {{-- Columna: Acciones --}}
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
