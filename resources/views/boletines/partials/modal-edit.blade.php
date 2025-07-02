<div id="modal-editar-{{ $boletin->id }}" class="hidden">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <form id="form-boletin-{{ $boletin->id }}" action="{{ route('boletines.update', $boletin) }}" method="POST"
            enctype="multipart/form-data" class="px-4 py-6 space-y-4 bg-gray-200 rounded-xl">
            @csrf
            @method('PUT')

            <h1 class="mb-4 text-xl font-semibold">Editar Boletín</h1> {{-- Título añadido para claridad --}}

            {{-- Campo para el Nombre --}}
            <div class="mb-4">
                <label for="nombre" class="block font-semibold">Nombre del Boletín:</label>
                <input type="text" name="nombre" id="nombre" class="w-full p-2 mt-1 border rounded-lg"
                    value="{{ old('nombre', $boletin->nombre) }}" required>
                @error('nombre')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campo para la Descripción (anteriormente "Contenido") --}}
            <div class="mb-4">
                <label for="descripcion" class="block font-semibold">Descripción:</label>
                <textarea name="descripcion" id="descripcion" rows="4" class="w-full p-2 mt-1 border rounded-lg"
                    required>{{ old('descripcion', $boletin->descripcion) }}</textarea>
                @error('descripcion')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Sección de Indicadores de Precio --}}
            <div class="p-3 mt-4 bg-gray-100 rounded-md"> {{-- Cambiado a bg-gray-100 para diferenciar --}}
                <h3 class="mb-2 font-semibold text-black text-md">Indicadores de Precio:</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    {{-- Precio Más Alto --}}
                    <div>
                        <label for="precio_mas_alto" class="block text-sm font-medium text-gray-700">Precio Más
                            Alto:</label>
                        <input type="number" step="0.01" name="precio_mas_alto" id="precio_mas_alto"
                            class="w-full p-2 mt-1 border rounded-lg"
                            value="{{ old('precio_mas_alto', $boletin->precio_mas_alto) }}">
                        @error('precio_mas_alto')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <label for="lugar_precio_mas_alto" class="block mt-2 text-sm font-medium text-gray-700">Lugar
                            (Precio Más Alto):</label>
                        <input type="text" name="lugar_precio_mas_alto" id="lugar_precio_mas_alto"
                            class="w-full p-2 mt-1 border rounded-lg"
                            value="{{ old('lugar_precio_mas_alto', $boletin->lugar_precio_mas_alto) }}">
                        @error('lugar_precio_mas_alto')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Precio Más Bajo --}}
                    <div>
                        <label for="precio_mas_bajo" class="block text-sm font-medium text-gray-700">Precio Más
                            Bajo:</label>
                        <input type="number" step="0.01" name="precio_mas_bajo" id="precio_mas_bajo"
                            class="w-full p-2 mt-1 border rounded-lg"
                            value="{{ old('precio_mas_bajo', $boletin->precio_mas_bajo) }}">
                        @error('precio_mas_bajo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <label for="lugar_precio_mas_bajo" class="block mt-2 text-sm font-medium text-gray-700">Lugar
                            (Precio Más Bajo):</label>
                        <input type="text" name="lugar_precio_mas_bajo" id="lugar_precio_mas_bajo"
                            class="w-full p-2 mt-1 border rounded-lg"
                            value="{{ old('lugar_precio_mas_bajo', $boletin->lugar_precio_mas_bajo) }}">
                        @error('lugar_precio_mas_bajo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Sección de Archivo Adjunto --}}
            <div class="mb-4">
                <label for="archivo_upload" class="block font-semibold">Archivo Adjunto:</label>

                @if ($boletin->archivo) {{-- ¡REVERTIDO! Usa $boletin->archivo --}}
                <div class="mt-2 flex items-center space-x-3 p-3 bg-gray-50 rounded-md **archivo-actual-info**">
                    @php
                    // Obtener la extensión del nombre de archivo de la ruta relativa
                    $extension = pathinfo($boletin->archivo, PATHINFO_EXTENSION);
                    $iconSrc = asset('images/form.svg');

                    if (in_array(strtolower($extension), ['pdf'])) {
                    $iconSrc = asset('images/PDF.svg');
                    }
                    @endphp
                    <a href="{{ asset('storage/' . $boletin->archivo) }}" target="_blank" {{-- ¡REVERTIDO! Usa asset()
                        --}}
                        class="flex flex-col items-center text-blue-600 transition-transform duration-300 ease-in-out transform hover:underline hover:scale-105">
                        <img src="{{ $iconSrc }}" alt="Icono de Archivo Actual" class="w-16 h-16 cursor-pointer">
                        <span class="**archivo-extension-text**">Ver Archivo Actual
                            ({{ strtoupper($extension) ?: 'Sin Ext.' }})</span>
                    </a>
                </div>
                <p class="mt-2 text-sm text-gray-600 **info-archivo-subido**">Deja este campo vacío para mantener el
                    archivo actual, o sube uno
                    nuevo para reemplazarlo.</p>
                @else
                <p class="mt-2 text-sm text-gray-600 **no-archivo-mensaje**">No hay archivo adjunto actualmente.
                    Puedes subir uno a
                    continuación.</p>
                @endif

                {{-- Input para subir un nuevo archivo --}}
                <input type="file" name="archivo_upload" id="archivo_upload" class="w-full p-2 mt-2 border rounded-lg">
                @error('archivo_upload')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mb-6">
                <a href="{{ route('boletines.index') }}" onclick="ocultarModal('editar', '{{ $boletin->id }}')"
                    class="inline-block px-4 py-2 text-white bg-gray-600 rounded hover:bg-gray-700">
                    Cerrar
                </a>
                <button type="submit" class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>
