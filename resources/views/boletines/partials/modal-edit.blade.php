<div id="modal-editar-{{ $boletin->id }}" class="hidden">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <form id="form-boletin-{{ $boletin->id }}" action="{{ route('boletines.update', $boletin) }}" method="POST"
            enctype="multipart/form-data" class="bg-gray-200 px-4 py-6 space-y-4 rounded-xl">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="archivo_upload" class="block font-semibold">Archivo Adjunto:</label>

                {{-- Muestra el archivo actual como icono si existe --}}
                @if ($boletin->archivo)
                    <div class="mt-2 flex items-center space-x-3 p-3 bg-gray-50 rounded-md **archivo-actual-info**">
                        @php
                            // Obtener la extensión para mostrar un icono relevante
                            $extension = pathinfo($boletin->archivo, PATHINFO_EXTENSION);
                            $iconSrc = asset('images/form.svg'); // Icono genérico por defecto

                            // Asignar icono según la extensión (asegúrate de tener estos archivos en public/images/)
                            if (in_array(strtolower($extension), ['pdf'])) {
                                $iconSrc = asset('images/PDF.svg');
                            }
                        @endphp
                        <a href="{{ asset('storage/' . $boletin->archivo) }}" target="_blank"
                            class="flex flex-col items-center text-blue-600 hover:underline
                            transform transition-transform duration-300 ease-in-out hover:scale-105">
                            <img src="{{ $iconSrc }}" alt="Icono de Archivo Actual"
                                class="w-16 h-16 cursor-pointer">
                            <span class="**archivo-extension-text**">Ver Archivo Actual
                                ({{ strtoupper($extension) ?: 'Sin Ext.' }})</span>
                        </a>
                        {{-- <span class="flex-grow text-sm text-gray-500 truncate">{{ basename($boletin->archivo) }}</span> --}}
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
                <input type="file" name="archivo_upload" id="archivo_upload"
                    class="w-full p-2 mt-2 border rounded-lg">
                @error('archivo_upload')
                    {{-- El nombre debe coincidir con el del campo en el controlador --}}
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="contenido" class="block font-semibold">Contenido:</label>
                <textarea name="contenido" id="contenido" rows="8" class="w-full p-2 mt-1 border rounded-lg" required>{{ old('contenido', $boletin->contenido) }}</textarea>
                @error('contenido')
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
