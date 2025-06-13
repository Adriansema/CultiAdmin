<div id="modal-ver-{{ $boletin->id }}" class="hidden">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="w-full max-w-xl p-6 bg-[var(--color-gris1)] rounded-lg shadow-xl space-y-4 ">
            <h1 class="mb-4 text-xl font-semibold">
                Detalles del Boletín
            </h1>

            {{-- Sección de Estado del Boletín (Más Prominente) --}}
            <div
                class="mb-6 p-4 rounded-lg
                    @if ($boletin->estado === 'aprobado') bg-green-100 text-green-800 border border-green-300
                    @elseif ($boletin->estado === 'rechazado') bg-red-100 text-red-800 border border-red-300
                    @elseif ($boletin->estado === 'pendiente') bg-yellow-100 text-yellow-800 border border-yellow-300
                    @else bg-gray-100 text-gray-800 border border-gray-300 @endif">
                <h3 class="text-base font-semibold">Estado Actual:
                    <span class="font-bold">{{ ucfirst($boletin->estado) }}</span>
                </h3>

                {{-- AÑADIR LA LÓGICA DE VALIDACIÓN/RECHAZO AQUÍ --}}

                @if ($boletin->estado === 'rechazado' && $boletin->observaciones)
                    <p class="text-sm mt-2">
                        <strong>Observación del Operador:</strong> {{ $boletin->observaciones }}
                    </p>
                    {{-- Aquí puedes añadir quién lo rechazó --}}
                    @if ($boletin->rechazador)
                        <p class="text-sm mt-1 text-red-700">
                            Rechazado por: <span class="font-medium">{{ $boletin->rechazador->name }}</span>
                        </p>
                    @endif
                    <div class="mt-4">
                        <a href="{{ route('boletines.edit', $boletin->id) }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Ir a Editar Boletín →
                        </a>
                    </div>
                @elseif ($boletin->estado === 'aprobado')
                    <p class="text-sm mt-2">¡Tu boletín ha sido aprobado y está listo para ser consumido!</p>
                    {{-- Aquí puedes añadir quién lo validó --}}
                    @if ($boletin->validador)
                        <p class="text-sm mt-1 text-green-700">
                            Validado por: <span class="font-medium">{{ $boletin->validador->name }}</span>
                        </p>
                    @endif
                @elseif ($boletin->estado === 'pendiente')
                    <p class="text-sm mt-2">Tu boletín está pendiente de revisión por parte del operador.</p>
                    {{-- Opcional: Si quieres indicar que aún no hay validador/rechazador --}}
                    {{-- <p class="text-sm mt-1 text-gray-600">Aún no ha sido revisado por un operador.</p> --}}
                @endif
            </div>

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

            <button type="button" onclick="ocultarModal('ver', '{{ $boletin->id }}')"
                class="px-4 py-2 text-white bg-[var(--color-iconos)] rounded hover:bg-[var(--color-iconos6)]">
                Cancelar
            </button>
        </div>
    </div>
</div>
