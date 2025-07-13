<div id="createBoletinModal"
    class="fixed inset-0 z-[9999] items-center justify-center bg-black bg-opacity-50 overflow-y-auto hidden" wire:ignore>
    {{-- wire:ignore es importante si usas Livewire en la página padre --}}

    {{-- Contenedor del contenido del modal (el recuadro blanco central) --}}
    <div id="createBoletinModalContent"
        class="w-full max-w-2xl p-6 mx-4 my-8 transition-all duration-300 transform bg-white shadow-lg rounded-2xl"
        role="dialog" aria-modal="true" aria-labelledby="uploadModalTitle" onclick="event.stopPropagation();">
        {{-- ^^^^^^^^^^^^^^^ Agregado role, aria-modal y stopPropagation para accesibilidad y manejo de eventos --}}

        {{-- Encabezado del modal con el botón X --}}
        <div class="flex items-center justify-between pb-4 mb-6">
            <h3 id="uploadModalTitle" class="flex items-center space-x-3 text-2xl font-bold text-gray-800">
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

        <form id="createBoletinForm">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" autocomplete="off">

            <div id="createBoletinStep1" class="transition-all duration-300 ease-in-out step-1">

                {{-- ¡AQUÍ ESTÁ EL ÁREA DE DROP DE ARCHIVOS! --}}
                <div id="fileDropArea"
                    class="relative p-8 bg-white flex flex-col items-center group justify-center
                    border-2 border-dashed border-gray-300 rounded-lg mb-6
                    cursor-pointer hover:border-green-500 transition-colors duration-200 w-full max-w-xl mx-auto">
                    {{-- ^^^^^^^^^^^^^^^ Las clases de estilo y el ID están en el lugar correcto --}}
                    {{-- mx-auto para centrar horizontalmente dentro de su contenedor (createBoletinModalContent) --}}


                    <input type="file" id="pdfFileInput" accept=".pdf" class="hidden">

                    <label for="pdfFileInput" class="cursor-pointer text-gray-600 hover:text-green-700">
                        <img src="{{ asset('images/Importar.svg') }}"class="w-12 h-12 mx-auto mb-3 relative inset-0 block group-hover:hidden"
                            alt="Icono de Importar">
                        <img src="{{ asset('images/Importar-hover.svg') }}"class="w-12 h-12 mx-auto mb-3 relative inset-0 hidden group-hover:block"
                            alt="Icono de importar hover">
                        <p class="font-semibold text-lg">Arrastra tu archivo aquí o haz clic para seleccionar</p>
                        <p class="text-md text-gray-500 mt-1">(Solo archivos .pdf)</p>
                    </label>
                </div>

                <div id="fileUploadPreview"
                    class="p-4 mb-6 border border-gray-200 file-upload-preview bg-gray-50 rounded-xl hidden">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 flex-grow min-w-0">
                            <svg class="w-6 h-6 text-darkblue flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <p id="previewFileName" class="font-medium text-gray-800 truncate"></p>
                        </div>
                        <button type="button" id="removeSelectedFileButton"
                            class="text-red-500 hover:text-red-700 flex-shrink-0 ml-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="w-full h-2 mt-3 bg-gray-200 rounded-full">
                        <div id="progressBar"
                            class="h-2 bg-green-500 rounded-full transition-all duration-100 ease-linear"
                            style="width: 0%"></div>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                        <span id="previewFileSize" class="text-md text-gray-500"></span>
                        <p id="progressText" class="text-md text-right text-gray-600">0%</p>
                    </div>
                </div>
                <div id="archivo_error" class="text-red-500 text-md mt-1 validation-error-message" data-field="archivo">
                </div>

            </div>

            <div id="createBoletinStep2" class="transition-all duration-300 ease-in-out step-2 hidden">
                <div class="mb-4">
                    <label for="bulletinName" class="block mb-2 text-md font-semibold text-gray-700">Nombre del
                        boletín: <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" id="bulletinName" name="nombre" maxlength="100"
                            class="w-full px-4 py-2 pr-12 transition-all duration-200 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                            placeholder="Ingresar texto" required>
                        <span id="bulletinNameCharCount"
                            class="absolute text-md text-gray-500 -translate-y-1/2 right-3 top-1/2">0/100</span>
                    </div>
                    <div id="nombre_error" class="text-red-500 text-md mt-1 validation-error-message"
                        data-field="nombre"></div>
                </div>

                <div class="mb-4">
                    <div class="flex items-center gap-4"> 
                        <label class="block text-md font-semibold text-gray-700 mb-0">
                            Producto: <span class="text-red-500">*</span>
                        </label>
                        <div class="flex flex-wrap gap-4">
                            <label class="flex items-center">
                                <input type="radio" name="producto" value="cafe" class="hidden peer"
                                    id="productoCafeRadio" checked>
                                <div
                                    class="flex items-center px-5 py-2 space-x-2 text-gray-700 transition-all duration-300 bg-white border border-gray-300 rounded-full shadow-md cursor-pointer peer-checked:bg-green-600 peer-checked:text-white hover:bg-gray-100">
                                    <span class="text-lg">☕</span>
                                    <span class="font-medium">Café</span>
                                </div>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="producto" value="mora" class="hidden peer"
                                    id="productoMoraRadio">
                                <div
                                    class="flex items-center px-5 py-2 space-x-2 text-gray-700 transition-all duration-300 bg-white border border-gray-300 rounded-full shadow-md cursor-pointer peer-checked:bg-purple-600 peer-checked:text-white hover:bg-gray-100">
                                    <span class="text-lg">🍇</span>
                                    <span class="font-medium">Mora</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div id="producto_error" class="text-red-500 text-md mt-1 validation-error-message"
                        data-field="producto"></div>
                </div>

                <div class="mb-6">
                    <label for="bulletinDescription"
                        class="block mb-2 text-md font-semibold text-gray-700">Descripción: <span
                            class="text-red-500">*</span></label>
                    <div class="relative">
                        <textarea id="bulletinDescription" name="descripcion" maxlength="500" rows="3"
                            class="w-full px-4 py-2 pr-12 transition-all duration-200 border border-gray-300 resize-y rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                            placeholder="Ej: Semana del 10 al 17 de Abril" required></textarea>
                        <span id="bulletinDescriptionCharCount"
                            class="absolute text-md text-gray-500 right-3 bottom-2">0/500</span>
                    </div>
                    <div id="descripcion_error" class="text-red-500 text-md mt-1 validation-error-message"
                        data-field="descripcion"></div>
                </div>

                <div class="mb-6">
                    <h4 class="block mb-4 text-md font-semibold text-gray-700">Principales indicadores</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">

                        <div>
                            <label for="precioMasAlto" class="block text-md font-bold text-gray-700">Precio más alto:
                                <span class="text-red-500">*</span></label>

                            <div class="flex items-center gap-2 mt-1">
                                <img src="{{ asset('images/alto.svg') }}" alt="Icono subir"
                                    class="w-6 h-6 flex-shrink-0">
                                <input type="text" name="precio_mas_alto" id="precioMasAlto"
                                    class="block w-full rounded-md border-gray-300 shadow-md focus:border-indigo-300 focus:ring
                                    focus:ring-indigo-200 focus:ring-opacity-50 text-right price-input"
                                    placeholder="Ingresar precio">
                            </div>
                            <div id="precio_mas_alto_error" class="text-red-500 text-md mt-1 validation-error-message"
                                data-field="precio_mas_alto"></div>
                        </div>

                        <div>
                            <label for="lugarPrecioMasAlto" class="block text-md font-bold text-gray-700">Lugar precio
                                más alto: <span class="text-red-500">*</span></label>
                            <input type="text" name="lugar_precio_mas_alto" id="lugarPrecioMasAlto"
                                maxlength="255"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-md focus:border-indigo-300 focus:ring
                                focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="Ingresar lugar">
                            <div id="lugar_precio_mas_alto_error"
                                class="text-red-500 text-md mt-1 validation-error-message"
                                data-field="lugar_precio_mas_alto"></div>
                        </div>

                        <div>
                            <label for="precioMasBajo" class="block text-md font-bold text-gray-700">Precio más bajo:
                                <span class="text-red-500">*</span></label>

                            <div class="flex items-center gap-2 mt-1">
                                <img src="{{ asset('images/bajo.svg') }}" alt="Icono bajar"
                                    class="w-6 h-6 flex-shrink-0">
                                <input type="text" name="precio_mas_bajo" id="precioMasBajo"
                                    class="block w-full rounded-md border-gray-300 shadow-md focus:border-indigo-300 focus:ring
                                    focus:ring-indigo-200 focus:ring-opacity-50 text-right price-input"
                                    placeholder="Ingresar precio">
                            </div>
                            <div id="precio_mas_bajo_error" class="text-red-500 text-md mt-1 validation-error-message"
                                data-field="precio_mas_bajo"></div>
                        </div>

                        <div>
                            <label for="lugarPrecioMasBajo" class="block text-md font-bold text-gray-700">Lugar precio
                                más bajo: <span class="text-red-500">*</span></label>
                            <input type="text" name="lugar_precio_mas_bajo" id="lugarPrecioMasBajo"
                                maxlength="255"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-md focus:border-indigo-300 focus:ring
                                focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="Ingresar lugar">
                            <div id="lugar_precio_mas_bajo_error"
                                class="text-red-500 text-md mt-1 validation-error-message"
                                data-field="lugar_precio_mas_bajo"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Botones de acción del formulario --}}
            <div class="flex justify-end mt-8">
                <button type="button" id="cancelCreateModalButton"
                    class="relative z-50 px-6 py-2.5 text-white rounded-full bg-[var(--color-textmarca)] hover:bg-[var(--color-texthovermarca)]
                    shadow-md transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-darkblue">
                    Cancelar
                </button>

                <button type="submit" id="submitCreateBoletinButton"
                    class="px-6 py-2.5 text-white rounded-full ml-auto bg-[var(--color-sgt)] hover:bg-[var(--color-hoversgt)]
                    shadow-md transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 hidden">
                    Subir Boletín
                </button>
            </div>
        </form>
    </div>
</div>
