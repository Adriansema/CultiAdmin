{{-- resources/views/boletines/partials/modal-create.blade.php (Versión con Alpine.js y wire:ignore) --}}

<div id="createBoletinModal"
    x-data="uploadForm()" {{-- ¡Aquí se conecta el componente Alpine.js! --}}
    x-ref="createBoletinModalRef" {{-- ¡Añadimos un x-ref para poder llamarlo desde el botón! --}}
    x-show="open" {{-- Controla la visibilidad con la propiedad 'open' de Alpine --}}
    @click.outside="closeModal()" {{-- Cierra el modal al hacer click fuera --}}
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    x-transition.opacity {{-- Transición de opacidad para una apertura/cierre suave --}}
    style="display: none;" {{-- Oculto por defecto hasta que Alpine lo inicialice --}}
    wire:ignore {{-- *** ¡CRÍTICO! Esto le dice a Livewire que ignore este div y sus hijos. *** --}}
    >

    <div class="w-full max-w-2xl p-6 transition-all duration-300 transform bg-white shadow-lg rounded-2xl"
        @click.stop {{-- Evita que los clics dentro del modal cierren el modal --}}
        >

        {{-- Encabezado del Modal (común a ambos pasos) --}}
        <div class="flex items-center justify-between pb-4 mb-6">
            <h3 class="flex items-center space-x-3 text-2xl font-bold text-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-darkblue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Subir archivo</span>
            </h3>
            {{-- BOTÓN CERRAR "X" --}}
            <button type="button" @click="closeModal()" {{-- Usa la función de Alpine --}}
                    class="relative z-50 text-gray-500 transition-colors duration-200 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mensaje de cabecera (común) -->
        <p class="mb-6 text-sm text-gray-600">
            Sube un archivo, luego ingresa título y descripción.
        </p>

        <!-- Formulario principal -->
        <form id="createBoletinForm" @submit.prevent="uploadFile()"> {{-- *** ¡ID AGREGADO AQUÍ! *** --}}
            @csrf

            <!-- STEP 1: Carga de Archivo -->
            <div x-show="currentStep === 1" class="transition-all duration-300 ease-in-out step-1">
                <div class="relative flex flex-col items-center justify-center w-full h-64 p-6 transition-all duration-300 border-gray-300 border-dashed cursor-pointer rounded-2xl"
                    :class="{ 'border-green-500 border-2 bg-green-50': isDragging, 'border-gray-300 border-dashed': !isDragging }"
                    @dragover.prevent="isDragging = true"
                    @dragleave="isDragging = false"
                    @drop.prevent="handleDrop($event)">

                    <input type="file" id="pdfFileInput" name="archivo" accept=".pdf" class="absolute inset-0 opacity-0 cursor-pointer"
                        @change="handleFileChange($event)" x-ref="pdfFileInputRef"> {{-- Agregado x-ref --}}

                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <p class="text-lg font-semibold text-gray-800">Cargar Nuevo Boletín</p>
                        <p class="text-sm text-gray-600">o arrastra un archivo pdf aquí.</p>
                        <p class="mt-1 text-xs text-gray-500">Tamaño máximo: 50 MB</p>
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
                        <input type="text" id="bulletinName" name="nombre_boletin" maxlength="100"
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
                        <textarea id="bulletinDescription" name="contenido" maxlength="500" rows="3"
                            class="w-full px-4 py-2 pr-12 transition-all duration-200 border border-gray-300 resize-y rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                            placeholder="Ej: Semana del 10 al 17 de Abril" required x-model="descripcionBoletin"></textarea>
                        <span class="absolute text-sm text-gray-500 right-3 bottom-2" x-text="`${descripcionBoletin.length}/500`"></span>
                    </div>
                </div>
            </div>

            {{-- Footer del Modal (Botones de acción, SIEMPRE visible) --}}
            <div class="flex justify-end mt-8 space-x-4">
                {{-- BOTÓN CANCELAR (SIEMPRE VISIBLE EN EL FOOTER) --}}
                <button type="button" @click="closeModal()" {{-- Usa la función de Alpine --}}
                    class="relative z-50 px-6 py-2.5 text-white rounded-full bg-darkblue hover:bg-gray-800 shadow-md transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-darkblue">
                    Cancelar
                </button>

                <button type="submit" class="px-6 py-2.5 text-white rounded-full bg-green-600 hover:bg-green-700 shadow-md transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-green-400"
                    x-show="currentStep === 2"> {{-- x-show para ocultar en el paso 1 --}}
                    Subir Boletín
                </button>
            </div>
        </form>
    </div>
</div>
