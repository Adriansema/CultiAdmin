{{-- resources/views/boletines/partials/boletin_row.blade.php --}}

<tr id="boletin-row-{{ $boletin->id }}" class="bg-white hover:bg-gray-200">
    <td class="max-w-xs px-4 py-2 text-gray-600 break-words whitespace-normal align-top boletin-contenido-cell">
        {{ Str::limit($boletin->contenido, 60) }}
        
        {{-- *** NUEVA SECCIÓN: Mostrar Indicadores en la tabla *** --}}
        @if ($boletin->precio_mas_alto || $boletin->precio_mas_bajo)
            <div class="mt-2 text-xs text-gray-700">
                <p class="flex items-center">
                    <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                    </svg>
                    Máx: {{ $boletin->precio_mas_alto ? '$' . number_format($boletin->precio_mas_alto, 2) : 'N/A' }} 
                    @if ($boletin->lugar_precio_mas_alto) ({{ $boletin->lugar_precio_mas_alto }}) @endif
                </p>
                <p class="flex items-center">
                    <svg class="w-4 h-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                    Mín: {{ $boletin->precio_mas_bajo ? '$' . number_format($boletin->precio_mas_bajo, 2) : 'N/A' }} 
                    @if ($boletin->lugar_precio_mas_bajo) ({{ $boletin->lugar_precio_mas_bajo }}) @endif
                </p>
            </div>
        @endif
        {{-- *** FIN NUEVA SECCIÓN *** --}}
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
