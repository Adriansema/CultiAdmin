<div id="modal-editar-{{ $boletin->id }}"
    class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div class="relative bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl mx-auto my-8">
        {{-- Encabezado del modal --}}
        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900">Editar Boletín: {{ $boletin->nombre }}</h3>
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
            method="POST" enctype="multipart/form-data" class="py-4">
            @csrf
            @method('PUT') {{-- Importante para que Laravel reconozca la solicitud como PUT/PATCH --}}

            {{-- Campo Nombre del Boletín --}}
            <div class="mb-4">
                <label for="edit_nombre_{{ $boletin->id }}" class="block text-sm font-medium text-gray-700">Nombre del
                    Boletín</label>
                <input type="text" name="nombre" id="edit_nombre_{{ $boletin->id }}"
                    value="{{ old('nombre', $boletin->nombre) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                {{-- Div para mostrar errores de validación del nombre --}}
                <div id="edit_nombre_error_{{ $boletin->id }}" class="text-red-500 text-sm mt-1"></div>
            </div>

            {{-- Campo Descripción --}}
            <div class="mb-4">
                <label for="edit_descripcion_{{ $boletin->id }}"
                    class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea name="descripcion" id="edit_descripcion_{{ $boletin->id }}" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('descripcion', $boletin->descripcion) }}</textarea>
                {{-- Div para mostrar errores de validación de la descripción --}}
                <div id="edit_descripcion_error_{{ $boletin->id }}" class="text-red-500 text-sm mt-1"></div>
            </div>

            {{-- Campo Archivo PDF --}}
            <div class="mb-4">
                <label for="edit_archivo_upload_{{ $boletin->id }}" class="block text-sm font-medium text-gray-700">Archivo
                    PDF (opcional)</label>
                <input type="file" name="archivo_upload" id="edit_archivo_upload_{{ $boletin->id }}" accept=".pdf"
                    class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                <p class="mt-1 text-sm text-gray-500">Tamaño máximo: 5MB. Formato: PDF.</p>
                {{-- Div para mostrar errores de validación del archivo --}}
                <div id="edit_archivo_upload_error_{{ $boletin->id }}" class="text-red-500 text-sm mt-1"></div>
                @if ($boletin->archivo)
                    <p class="mt-2 text-sm text-gray-600">Archivo actual: <a href="{{ Storage::url($boletin->archivo) }}"
                            target="_blank" class="text-blue-600 hover:underline">Ver PDF</a></p>
                @endif
            </div>

            {{-- Campos Precio Más Alto y Lugar Precio Más Alto --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="edit_precio_mas_alto_{{ $boletin->id }}"
                        class="block text-sm font-medium text-gray-700">Precio Más Alto</label>
                    <input type="number" step="0.01" name="precio_mas_alto" id="edit_precio_mas_alto_{{ $boletin->id }}"
                        value="{{ old('precio_mas_alto', $boletin->precio_mas_alto) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    {{-- Div para mostrar errores de validación del precio más alto --}}
                    <div id="edit_precio_mas_alto_error_{{ $boletin->id }}" class="text-red-500 text-sm mt-1"></div>
                </div>
                <div>
                    <label for="edit_lugar_precio_mas_alto_{{ $boletin->id }}"
                        class="block text-sm font-medium text-gray-700">Lugar Precio Más Alto</label>
                    <input type="text" name="lugar_precio_mas_alto" id="edit_lugar_precio_mas_alto_{{ $boletin->id }}"
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
                        class="block text-sm font-medium text-gray-700">Precio Más Bajo</label>
                    <input type="number" step="0.01" name="precio_mas_bajo" id="edit_precio_mas_bajo_{{ $boletin->id }}"
                        value="{{ old('precio_mas_bajo', $boletin->precio_mas_bajo) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    {{-- Div para mostrar errores de validación del precio más bajo --}}
                    <div id="edit_precio_mas_bajo_error_{{ $boletin->id }}" class="text-red-500 text-sm mt-1"></div>
                </div>
                <div>
                    <label for="edit_lugar_precio_mas_bajo_{{ $boletin->id }}"
                        class="block text-sm font-medium text-gray-700">Lugar Precio Más Bajo</label>
                    <input type="text" name="lugar_precio_mas_bajo" id="edit_lugar_precio_mas_bajo_{{ $boletin->id }}"
                        value="{{ old('lugar_precio_mas_bajo', $boletin->lugar_precio_mas_bajo) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    {{-- Div para mostrar errores de validación del lugar del precio más bajo --}}
                    <div id="edit_lugar_precio_mas_bajo_error_{{ $boletin->id }}" class="text-red-500 text-sm mt-1">
                    </div>
                </div>
            </div>

            {{-- Pie de página del modal: Botones de acción --}}
            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button type="button" onclick="cerrarModal('editar', '{{ $boletin->id }}')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 mr-2">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>