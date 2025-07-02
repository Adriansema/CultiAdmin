<div id="modal-ver-{{ $boletin->id }}" class="hidden">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="w-full max-w-xl p-6 bg-[var(--color-gris1)] rounded-lg shadow-xl space-y-4 ">
            <h1 class="mb-4 text-xl font-semibold">
                Detalles del Boletín
            </h1>

            {{-- Sección de Estado del Boletín (Más Prominente) --}}
            <div class="mb-6 p-4 rounded-lg
                    @if ($boletin->estado === 'aprobado') bg-green-100 text-green-800 border border-green-300
                    @elseif ($boletin->estado === 'rechazado') bg-red-100 text-red-800 border border-red-300
                    @elseif ($boletin->estado === 'pendiente') bg-yellow-100 text-yellow-800 border border-yellow-300
                    @else bg-gray-100 text-gray-800 border border-gray-300 @endif">

                <p class="mb-2">
                    <strong class="font-semibold">Usuario Creador:</strong>
                    @if ($boletin->user)
                    {{ $boletin->user->name }}
                    @if ($boletin->user->roles->isNotEmpty())
                    <span class="text-gray-600">({{ $boletin->user->roles->pluck('name')->join(', ') }})</span>
                    @endif
                    @else
                    Usuario Desconocido
                    @endif
                </p>

                <h3 class="text-base font-semibold">Estado Actual:
                    <span class="font-bold">{{ ucfirst($boletin->estado) }}</span>
                </h3>

                {{-- AÑADIR LA LÓGICA DE VALIDACIÓN/RECHAZO AQUÍ --}}

                @if ($boletin->estado === 'rechazado' && $boletin->observaciones)
                <p class="mt-2 text-sm">
                    <strong>Observación del Operador:</strong> {{ $boletin->observaciones }}
                </p>
                {{-- Aquí puedes añadir quién lo rechazó --}}
                @if ($boletin->rechazador)
                <p class="mt-1 text-sm text-red-700">
                    Rechazado por: <span class="font-medium">{{ $boletin->rechazador->name }}</span>
                </p>
                @endif
                <div class="mt-4">
                    {{-- <a href="{{ route('boletines.edit', $boletin->id) }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Ir a Editar Boletín →
                    </a> --}}
                    <p>Debes ir a editar el Boletin para que puedas Corregir tú error</p>
                </div>
                @elseif ($boletin->estado === 'aprobado')
                <p class="mt-2 text-sm">¡Tu boletín ha sido aprobado y está listo para ser consumido!</p>
                {{-- Aquí puedes añadir quién lo validó --}}
                @if ($boletin->validador)
                <p class="mt-1 text-sm text-green-700">
                    Validado por: <span class="font-medium">{{ $boletin->validador->name }}</span>
                </p>
                @endif
                @elseif ($boletin->estado === 'pendiente')
                <p class="mt-2 text-sm">Tu boletín está pendiente de revisión por parte del operador.</p>
                {{-- Opcional: Si quieres indicar que aún no hay validador/rechazador --}}
                {{-- <p class="mt-1 text-sm text-gray-600">Aún no ha sido revisado por un operador.</p> --}}
                @endif
            </div>

            {{-- Detalles del Boletin (Nombre, Descripción, Archivo) --}}
            <div class="p-3 mt-4 rounded-md bg-gray-50">
                <h3 class="mb-2 font-semibold text-black text-md">Nombre del Boletín:</h3>
                <p class="mb-4 text-black whitespace-pre-line">{{ $boletin->nombre }}</p> {{-- Muestra el nombre --}}

                <h3 class="mb-2 font-semibold text-black text-md">Descripción:</h3>
                <p class="text-black whitespace-pre-line">{{ $boletin->descripcion }}</p> {{-- ¡CAMBIADO! Ahora usa
                $boletin->descripcion --}}
            </div>

            @if ($boletin->archivo) {{-- ¡REVERTIDO! Usa $boletin->archivo --}}
            <div class="flex flex-col items-center justify-center p-3 mt-4 rounded-md bg-gray-50">
                <h3 class="mb-2 text-xs font-semibold text-black">Archivo Adjunto:</h3>
                {{-- Construye la URL con asset() y la ruta relativa --}}
                <a href="{{ asset('storage/' . $boletin->archivo) }}" target="_blank"
                    class="flex flex-col items-center text-blue-600 transition-transform duration-300 ease-in-out transform hover:underline hover:scale-105">
                    <img src="{{ asset('images/PDF.svg') }}" alt="Icono PDF" class="mb-1 cursor-pointer w-14 h-14">
                    <span class="text-sm text-gray-700">Ver PDF</span>
                </a>
            </div>
            @else
            <div class="p-3 mt-4 rounded-md bg-gray-50">
                <p class="text-gray-700">No hay archivo PDF adjunto para este boletín.</p>
            </div>
            @endif

            {{-- Sección de Indicadores de Precio --}}
            <div class="p-3 mt-4 rounded-md bg-gray-50">
                <h3 class="mb-2 font-semibold text-black text-md">Indicadores de Precio:</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    {{-- Precio Más Alto --}}
                    <div>
                        <p class="text-gray-700">
                            <strong>Precio Más Alto:</strong>
                            @if ($boletin->precio_mas_alto)
                            <span class="text-green-600">${{ number_format($boletin->precio_mas_alto, 2) }}</span>
                            @if ($boletin->lugar_precio_mas_alto)
                            <span class="text-gray-600">({{ $boletin->lugar_precio_mas_alto }})</span>
                            @endif
                            @else
                            N/A
                            @endif
                        </p>
                    </div>
                    {{-- Precio Más Bajo --}}
                    <div>
                        <p class="text-gray-700">
                            <strong>Precio Más Bajo:</strong>
                            @if ($boletin->precio_mas_bajo)
                            <span class="text-red-600">${{ number_format($boletin->precio_mas_bajo, 2) }}</span>
                            @if ($boletin->lugar_precio_mas_bajo)
                            <span class="text-gray-600">({{ $boletin->lugar_precio_mas_bajo }})</span>
                            @endif
                            @else
                            N/A
                            @endif
                        </p>
                    </div>
                </div>
            </div>


            <button type="button" onclick="ocultarModal('ver', '{{ $boletin->id }}')"
                class="px-4 py-2 text-white bg-[var(--color-iconos)] rounded hover:bg-[var(--color-iconos6)]">
                Cerrar
            </button>
        </div>
    </div>
</div>
