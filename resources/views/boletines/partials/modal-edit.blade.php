<div id="modal-editar-{{ $boletin->id }}"
    class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-50 items-center justify-center">
    <div class="relative bg-[var(--color-Gestion)] rounded-3xl shadow-xl p-6 w-full max-w-3xl mx-auto my-8">
        {{-- Encabezado del modal --}}
        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
            <h3 class="text-2xl font-semibold text-gray-900">Editar Boletín</h3>
            {{-- Botón para cerrar el modal --}}
            <button type="button" class="text-gray-400 hover:text-gray-600"
                onclick="cerrarModal('editar', '{{ $boletin->id }}')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        {{-- Cuerpo del modal: Formulario de edición --}}
        <form id="editBoletinForm-{{ $boletin->id }}" action="{{ route('boletines.update', $boletin->id) }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="edit_nombre_{{ $boletin->id }}" class="block text-sm font-bold text-gray-700">Nombre del
                    Boletín</label>
                <input type="text" name="nombre" id="edit_nombre_{{ $boletin->id }}"
                    value="{{ old('nombre', $boletin->nombre) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <div id="edit_nombre_error_{{ $boletin->id }}" class="text-red-500 text-sm mt-1"></div>
            </div>

            {{-- Campo Descripción --}}
            <div class="mb-4">
                <label for="edit_descripcion_{{ $boletin->id }}"
                    class="block text-sm font-bold text-gray-700">Descripción</label>
                <textarea name="descripcion" id="edit_descripcion_{{ $boletin->id }}" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('descripcion', $boletin->descripcion) }}</textarea>
                {{-- Div para mostrar errores de validación de la descripción --}}
                <div id="edit_descripcion_error_{{ $boletin->id }}" class="text-red-500 text-sm mt-1"></div>
            </div>

            {{-- Campo Archivo PDF - MEJORADO --}}
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Archivo PDF (opcional)</label>

                <div class="flex items-center gap-2">
                    <label for="edit_archivo_upload_{{ $boletin->id }}"
                        class="cursor-pointer flex items-center justify-center px-4 py-2 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        <span>Seleccionar archivo</span>
                        <input type="file" name="archivo_upload" id="edit_archivo_upload_{{ $boletin->id }}"
                            accept=".pdf" class="hidden"
                            onchange="updateFileName(this, 'fileNameDisplay_{{ $boletin->id }}')">
                    </label>
                    <span id="fileNameDisplay_{{ $boletin->id }}"
                        class="text-sm text-gray-600 truncate max-w-xs"></span>
                </div>

                <p class="mt-1 text-sm text-gray-500">Tamaño máximo: 5MB. Formato: PDF.</p>

                <div id="edit_archivo_upload_error_{{ $boletin->id }}" class="text-red-500 text-sm mt-1"></div>

                @if ($boletin->archivo)
                    <div class="flex flex-col items-center justify-center p-3 mt-4 rounded-md bg-gray-50"
                        id="current_file_section_{{ $boletin->id }}">
                        <h3 class="mb-2 text-md font-semibold text-black">Archivo Adjunto Actual:</h3>
                        <div class="flex items-center gap-4">
                            {{-- Construye la URL con asset() y la ruta relativa --}}
                            <a href="{{ Storage::url($boletin->archivo) }}" target="_blank"
                                class="flex flex-col items-center text-blue-600 transition-transform duration-300 ease-in-out transform hover:underline hover:scale-105">
                                <img src="{{ asset('images/PDF.svg') }}" alt="Icono PDF"
                                    class="mb-1 cursor-pointer w-16 h-20">
                                <span class="text-sm text-gray-700">Ver PDF</span>
                            </a>
                            <button type="button" onclick="removeFile('{{ $boletin->id }}')"
                                class="flex flex-col items-center text-red-600 hover:text-red-800 transition duration-150 ease-in-out focus:outline-none">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                <span class="text-xs">Quitar</span>
                            </button>
                        </div>
                        <input type="hidden" name="remove_archivo" id="remove_archivo_{{ $boletin->id }}"
                            value="0">
                    </div>
                @else
                    <div class="p-3 mt-4 rounded-md bg-gray-50" id="current_file_section_{{ $boletin->id }}"
                        style="display:none;">
                        <p class="text-gray-700">No hay archivo PDF adjunto para este boletín.</p>
                    </div>
                @endif
            </div>

            {{-- Campos Precio Más Alto y Lugar Precio Más Alto --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="edit_precio_mas_alto_{{ $boletin->id }}"
                        class="block text-sm font-bold text-gray-700">Precio Más Alto</label>
                    <div class="flex items-center gap-2 mt-1"> {{-- Usamos flexbox aquí --}}
                        <img src="{{ asset('images/subir.svg') }}" alt="Icono subir" class="w-6 h-6">
                        <input type="number" step="0.01" name="precio_mas_alto"
                            id="edit_precio_mas_alto_{{ $boletin->id }}"
                            value="{{ old('precio_mas_alto', $boletin->precio_mas_alto) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <div id="edit_precio_mas_alto_error_{{ $boletin->id }}" class="text-red-500 text-sm mt-1"></div>
                </div>
                <div>
                    <label for="edit_lugar_precio_mas_alto_{{ $boletin->id }}"
                        class="block text-sm font-bold text-gray-700">Lugar Precio Más Alto</label>
                    <input type="text" name="lugar_precio_mas_alto"
                        id="edit_lugar_precio_mas_alto_{{ $boletin->id }}"
                        value="{{ old('lugar_precio_mas_alto', $boletin->lugar_precio_mas_alto) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    {{-- Div para mostrar errores de validación del lugar del precio más alto --}}
                    <div id="edit_lugar_precio_mas_alto_error_{{ $boletin->id }}" class="text-red-500 text-sm mt-1">
                    </div>
                </div>
            </div>

            {{-- Campos Precio Más Bajo y Lugar Precio Más Bajo --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="edit_precio_mas_bajo_{{ $boletin->id }}"
                        class="block text-sm font-bold text-gray-700">Precio Más Bajo</label>
                    <div class="flex items-center gap-2 mt-1"> {{-- Usamos flexbox aquí --}}
                        <img src="{{ asset('images/bajar.svg') }}" alt="Iconobajar" class="w-6 h-6">
                        <input type="number" step="0.01" name="precio_mas_bajo"
                            id="edit_precio_mas_bajo_{{ $boletin->id }}"
                            value="{{ old('precio_mas_bajo', $boletin->precio_mas_bajo) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    {{-- Div para mostrar errores de validación del precio más bajo --}}
                    <div id="edit_precio_mas_bajo_error_{{ $boletin->id }}" class="text-red-500 text-sm mt-1"></div>
                </div>
                <div>
                    <label for="edit_lugar_precio_mas_bajo_{{ $boletin->id }}"
                        class="block text-sm font-bold text-gray-700">Lugar Precio Más Bajo</label>
                    <input type="text" name="lugar_precio_mas_bajo"
                        id="edit_lugar_precio_mas_bajo_{{ $boletin->id }}"
                        value="{{ old('lugar_precio_mas_bajo', $boletin->lugar_precio_mas_bajo) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    {{-- Div para mostrar errores de validación del lugar del precio más bajo --}}
                    <div id="edit_lugar_precio_mas_bajo_error_{{ $boletin->id }}" class="text-red-500 text-sm mt-1">
                    </div>
                </div>
            </div>

            {{-- Pie de página del modal: Botones de acción --}}
            <div class="flex items-center justify-between mt-6">

                <button type="button" onclick="cerrarModal('editar', '{{ $boletin->id }}')"
                    class="px-4 py-3 text-md font-bold text-white bg-[var(--color-textmarca)] rounded-xl hover:bg-[var(--color-texthovermarca)]">
                    Cancelar
                </button>

                <button type="submit"
                    class="px-4 py-3 text-md font-bold text-white bg-[var(--color-sgt)] rounded-xl hover:bg-[var(--color-hoversgt)]">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Función para formatear el tamaño del archivo a un formato legible
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Función para actualizar el nombre del archivo seleccionado y su tamaño
    function updateFileName(input, displayId) {
        const displayElement = document.getElementById(displayId);
        if (input.files.length > 0) {
            const file = input.files[0];
            displayElement.textContent = `${file.name} (${formatFileSize(file.size)})`;
        } else {
            displayElement.textContent = '';
        }
    }

    // Función para "quitar" el archivo existente
    function removeFile(boletinId) {
        // Establece el valor del campo oculto a 1 para indicar que se debe eliminar
        document.getElementById(`remove_archivo_${boletinId}`).value = "1";
        
        // Oculta la sección de "Archivo Adjunto Actual"
        const currentFileSection = document.getElementById(`current_file_section_${boletinId}`);
        if (currentFileSection) {
            currentFileSection.style.display = 'none'; // Oculta el div
            // También puedes resetear el input de archivo si quieres
            document.getElementById(`edit_archivo_upload_${boletinId}`).value = ''; // Limpia el input file
            document.getElementById(`fileNameDisplay_${boletinId}`).textContent = ''; // Limpia el display del nombre
        }
    }

    // Lógica para inicializar el estado al cargar el DOM o al abrir el modal
    document.addEventListener('DOMContentLoaded', function() {
        const boletinId = '{{ $boletin->id }}'; 
        const removeArchivoInput = document.getElementById(`remove_archivo_${boletinId}`);
        const currentFileSection = document.getElementById(`current_file_section_${boletinId}`);

        if (removeArchivoInput) {
            // Siempre reinicia a 0 cuando el modal se carga/inicializa
            removeArchivoInput.value = "0"; 
        }

        // Blade solo inyecta un valor booleano para que JavaScript decida la visibilidad
        const hasExistingFile = {{ $boletin->archivo ? 'true' : 'false' }};

        if (currentFileSection) {
            if (hasExistingFile) {
                currentFileSection.style.display = 'flex'; // O 'block', según tu diseño
            } else {
                currentFileSection.style.display = 'none';
            }
        }
    });
</script>