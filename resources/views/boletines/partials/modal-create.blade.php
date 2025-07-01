{{-- resources/views/boletines/partials/modal-create.blade.php --}}

{{-- Contenedor principal del modal (backdrop y modal en sí) --}}
<div id="createBoletinModal"
    x-data="uploadForm()"
    x-ref="createBoletinModalRef"
    x-show="open"
    @click.outside="closeModal()"
    @keydown.escape.window="closeModal()"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50 overflow-y-auto"
    style="display: none;"
    wire:ignore
    >

    {{-- Contenido del modal (el recuadro blanco) --}}
    <div class="w-full max-w-2xl p-6 mx-4 my-8 transition-all duration-300 transform bg-white shadow-lg rounded-2xl"
        @click.stop
        >

        {{-- Encabezado del Modal --}}
        <div class="flex items-center justify-between pb-4 mb-6">
            <h3 class="flex items-center space-x-3 text-2xl font-bold text-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-darkblue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Subir archivo</span>
            </h3>
            {{-- Botón Cerrar "X" --}}
            <button type="button" @click="closeModal()"
                    class="relative z-50 text-gray-500 transition-colors duration-200 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Mensaje de cabecera --}}
        <p class="mb-6 text-sm text-gray-600">
            Sube un archivo, luego ingresa título y descripción.
        </p>

        {{-- Formulario principal --}}
        <form id="createBoletinForm" @submit.prevent="uploadFile()">
            @csrf

            <!-- STEP 1: Carga de Archivo -->
            <div x-show="currentStep === 1" class="transition-all duration-300 ease-in-out step-1">
                <div class="relative flex flex-col items-center justify-center w-full h-64 p-6 transition-all duration-300 border-gray-300 border-dashed cursor-pointer rounded-2xl"
                    :class="{ 'border-green-500 border-2 bg-green-50': isDragging, 'border-gray-300 border-dashed': !isDragging }"
                    @dragover.prevent="isDragging = true"
                    @dragleave="isDragging = false"
                    @drop.prevent="handleDrop($event)">

                    <input type="file" id="pdfFileInput" name="archivo" accept=".pdf" class="absolute inset-0 opacity-0 cursor-pointer"
                        @change="handleFileChange($event)" x-ref="pdfFileInputRef">

                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <p class="text-lg font-semibold text-gray-800">Cargar Nuevo Boletín</p>
                        <p class="text-sm text-gray-600">o arrastra un archivo pdf aquí.</p>
                        <p class="mt-1 text-xs text-gray-500">Tamaño máximo: 50 MB</p>
                        <p class="mt-2 text-sm font-medium text-gray-700" x-text="file ? file.name : 'Ningún archivo seleccionado'"></p>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Detalles del Boletín y Vista Previa de Carga -->
            <div x-show="currentStep === 2" class="transition-all duration-300 ease-in-out step-2">
                <!-- Vista previa y barra de progreso -->
                <div x-show="file" class="p-4 mb-6 border border-gray-200 file-upload-preview bg-gray-50 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-darkblue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="font-medium text-gray-800" x-text="file ? file.name : ''"></p>
                        </div>
                        <span class="text-sm text-gray-500" x-text="file ? `${(file.size / (1024 * 1024)).toFixed(2)} MB` : ''"></span>
                    </div>

                    <div class="w-full h-2 mt-3 bg-gray-200 rounded-full">
                        <div class="h-2 bg-green-500 rounded-full" :style="`width: ${progress}%`"></div>
                    </div>
                    <p class="mt-1 text-sm text-right text-gray-600" x-text="`${progress}%`"></p>
                </div>

                {{-- Campos del formulario --}}
                <div class="mb-4">
                    <label for="bulletinName" class="block mb-2 text-sm font-semibold text-gray-700">Nombre del Boletín</label>
                    <div class="relative">
                        <input type="text" id="bulletinName" name="nombre" maxlength="100" {{-- ¡CAMBIADO! name="nombre" --}}
                            class="w-full px-4 py-2 pr-12 transition-all duration-200 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                            placeholder="Ingresar texto" required x-model="nombreBoletin">
                        <span class="absolute text-sm text-gray-500 -translate-y-1/2 right-3 top-1/2" x-text="`${nombreBoletin.length}/100`"></span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Producto</label>
                    <div class="flex flex-wrap gap-4">
                        {{-- Opción Café --}}
                        <label class="flex items-center">
                            <input type="radio" name="producto" value="cafe" class="hidden peer" x-model="producto">
                            <div class="flex items-center px-5 py-2 space-x-2 text-gray-700 transition-all duration-300 bg-white border border-gray-300 rounded-full shadow-sm cursor-pointer peer-checked:bg-green-600 peer-checked:text-white hover:bg-gray-100">
                                <span class="text-lg">☕</span>
                                <span class="font-medium">Café</span>
                            </div>
                        </label>

                        {{-- Opción Mora --}}
                        <label class="flex items-center">
                            <input type="radio" name="producto" value="mora" class="hidden peer" x-model="producto">
                            <div class="flex items-center px-5 py-2 space-x-2 text-gray-700 transition-all duration-300 bg-white border border-gray-300 rounded-full shadow-sm cursor-pointer peer-checked:bg-purple-600 peer-checked:text-white hover:bg-gray-100">
                                <span class="text-lg">🍇</span>
                                <span class="font-medium">Mora</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="bulletinDescription" class="block mb-2 text-sm font-semibold text-gray-700">Descripción</label>
                    <div class="relative">
                        <textarea id="bulletinDescription" name="descripcion" maxlength="500" rows="3" {{-- ¡CAMBIADO! name="descripcion" --}}
                            class="w-full px-4 py-2 pr-12 transition-all duration-200 border border-gray-300 resize-y rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                            placeholder="Ej: Semana del 10 al 17 de Abril" required x-model="descripcionBoletin"></textarea>
                        <span class="absolute text-sm text-gray-500 right-3 bottom-2" x-text="`${descripcionBoletin.length}/500`"></span>
                    </div>
                </div>

                {{-- Sección: Principales indicadores --}}
                <div class="mb-6">
                    <h4 class="block mb-4 text-sm font-semibold text-gray-700">Principales indicadores</h4>

                    {{-- Indicador de Precio Más Alto --}}
                    <div class="flex items-center mb-4 space-x-3">
                        {{-- Flecha hacia arriba (verde) --}}
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                        </svg>

                        {{-- Input para Precio Más Alto --}}
                        <input type="text" id="precioMasAlto" name="precio_mas_alto"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                            placeholder="Ingresar precio" x-model="precioMasAlto" inputmode="decimal">

                        {{-- Input para Lugar Precio Más Alto --}}
                        <input type="text" id="lugarPrecioMasAlto" name="lugar_precio_mas_alto" maxlength="255"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                            placeholder="Ingresar lugar" x-model="lugarPrecioMasAlto">
                    </div>

                    {{-- Indicador de Precio Más Bajo --}}
                    <div class="flex items-center space-x-3">
                        {{-- Flecha hacia abajo (roja) --}}
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>

                        {{-- Input para Precio Más Bajo --}}
                        <input type="text" id="precioMasBajo" name="precio_mas_bajo"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                            placeholder="Ingresar precio" x-model="precioMasBajo" inputmode="decimal">

                        {{-- Input para Lugar Precio Más Bajo --}}
                        <input type="text" id="lugarPrecioMasBajo" name="lugar_precio_mas_bajo" maxlength="255"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                            placeholder="Ingresar lugar" x-model="lugarPrecioMasBajo">
                    </div>
                </div>
            </div>

            {{-- Footer del Modal (Botones de acción, SIEMPRE visible) --}}
            <div class="flex justify-end mt-8 space-x-4">
                {{-- BOTÓN CANCELAR (SIEMPRE VISIBLE EN EL FOOTER) --}}
                <button type="button" @click="closeModal()"
                    class="relative z-50 px-6 py-2.5 text-white rounded-full bg-darkblue hover:bg-gray-800 shadow-md transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-darkblue">
                    Cancelar
                </button>

                <button type="submit" class="px-6 py-2.5 text-white rounded-full bg-green-600 hover:bg-green-700 shadow-md transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-green-400"
                    x-show="currentStep === 2">
                    Subir Boletín
                </button>
            </div>
        </form>
    </div>
</div>
