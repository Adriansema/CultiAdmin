<div id="modal-view-{{ $boletin->id }}" class="hidden">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="w-full max-w-xl p-6 bg-[var(--color-gris1)] rounded-lg shadow-xl space-y-4 ">
            {{-- Detalles del Boletin --}}
            @if ($boletin->archivo)
                <div class="flex justify-between gap-4 mt-4"> {{-- Contenedor principal con flexbox y gap para la separación --}}

                    {{-- Div del Contenido (Izquierda) --}}
                    <div class="flex-grow p-3 rounded-md bg-gray-50 "> {{-- flex-grow para ocupar espacio y estilos de tarjeta --}}
                        <h3 class="font-semibold text-black text-md ">Contenido:</h3>
                        <p class="text-black whitespace-pre-line">
                            {{ $boletin->contenido }}
                        </p>
                    </div>

                    {{-- Div del Icono (Derecha) --}}
                    <div class="flex flex-col items-center justify-center p-3 rounded-md bg-gray-50 flex-shrink-2">
                        {{-- Estilos de tarjeta y centrado --}}
                        <h3 class="text-xs font-semibold text-black ">Archivo adjunto:</h3>
                        {{-- Oculto visualmente, para accesibilidad --}}
                        <a href="{{ asset('storage/' . $boletin->archivo) }}" target="_blank"
                            class="flex flex-col items-center text-blue-600 transition-transform duration-300 ease-in-out transform hover:underline hover:scale-105">
                            <img src="{{ asset('images/PDF.svg') }}" alt="Icono PDF"
                                class="mb-1 cursor-pointer w-14 h-14">
                        </a>
                    </div>
                </div>
            @else
                {{-- Si no hay archivo, solo mostrar el contenido ocupando todo el ancho --}}
                <div class="p-3 mt-4 rounded-md bg-gray-50">
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
