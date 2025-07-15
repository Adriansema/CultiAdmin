<div id="modal-ver-{{ $boletin->id }}"
    class="hidden fixed inset-0 z-50 flex-col items-center justify-center bg-gray-700/50 p-4">
    <h1 class="mb-4 text-3xl font-bold text-white text-center">
        Detalles del boletín
    </h1>

    {{-- Contenedor del contenido del modal --}}
    <div class="w-full max-w-xl p-6 bg-[var(--color-gris1)] rounded-lg shadow-xl space-y-4 ">

        {{-- Lógica PHP para determinar las clases del estado del boletín --}}
        @php
            $baseClasses = 'mb-6 p-4 rounded-lg'; // Clases base para el div de estado
            $estadoClasses = ''; // Inicializa las clases de estado

            if ($boletin->estado === 'aprobado') {
                $estadoClasses = 'bg-green-100 text-green-800 border border-green-300';
            } elseif ($boletin->estado === 'rechazado') {
                $estadoClasses = 'bg-red-100 text-red-800 border border-red-300';
            } elseif ($boletin->estado === 'pendiente') {
                $estadoClasses = 'bg-yellow-100 text-yellow-800 border border-yellow-300';
            } else {
                $estadoClasses = 'bg-gray-100 text-gray-800 border border-gray-300';
            }

            // Combina todas las clases para el div de estado
            $cardClasses = $baseClasses . ' ' . $estadoClasses;
        @endphp

        {{-- Sección de Estado del Boletín (Más Prominente) --}}
        <div class="{{ $cardClasses }}">
            <p class="mb-2">
                <strong class="font-semibold">Creador:</strong>
                @if ($boletin->user)
                    {{ $boletin->user->name }}
                    @if ($boletin->user->roles->isNotEmpty())
                        <span class="text-gray-600">({{ $boletin->user->roles->pluck('name')->join(', ') }})</span>
                    @endif
                @else
                    Usuario desconocido
                @endif
            </p>

            <h3 class="text-base font-semibold">Estado actual:
                <span class="font-bold">{{ ucfirst($boletin->estado) }}</span>
            </h3>

            {{-- AÑADIR LA LÓGICA DE VALIDACIÓN/RECHAZO AQUÍ --}}
            @if ($boletin->estado === 'rechazado' && $boletin->observaciones)
                <p class="mt-2 text-sm">
                    <strong>Observaciones:</strong> {{ $boletin->observaciones }}
                </p>
                {{-- Aquí puedes añadir quién lo rechazó --}}
                @if ($boletin->rechazador)
                    <p class="mt-1 text-sm text-red-700">
                        Rechazado por: <span class="font-medium">{{ $boletin->rechazador->name }}</span>
                    </p>
                @endif
                <div class="mt-4">
                    <p>Debes ir a editar el boletín para que puedas corregir tu error</p>
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
                <p class="mt-2 text-sm">Tu boletín está pendiente de revisión</p>
            @endif
        </div>

        <label class="mb-2 mt-4 font-semibold text-gray-700 text-sm">Nombre del boletín:</label>
        <div class="block p-3 mt-4 rounded-lg bg-white border border-gray-300 shadow-sm">
            {{ $boletin->nombre }}
        </div>
        <div class="text-red-500 text-sm mt-1"></div>

        {{-- Sección de Descripción --}}
        <label class="mb-2 mt-4 font-semibold text-gray-700 text-sm">Descripción:</label>
        <div class="p-3 mt-4 rounded-lg border border-gray-300 bg-gray-50">
            <p class="text-gray-700 whitespace-pre-line">{{ $boletin->descripcion }}</p>
        </div>

        <label class="mb-2 mt-4 font-semibold text-gray-700 text-sm">Archivo adjunto:</label>
        @if ($boletin->archivo)
            <div class="flex flex-col items-center justify-center p-3 mt-2 rounded-md bg-gray-50">
                <a href="{{ asset('storage/' . $boletin->archivo) }}" target="_blank"
                    class="flex flex-col items-center text-blue-600 transition-transform duration-300 ease-in-out transform hover:underline hover:scale-105">
                    <img src="{{ asset('images/PDF.svg') }}" alt="Icono PDF" class="mb-1 cursor-pointer w-14 h-14">
                    <span class="text-sm text-gray-700">Ver pdf</span>
                </a>
            </div>
        @else
            <div class="p-3 mt-2 rounded-md bg-gray-50">
                <p class="text-gray-700">No hay archivo pdf adjunto para este boletín.</p>
            </div>
        @endif

        {{-- Sección de Indicadores de Precio --}}
        <label class="mb-2 mt-4 font-semibold text-gray-700 text-sm">Indicadores de precio:</label>
        <div class="flex flex-col md:flex-row md:space-x-4 space-y-4 md:space-y-0">
            {{-- Precio Más Alto --}}
            <div class="p-3 border border-gray-300 bg-white rounded-lg flex-1">
                <strong class="font-bold text-sm block mb-2">Precio más alto:</strong>
                <div>
                    <p class="text-gray-700">
                        @if ($boletin->precio_mas_alto_formatted) {{-- Usar la propiedad formateada --}}
                            <span class="text-green-600">{{ $boletin->precio_mas_alto_formatted }}</span>
                            @if ($boletin->lugar_precio_mas_alto)
                                <span class="text-gray-600 font-bold">({{ $boletin->lugar_precio_mas_alto }})</span>
                            @endif
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>

            {{-- Precio Más Bajo --}}
            <div class="p-3 border border-gray-300 bg-white rounded-lg flex-1">
                <strong class="font-bold text-sm block mb-2">Precio más bajo:</strong>
                <div>
                    <p class="text-gray-700">
                        @if ($boletin->precio_mas_bajo_formatted) {{-- Usar la propiedad formateada --}}
                            <span class="text-red-600">{{ $boletin->precio_mas_bajo_formatted }}</span>
                            @if ($boletin->lugar_precio_mas_bajo)
                                <span class="text-gray-600 font-bold">({{ $boletin->lugar_precio_mas_bajo }})</span>
                            @endif
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <button type="button" onclick="cerrarModal('ver', '{{ $boletin->id }}')"
            class="px-4 py-2 text-white bg-[var(--color-iconos)] rounded hover:bg-[var(--color-iconos6)]">
            Cerrar
        </button>
    </div>
</div>
