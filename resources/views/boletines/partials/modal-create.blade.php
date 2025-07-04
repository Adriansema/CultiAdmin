<div id="createBoletinModal"
    class="fixed inset-0 z-[9999] items-center justify-center bg-black bg-opacity-50 overflow-y-auto hidden" wire:ignore>
    {{-- wire:ignore es importante si usas Livewire en la página padre --}}

    {{-- Contenedor del contenido del modal --}}
    <div id="createBoletinModalContent"
        class="w-full max-w-2xl p-6 mx-4 my-8 transition-all duration-300 transform bg-white shadow-lg rounded-2xl">

        {{-- Encabezado del modal con el botón X --}}
        <div class="flex items-center justify-between pb-4 mb-6">
            <h3 class="flex items-center space-x-3 text-2xl font-bold text-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-darkblue" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span>Subir archivo</span>
            </h3>
            {{-- Botón de la X para cerrar --}}
            <button type="button" id="closeCreateModalXButton"
                class="relative z-50 text-gray-500 transition-colors duration-200 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <p class="mb-6 text-sm text-gray-600">
            Sube un archivo, luego ingresa título y descripción.
        </p>

        <form id="createBoletinForm">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" autocomplete="off">

            <div id="createBoletinStep1" class="transition-all duration-300 ease-in-out step-1">
                
                <div id="fileDropArea"
                    class="relative flex flex-col items-center justify-center w-full h-64 p-6
                    border-gray-600 border-2 cursor-pointer rounded-2xl transition-all duration-300 hover:border-green-500 hover:bg-green-50/50"
                    style="border-style: dashed !important;">
                    <div id="dropAreaOverlay"
                        class="absolute inset-0 bg-white opacity-0 transition-opacity duration-300 pointer-events-none hover:opacity-50">
                    </div>

                    <input type="file" id="pdfFileInput" name="archivo" accept=".pdf"
                        class="absolute inset-0 opacity-0 cursor-pointer z-10">

                    <div class="text-center relative z-20 transition-opacity duration-300">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <p class="text-lg font-semibold text-gray-800">Cargar Nuevo Boletín</p>
                        <p class="text-sm text-gray-600">o arrastra un archivo pdf aquí.</p>
                        <p class="mt-1 text-xs text-gray-500">Tamaño máximo: 50 MB</p>
                        <p id="fileNameDisplay" class="mt-2 text-sm font-medium text-gray-700 hidden">Ningún archivo seleccionado</p> 
                        {{-- fileNameDisplay NO SE USA para mostrar el nombre del archivo cargado. Lo gestiona fileUploadPreview --}}
                    </div>
                </div>

                <div id="fileUploadPreview"
                    class="p-4 mb-6 border border-gray-200 file-upload-preview bg-gray-50 rounded-xl hidden">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 flex-grow min-w-0"> {{-- flex-grow para que el texto ocupe espacio --}}
                            <svg class="w-6 h-6 text-darkblue flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <p id="previewFileName" class="font-medium text-gray-800 truncate"></p> {{-- truncate para nombres largos --}}
                        </div>
                        <button type="button" id="removeSelectedFileButton" class="text-red-500 hover:text-red-700 flex-shrink-0 ml-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="w-full h-2 mt-3 bg-gray-200 rounded-full">
                        <div id="progressBar" class="h-2 bg-green-500 rounded-full transition-all duration-100 ease-linear" style="width: 0%"></div>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                        <span id="previewFileSize" class="text-sm text-gray-500"></span>
                        <p id="progressText" class="text-sm text-right text-gray-600">0%</p>
                    </div>
                </div>
                {{-- Aquí puedes añadir un div para mostrar errores de validación del archivo si lo necesitas --}}
                <div id="archivo_error" class="text-red-500 text-sm mt-1 validation-error-message" data-field="archivo"></div>

            </div> <div id="createBoletinStep2" class="transition-all duration-300 ease-in-out step-2 hidden">
                {{-- Campo Nombre del Boletín --}}
                <div class="mb-4">
                    <label for="bulletinName" class="block mb-2 text-sm font-semibold text-gray-700">Nombre del
                        Boletín</label>
                    <div class="relative">
                        <input type="text" id="bulletinName" name="nombre" maxlength="100"
                            class="w-full px-4 py-2 pr-12 transition-all duration-200 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                            placeholder="Ingresar texto" required>
                        <span id="bulletinNameCharCount"
                            class="absolute text-sm text-gray-500 -translate-y-1/2 right-3 top-1/2">0/100</span>
                    </div>
                    <div id="nombre_error" class="text-red-500 text-sm mt-1 validation-error-message" data-field="nombre"></div>
                </div>

                {{-- Campo Producto --}}
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Producto</label>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="producto" value="cafe" class="hidden peer"
                                id="productoCafeRadio" checked>
                            <div
                                class="flex items-center px-5 py-2 space-x-2 text-gray-700 transition-all duration-300 bg-white border border-gray-300 rounded-full shadow-sm cursor-pointer peer-checked:bg-green-600 peer-checked:text-white hover:bg-gray-100">
                                <span class="text-lg">☕</span>
                                <span class="font-medium">Café</span>
                            </div>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="producto" value="mora" class="hidden peer"
                                id="productoMoraRadio">
                            <div
                                class="flex items-center px-5 py-2 space-x-2 text-gray-700 transition-all duration-300 bg-white border border-gray-300 rounded-full shadow-sm cursor-pointer peer-checked:bg-purple-600 peer-checked:text-white hover:bg-gray-100">
                                <span class="text-lg">🍇</span>
                                <span class="font-medium">Mora</span>
                            </div>
                        </label>
                    </div>
                    <div id="producto_error" class="text-red-500 text-sm mt-1 validation-error-message" data-field="producto"></div>
                </div>

                {{-- Campo Descripción --}}
                <div class="mb-6">
                    <label for="bulletinDescription"
                        class="block mb-2 text-sm font-semibold text-gray-700">Descripción</label>
                    <div class="relative">
                        <textarea id="bulletinDescription" name="descripcion" maxlength="500" rows="3"
                            class="w-full px-4 py-2 pr-12 transition-all duration-200 border border-gray-300 resize-y rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                            placeholder="Ej: Semana del 10 al 17 de Abril" required></textarea>
                        <span id="bulletinDescriptionCharCount"
                            class="absolute text-sm text-gray-500 right-3 bottom-2">0/500</span>
                    </div>
                    <div id="descripcion_error" class="text-red-500 text-sm mt-1 validation-error-message" data-field="descripcion"></div>
                </div>

                {{-- Sección de Indicadores (Usando la estructura de Grid de 2x2) --}}
                <div class="mb-6">
                    <h4 class="block mb-4 text-sm font-semibold text-gray-700">Principales indicadores</h4>
                    
                    {{-- Contenedor principal de la cuadrícula 2x2 --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4"> 

                        {{-- Columna 1, Fila 1: Precio Más Alto (con su icono) --}}
                        <div>
                            <label for="precioMasAlto" class="block text-sm font-bold text-gray-700">Precio Más Alto</label>
                            <div class="flex items-center gap-2 mt-1">
                                <img src="{{ asset('images/subir.svg') }}" alt="Icono subir" class="w-6 h-6 flex-shrink-0">
                                <input type="number" step="0.01" name="precio_mas_alto"
                                    id="precioMasAlto"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    placeholder="Ingresar precio">
                            </div>
                            <div id="precio_mas_alto_error" class="text-red-500 text-sm mt-1 validation-error-message" data-field="precio_mas_alto"></div>
                        </div>
                        
                        {{-- Columna 2, Fila 1: Lugar Precio Más Alto --}}
                        <div>
                            <label for="lugarPrecioMasAlto" class="block text-sm font-bold text-gray-700">Lugar Precio Más Alto</label>
                            <input type="text" name="lugar_precio_mas_alto"
                                id="lugarPrecioMasAlto"
                                maxlength="255"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="Ingresar lugar">
                            <div id="lugar_precio_mas_alto_error" class="text-red-500 text-sm mt-1 validation-error-message" data-field="lugar_precio_mas_alto"></div>
                        </div>

                        {{-- Columna 1, Fila 2: Precio Más Bajo (con su icono) --}}
                        <div>
                            <label for="precioMasBajo" class="block text-sm font-bold text-gray-700">Precio Más Bajo</label>
                            <div class="flex items-center gap-2 mt-1">
                                <img src="{{ asset('images/bajar.svg') }}" alt="Icono bajar" class="w-6 h-6 flex-shrink-0">
                                <input type="number" step="0.01" name="precio_mas_bajo"
                                    id="precioMasBajo"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    placeholder="Ingresar precio">
                            </div>
                            <div id="precio_mas_bajo_error" class="text-red-500 text-sm mt-1 validation-error-message" data-field="precio_mas_bajo"></div>
                        </div>
                        
                        {{-- Columna 2, Fila 2: Lugar Precio Más Bajo --}}
                        <div>
                            <label for="lugarPrecioMasBajo" class="block text-sm font-bold text-gray-700">Lugar Precio Más Bajo</label>
                            <input type="text" name="lugar_precio_mas_bajo"
                                id="lugarPrecioMasBajo"
                                maxlength="255"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="Ingresar lugar">
                            <div id="lugar_precio_mas_bajo_error" class="text-red-500 text-sm mt-1 validation-error-message" data-field="lugar_precio_mas_bajo"></div>
                        </div>

                    </div> {{-- Fin del grid principal de indicadores --}}
                </div>
            </div>

            {{-- Botones de acción del formulario --}}
            <div class="flex justify-end mt-8">
                <button type="button" id="cancelCreateModalButton"
                    class="relative z-50 px-6 py-2.5 text-white rounded-full bg-[var(--color-textmarca)] hover:bg-[var(--color-texthovermarca)] shadow-md transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-darkblue">
                    Cancelar
                </button>

                <button type="submit" id="submitCreateBoletinButton"
                    class="px-6 py-2.5 text-white rounded-full ml-auto bg-[var(--color-sgt)] hover:bg-[var(--color-hoversgt)]shadow-md transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 hidden">
                    Subir Boletín
                </button>
            </div>
        </form>
    </div>
</div>
