{{-- El modal solo se mostrará si el usuario tiene permiso para crear o importar boletines --}}
@canany(['create', 'import'], App\Models\Boletin::class)
    <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        style="display: none;">
        <div class="w-full max-w-2xl p-6 rounded-2xl shadow-lg bg-[var(--color-gris2)]" x-data="uploadForm()"
            x-ref="uploadForm">
            <h3 class="mb-4 text-xl font-semibold">Crear Boletín o Importar desde PDF</h3>

            <!-- Crear boletín e importar PDF -->
            <form action="{{ route('boletines.importarPdf') }}" method="POST" enctype="multipart/form-data"
                @submit.prevent="uploadFile">
                @csrf

                <div class="mb-4 bg-white">
                    <label class="block font-semibold ">Importar archivo PDF</label>
                    <input type="file" name="archivo" accept=".pdf" @change="handleFileChange($event)" />

                    <!-- Vista previa y barra de progreso -->
                    <template x-if="file">
                        <div class="p-3 mt-3 bg-gray-100 rounded">
                            <p class="font-medium" x-text="file.name"></p>
                            <p class="text-sm text-gray-500" x-text="(file.size / (1024 * 1024)).toFixed(2) + ' MB'"></p>

                            <div class="w-full h-2 mt-2 bg-gray-100 rounded">
                                <div class="h-2 rounded bg-[var(--color-iconos4)]" :style="'width: ' + progress + '%'">
                                </div>
                            </div>

                            <p class="mt-1 text-sm text-gray-600" x-text="progress + '%'"></p>
                        </div>
                    </template>
                </div>

                <div class="mb-4">
                    <label class="block font-semibold">Producto</label>
                    <div class="flex gap-4 mt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="producto" value="cafe" x-model="producto" class="hidden">
                            <span
                                :class="producto === 'cafe' ? 'bg-[var(--color-iconos5)] text-white' :
                                    'bg-gray-200 text-black'"
                                class="px-4 py-2 transition-all rounded-full">☕ Café</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="producto" value="mora" x-model="producto" class="hidden">
                            <span :class="producto === 'mora' ? 'bg-purple-700 text-white' : 'bg-gray-200 text-black'"
                                class="px-4 py-2 transition-all rounded-full">🍇 Mora</span>
                        </label>
                    </div>
                </div>


                <div class="mb-4">
                    <label class="block font-semibold">Descripción del boletín</label>
                    <textarea name="contenido" required class="w-full px-3 py-2 border rounded"
                        placeholder="Ej: Boletín de mora - semana 3"></textarea>
                </div>

                <button type="submit"
                    class="px-3 py-2 text-white rounded bg-[var(--color-iconos4)] hover:bg-[var(--color-iconos4)]">
                    Subir Boletín
                </button>

                <button @click="open = false"
                    class="px-4 py-2 mt-4 text-white bg-[var(--color-iconos)] rounded hover:bg-[var(--color-iconos6)]">
                    Cancelar
                </button>
            </form>
        </div>
    </div>
@endcanany

<script>
    function uploadForm() {
        return {
            file: null,
            progress: 0,
            producto: 'cafe', // valor por defecto
            // Acceso a las variables del modal de éxito/error global
            get globalModalState() {
                // Esto accede al estado de Alpine.js del div en el index.blade.php
                return document.querySelector('[x-data*="showSuccessModal"]')._x_dataStack[0];
            },

            handleFileChange(event) {
                this.file = event.target.files[0];
            },

            uploadFile() {
                if (!this.file) {
                    this.globalModalState.modalMessage = 'Por favor, selecciona un archivo PDF.';
                    this.globalModalState.showErrorModal = true;
                    return;
                }

                const form = this.$refs.uploadForm.querySelector('form');
                const formData = new FormData(form);
                const xhr = new XMLHttpRequest();

                xhr.open("POST", form.action, true);

                xhr.upload.addEventListener("progress", (e) => {
                    if (e.lengthComputable) {
                        this.progress = Math.round((e.loaded / e.total) * 100);
                    }
                });

                xhr.onload = () => {
                    if (xhr.status === 200) {
                        // Oculta el modal de carga/importación
                        this.open = false; // Asume que 'open' controla la visibilidad de tu modal de importación
                        this.progress = 0;
                        this.file = null;

                        // Muestra el modal de éxito con el mensaje adecuado
                        this.globalModalState.modalMessage = 'Boletín creado exitosamente.';
                        this.globalModalState.showSuccessModal = true;

                        setTimeout(() => {
                            window.location.reload();
                        }, 2000); // Recarga después de 2 segundos para dar tiempo al usuario de ver el modal
                    } else {
                        // Parsear la respuesta para obtener un mensaje de error más específico
                        let errorMessage = 'Error al subir el archivo.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            } else if (response.errors) {
                                // Si hay errores de validación de Laravel
                                errorMessage = Object.values(response.errors).flat().join('\n');
                            }
                        } catch (e) {
                            // Si la respuesta no es JSON, se queda con el mensaje genérico
                            console.error("Error parsing XHR error response:", e);
                        }

                        // Oculta el modal de carga/importación
                        this.open = false;

                        // Muestra el modal de error
                        this.globalModalState.modalMessage = errorMessage;
                        this.globalModalState.showErrorModal = true;
                        this.progress = 0;
                        this.file = null;
                        // No recargamos en caso de error, para que el usuario pueda ver el mensaje
                    }
                };

                // Manejo de errores de red (ej. sin conexión)
                xhr.onerror = () => {
                    this.open = false;
                    this.globalModalState.modalMessage = 'Error de red o conexión al servidor.';
                    this.globalModalState.showErrorModal = true;
                    this.progress = 0;
                    this.file = null;
                };

                xhr.send(formData);
            }
        };
    }
</script>
