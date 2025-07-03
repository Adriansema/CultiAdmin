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
                    border-gray-300 border-2 border-dashed cursor-pointer rounded-2xl transition-all duration-300 hover:border-green-500 hover:bg-green-50/50">
                    <div id="dropAreaOverlay"
                        class="absolute inset-0 bg-white opacity-0 transition-opacity duration-300 pointer-events-none hover:opacity-50 ">
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
                        <p id="fileNameDisplay" class="mt-2 text-sm font-medium text-gray-700">Ningún archivo
                            seleccionado</p>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Detalles del Boletín y Vista Previa de Carga -->
            <div id="createBoletinStep2" class="transition-all duration-300 ease-in-out step-2 hidden">
                <div id="fileUploadPreview"
                    class="p-4 mb-6 border border-gray-200 file-upload-preview bg-gray-50 rounded-xl hidden">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-darkblue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <p id="previewFileName" class="font-medium text-gray-800"></p>
                        </div>
                        <span id="previewFileSize" class="text-sm text-gray-500"></span>
                    </div>

                    <div class="w-full h-2 mt-3 bg-gray-200 rounded-full">
                        <div id="progressBar" class="h-2 bg-green-500 rounded-full" style="width: 0%"></div>
                    </div>
                    <p id="progressText" class="mt-1 text-sm text-right text-gray-600">0%</p>
                </div>

                {{-- Campo Nombre del Boletín --}}
                <div class="mb-4">
                    <label for="bulletinName" class="block mb-2 text-sm font-semibold text-gray-700">Nombre del
                        Boletín</label>
                    <div class="relative">
                        <input type="text" id="bulletinName" name="nombre" maxlength="100"
                            class="w-full px-4 py-2 pr-12 transition-all duration-200 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                            placeholder="Ingresar texto" required=""> {{-- Eliminado x-model --}}
                        <span id="bulletinNameCharCount"
                            class="absolute text-sm text-gray-500 -translate-y-1/2 right-3 top-1/2">0/100</span>
                    </div>
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
                </div>

                {{-- Campo Descripción --}}
                <div class="mb-6">
                    <label for="bulletinDescription"
                        class="block mb-2 text-sm font-semibold text-gray-700">Descripción</label>
                    <div class="relative">
                        <textarea id="bulletinDescription" name="descripcion" maxlength="500" rows="3"
                            class="w-full px-4 py-2 pr-12 transition-all duration-200 border border-gray-300 resize-y rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                            placeholder="Ej: Semana del 10 al 17 de Abril" required=""></textarea> {{-- Eliminado x-model --}}
                        <span id="bulletinDescriptionCharCount"
                            class="absolute text-sm text-gray-500 right-3 bottom-2">0/500</span>
                    </div>
                </div>

                {{-- Sección de Indicadores --}}
                <div class="mb-6">
                    <h4 class="block mb-4 text-sm font-semibold text-gray-700">Principales indicadores</h4>
                    <div class="flex items-center mb-4 space-x-3">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                        </svg>
                        <input type="text" id="precioMasAlto" name="precio_mas_alto"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                            placeholder="Ingresar precio" inputmode="decimal"> {{-- Eliminado x-model --}}
                        <input type="text" id="lugarPrecioMasAlto" name="lugar_precio_mas_alto" maxlength="255"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                            placeholder="Ingresar lugar"> {{-- Eliminado x-model --}}
                    </div>
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                        <input type="text" id="precioMasBajo" name="precio_mas_bajo"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                            placeholder="Ingresar precio" inputmode="decimal"> {{-- Eliminado x-model --}}
                        <input type="text" id="lugarPrecioMasBajo" name="lugar_precio_mas_bajo" maxlength="255"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                            placeholder="Ingresar lugar"> {{-- Eliminado x-model --}}
                    </div>
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
                    {{-- Añadido hidden --}}
                    Subir Boletín
                </button>
            </div>
        </form>
    </div>
</div>
