<div id="modal-view-{{ $boletin->id }}" class="hidden">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="w-full max-w-xl p-6 bg-[var(--color-gris1)] rounded-lg shadow-xl space-y-4 ">
            {{-- Detalles del Boletin --}}
            @if ($boletin->archivo)
                <div class="mt-4 flex justify-between gap-4"> {{-- Contenedor principal con flexbox y gap para la separación --}}

                    {{-- Div del Contenido (Izquierda) --}}
                    <div class="flex-grow p-3 bg-gray-50 rounded-md "> {{-- flex-grow para ocupar espacio y estilos de tarjeta --}}
                        <h3 class="text-md font-semibold text-black ">Contenido:</h3>
                        <p class="text-black whitespace-pre-line">
                            {{ $boletin->contenido }}
                        </p>
                    </div>

                    {{-- Div del Icono (Derecha) --}}
                    <div class="p-3 bg-gray-50 rounded-md flex-shrink-2 flex flex-col items-center justify-center">
                        {{-- Estilos de tarjeta y centrado --}}
                        <h3 class="text-xs font-semibold text-black ">Archivo Adjunto:</h3>
                        {{-- Oculto visualmente, para accesibilidad --}}
                        <a href="{{ asset('storage/' . $boletin->archivo) }}" target="_blank"
                            class="text-blue-600 hover:underline flex flex-col items-center
                                    transform transition-transform duration-300 ease-in-out
                                    hover:scale-105">
                            <img src="{{ asset('images/PDF.svg') }}" alt="Icono PDF"
                                class="w-14 h-14 mb-1 cursor-pointer">
                        </a>
                    </div>
                </div>
            @else
                {{-- Si no hay archivo, solo mostrar el contenido ocupando todo el ancho --}}
                <div class="mt-4 p-3 bg-gray-50 rounded-md">
                    <p class="text-gray-700 whitespace-pre-line">
                        {{ $boletin->contenido }}
                    </p>
                </div>
            @endif

            <button type="button" onclick="ocultarModal('view', '{{ $boletin->id }}')"
                class="px-4 py-2 text-gray-700 bg-gray-300 rounded hover:bg-gray-400">
                Cancelar
            </button>
        </div>
    </div>
</div>
