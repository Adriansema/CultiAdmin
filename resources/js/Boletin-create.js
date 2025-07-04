let createBoletinModal;
let createBoletinModalContent;
let closeCreateModalXButton;
let cancelCreateModalButton;
let pdfFileInput; // Input de tipo file
let createBoletinForm;
let createBoletinStep1; // Contenedor del Paso 1 (Nombre, Descripción, Carga PDF)
let createBoletinStep2; // Contenedor del Paso 2 (Indicadores de Precio)
let fileDropArea; // El div que actúa como área de drop y click para el archivo

// Referencias para la barra de progreso y previsualización (AJUSTADAS)
let fileUploadPreview;      // Contenedor de la barra de progreso y nombre de archivo (el que se muestra/oculta)
let previewFileName;        // El span para el nombre del archivo (antes selectedFileNameDisplay)
let previewFileSizeDisplay; // Texto para el tamaño del archivo en la previsualización.
let progressBar;            // La barra de progreso de HTML.
let progressText;           // El texto de porcentaje de la barra de progreso.
let removeSelectedFileButton; // El botón para quitar el archivo
let intervalIdForSimulation = null; // variable para guardar el ID del intervalo

let bulletinNameInput;
let bulletinNameCharCount;
let bulletinDescriptionInput;
let bulletinDescriptionCharCount;
let submitCreateBoletinButton;

let currentFile = null;
let currentStep = 1; // Controla el paso actual del formulario (1 o 2)
let isDragging = false;


// --- Funciones para manejar el modal ---

/**
 * Abre el modal de creación de boletines.
 * Siempre inicia en el Paso 1.
 */
window.openCreateBoletinModalVanilla = function () {
    console.log('DEBUG: openCreateBoletinModalVanilla llamado.');
    if (createBoletinModal) {
        createBoletinModal.classList.remove('hidden');
        createBoletinModal.classList.add('flex'); // Asegura que el modal se centre
        document.body.style.overflow = 'hidden'; // Bloquea el scroll del body
        resetCreateBoletinForm(); // Resetea el formulario al abrir, llevándolo al Paso 1
        console.log('DEBUG: Modal de creación abierto.');
    } else {
        console.error('ERROR: createBoletinModal no encontrado al intentar abrir.');
    }
};

/**
 * Cierra el modal de creación de boletines por completo.
 * No resetea el formulario, solo lo oculta.
 */
window.closeCreateBoletinModalVanilla = function () {
    console.log('DEBUG: closeCreateBoletinModalVanilla llamado.');
    if (createBoletinModal) {
        createBoletinModal.classList.remove('flex');
        createBoletinModal.classList.add('hidden'); // Oculta el modal
        document.body.style.overflow = ''; // Restaura el scroll del body
        console.log('DEBUG: Modal de creación cerrado.');
    } else {
        console.error('ERROR: createBoletinModal no encontrado al intentar cerrar.');
    }
};

/**
 * Resetea el formulario del modal a su estado inicial (Paso 1).
 * Mantiene el modal abierto.
 */
function resetCreateBoletinForm() {
    console.log('DEBUG: resetCreateBoletinForm llamado.');

    // Detener cualquier simulación de carga en progreso
    if (intervalIdForSimulation) {
        clearInterval(intervalIdForSimulation);
        intervalIdForSimulation = null;
        console.log('DEBUG: Simulación de carga detenida.');
    }

    currentFile = null;
    currentStep = 1; // Asegura que el paso es el 1

    // Resetear campos del formulario
    if (createBoletinForm) {
        createBoletinForm.reset();
    }
    if (pdfFileInput) {
        pdfFileInput.value = ''; // Limpia el input de archivo
    }

    // **Ajuste CRÍTICO para la UI de carga de archivo:**
    // Asegura que el área de drop esté visible y en su estado normal
    if (fileDropArea) {
        fileDropArea.classList.remove('hidden');
        fileDropArea.classList.remove('border-green-500', 'border-2', 'bg-green-50/50'); // Limpia estilos de drag
        fileDropArea.classList.add('border-gray-300'); // Restaura el borde normal
    }
    // Oculta la sección de previsualización/progreso
    if (fileUploadPreview) {
        fileUploadPreview.classList.add('hidden');
    }

    // Limpia los textos y la barra de progreso
    if (previewFileName) { // Usar previewFileName para el nombre del archivo
        previewFileName.textContent = ''; // Limpia el nombre del archivo
    }
    if (progressBar) {
        progressBar.style.width = '0%';
    }
    if (progressText) {
        progressText.textContent = '0%';
    }
    if (previewFileSizeDisplay) {
        previewFileSizeDisplay.textContent = ''; // Limpia el tamaño
    }


    if (bulletinNameCharCount) {
        bulletinNameCharCount.textContent = '0/100';
    }
    if (bulletinDescriptionCharCount) {
        bulletinDescriptionCharCount.textContent = '0/500';
    }

    // Mostrar Paso 1 y ocultar Paso 2
    if (createBoletinStep1) {
        createBoletinStep1.classList.remove('hidden');
    }
    if (createBoletinStep2) {
        createBoletinStep2.classList.add('hidden');
    }
    if (submitCreateBoletinButton) {
        submitCreateBoletinButton.classList.add('hidden'); // Oculta el botón de subir
    }

    // Limpiar clases de validación si las hubiera
    document.querySelectorAll('#createBoletinForm .border-red-500').forEach(el => {
        el.classList.remove('border-red-500');
    });
    document.querySelectorAll('#createBoletinForm .validation-error-message').forEach(el => {
        el.textContent = ''; // Limpia mensajes de error
    });
}

// --- Funciones de manejo de eventos ---

/**
 * Maneja la selección de archivos (input o drop).
 * @param {FileList} fileList - La lista de archivos recibida del evento.
 */
function handleFileChange(fileList) {
    console.log('DEBUG: handleFileChange disparado.');
    console.log('DEBUG: Files received in handleFileChange:', fileList);

    if (fileList && fileList.length > 0) {
        currentFile = fileList[0];
        console.log('DEBUG: Archivo seleccionado:', currentFile.name);

        // **Ajuste para la UI de carga de archivo:**
        // Ocultar el área de drop y mostrar la vista previa del archivo cargado
        if (fileDropArea) fileDropArea.classList.add('hidden');
        if (fileUploadPreview) fileUploadPreview.classList.remove('hidden');

        // Mostrar nombre y tamaño del archivo inmediatamente
        if (previewFileName) { // Usar previewFileName aquí
            previewFileName.textContent = currentFile.name;
        }
        if (previewFileSizeDisplay) {
            previewFileSizeDisplay.textContent = `${(currentFile.size / (1024 * 1024)).toFixed(2)} MB`;
        }

        // Resetear la barra de progreso para la simulación
        if (progressBar) progressBar.style.width = '0%';
        if (progressText) progressText.textContent = '0%';


        // Simular progreso de carga y avanzar al Paso 2
        let simulatedProgress = 0;
        // Guardar el ID del intervalo
        intervalIdForSimulation = setInterval(() => { // <-- Asigna a la nueva variable
            simulatedProgress += 10;
            if (simulatedProgress <= 100) {
                if (progressBar) progressBar.style.width = `${simulatedProgress}%`;
                if (progressText) progressText.textContent = `${simulatedProgress}%`;
            } else {
                clearInterval(intervalIdForSimulation); // Limpia al finalizar naturalmente
                intervalIdForSimulation = null; // Resetea la variable

                // Asegurar que la barra llegue a 100% al finalizar la simulación
                if (progressBar) progressBar.style.width = '100%';
                if (progressText) progressText.textContent = '100%';

                currentStep = 2; // Avanza al Paso 2
                // Transición visual a Paso 2
                if (createBoletinStep1) createBoletinStep1.classList.add('hidden');
                if (createBoletinStep2) createBoletinStep2.classList.remove('hidden');
                if (submitCreateBoletinButton) submitCreateBoletinButton.classList.remove('hidden'); // Muestra el botón de subir

                console.log('DEBUG: Transición a Paso 2. Archivo cargado simuladamente.');
            }
        }, 200);
    } else {
        console.log('DEBUG: No files found in FileList or fileList is null.');
        resetCreateBoletinForm(); // Si no hay archivo, resetear toda la UI
    }
}

/**
 * Maneja el arrastre de archivos sobre el área de carga.
 * @param {Event} event - El evento de arrastre.
 */
function handleDragOver(event) {
    event.preventDefault(); // Previene el comportamiento por defecto (abrir archivo en el navegador)
    isDragging = true;
    console.log('DEBUG: DragOver event fired.');
    if (fileDropArea) {
        fileDropArea.classList.add('border-green-500', 'border-2', 'bg-green-50/50'); // Ajuste de clases
        fileDropArea.classList.remove('border-gray-300'); // Asegura que el borde gris se quita
    } else {
        console.warn('WARNING: fileDropArea is null in handleDragOver.');
    }
}

/**
 * Maneja cuando un archivo sale del área de arrastre.
 * @param {Event} event - El evento de arrastre.
 */
function handleDragLeave(event) {
    isDragging = false;
    console.log('DEBUG: DragLeave event fired.');
    if (fileDropArea) {
        fileDropArea.classList.remove('border-green-500', 'border-2', 'bg-green-50/50'); // Ajuste de clases
        fileDropArea.classList.add('border-gray-300'); // Vuelve a añadir el borde gris
    } else {
        console.warn('WARNING: fileDropArea is null in handleDragLeave.');
    }
}

/**
 * Maneja el soltar de archivos en el área de carga.
 * @param {Event} event - El evento de soltar.
 */
function handleDrop(event) {
    event.preventDefault(); // Previene el comportamiento por defecto (abrir archivo en el navegador)
    isDragging = false;
    console.log('DEBUG: Drop event fired.');
    if (fileDropArea) {
        fileDropArea.classList.remove('border-green-500', 'border-2', 'bg-green-50/50'); // Ajuste de clases
        fileDropArea.classList.add('border-gray-300'); // Vuelve a añadir el borde gris
    } else {
        console.warn('WARNING: fileDropArea is null in handleDrop.');
    }
    handleFileChange(event.dataTransfer.files);
}

/**
 * Maneja el envío del formulario de creación de boletines.
 * @param {Event} event - El evento de envío del formulario.
 */
async function handleCreateBoletinSubmit(event) {
    event.preventDefault();
    console.log('DEBUG: Submit de formulario de creación detectado.');

    if (!currentFile) {
        window.showGlobalMessage('error', 'Por favor, selecciona un archivo PDF.');
        return;
    }

    const formData = new FormData(createBoletinForm);
    formData.append('archivo', currentFile); // Aseguramos que el archivo se añade al FormData

    // Pre-procesamiento de precios: Limpiar y convertir a punto decimal
    let precioMasAltoVal = document.getElementById('precioMasAlto').value;
    let lugarPrecioMasAltoVal = document.getElementById('lugarPrecioMasAlto').value;
    let precioMasBajoVal = document.getElementById('precioMasBajo').value;
    let lugarPrecioMasBajoVal = document.getElementById('lugarPrecioMasBajo').value;

    let cleanedPrecioMasAlto = precioMasAltoVal ? String(precioMasAltoVal).replace(/[^\d.,]/g, '').replace(/,/g, '.') : '';
    let cleanedPrecioMasBajo = precioMasBajoVal ? String(precioMasBajoVal).replace(/[^\d.,]/g, '').replace(/,/g, '.') : '';

    let processedPrecioMasAlto = parseFloat(cleanedPrecioMasAlto) || null;
    let processedPrecioMasBajo = parseFloat(cleanedPrecioMasBajo) || null;

    const hasPrecioAlto = processedPrecioMasAlto !== null;
    const hasLugarAlto = lugarPrecioMasAltoVal.trim() !== '';
    const hasPrecioBajo = processedPrecioMasBajo !== null;
    const hasLugarBajo = lugarPrecioMasBajoVal.trim() !== '';

    if ((hasPrecioAlto && !hasLugarAlto) || (!hasPrecioAlto && hasLugarAlto)) {
        window.showGlobalMessage('error', 'Para el precio más alto, por favor ingresa tanto el precio como el lugar, o déjalos ambos vacíos.');
        // Puedes añadir un div de error específico para esto en tu HTML y mostrarlo aquí
        // document.getElementById('precio_mas_alto_error').textContent = '...'
        return;
    }
    if ((hasPrecioBajo && !hasLugarBajo) || (!hasPrecioBajo && hasLugarBajo)) {
        window.showGlobalMessage('error', 'Para el precio más bajo, por favor ingresa tanto el precio como el lugar, o déjalos ambos vacíos.');
        // document.getElementById('precio_mas_bajo_error').textContent = '...'
        return;
    }

    // Setear los valores procesados en el formData para que sean enviados correctamente
    // Solo si tienen un valor, de lo contrario, no se añaden o se setean a null/vacío según tu backend espere
    if (hasPrecioAlto) {
        formData.set('precio_mas_alto', processedPrecioMasAlto);
    } else {
        formData.delete('precio_mas_alto'); // O setear a un string vacío si el backend lo espera
    }
    if (hasLugarAlto) {
        formData.set('lugar_precio_mas_alto', lugarPrecioMasAltoVal);
    } else {
        formData.delete('lugar_precio_mas_alto');
    }
    if (hasPrecioBajo) {
        formData.set('precio_mas_bajo', processedPrecioMasBajo);
    } else {
        formData.delete('precio_mas_bajo');
    }
    if (hasLugarBajo) {
        formData.set('lugar_precio_mas_bajo', lugarPrecioMasBajoVal);
    } else {
        formData.delete('lugar_precio_mas_bajo');
    }

    // Deshabilitar botón y mostrar spinner
    if (submitCreateBoletinButton) {
        submitCreateBoletinButton.disabled = true;
        // La animación de carga es solo visual, el texto real no cambiará inmediatamente
        submitCreateBoletinButton.innerHTML = `
            <span class="flex items-center justify-center w-full">
                <span>Subiendo...</span>
                <img src="/images/cargando_.svg" alt="Cargando..." class="w-5 h-5 ml-2 animate-spin">
            </span>
        `;
    }

    try {
        const response = await fetch(createBoletinForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        });

        const result = await response.json();

        if (response.ok) {
            console.log('DEBUG: Boletín creado con éxito:', result);
            window.closeCreateBoletinModalVanilla(); // Cierra el modal de creación completamente después de subir

            // Lógica para actualizar la tabla (similar a la de edición)
            const boletinesTableBody = document.getElementById('boletines-table-body');
            if (result.boletin_id && boletinesTableBody) {
                const rowResponse = await fetch(`/boletines/${result.boletin_id}/row-html`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    },
                    credentials: 'include'
                });

                if (rowResponse.ok) {
                    let newRowHtml = await rowResponse.text();
                    newRowHtml = newRowHtml.trim();
                    if (newRowHtml.startsWith('<tr')) {
                        const noBoletinesRow = document.getElementById('no-boletines-row');
                        if (noBoletinesRow) {
                            noBoletinesRow.remove();
                        }
                        boletinesTableBody.insertAdjacentHTML('afterbegin', newRowHtml);
                        if (typeof reindexTableRows === 'function') { // Asegurarse de que la función existe
                            reindexTableRows();
                        }
                        window.showGlobalMessage('success', result.message || 'Boletín creado y tabla actualizada.');
                    } else {
                        console.error('ERROR: El HTML de la fila es inesperado:', newRowHtml);
                        window.showGlobalMessage('error', 'Boletín creado, pero el HTML de la tabla es inesperado. Recargue la página.');
                    }
                } else {
                    console.error('ERROR: No se pudo obtener el HTML de la fila:', await rowResponse.text());
                    window.showGlobalMessage('error', 'Boletín creado, pero no se pudo actualizar la tabla. Recargue la página.');
                }
            } else {
                window.showGlobalMessage('success', result.message || 'Boletín creado con éxito.');
            }

        } else if (response.status === 422) {
            console.error('DEBUG: Errores de validación (422):', result.errors);
            // Mostrar errores de validación en el formulario
            displayCreateFormValidationErrors(result.errors);
            window.showGlobalMessage('error', result.message || 'Por favor, corrige los errores en el formulario.');
        } else {
            console.error('DEBUG: Error en la respuesta del servidor:', result);
            window.showGlobalMessage('error', result.message || 'Ocurrió un error inesperado al crear el boletín.');
        }
    } catch (error) {
        console.error('DEBUG: Error al crear el boletín:', error);
        window.showGlobalMessage('error', 'Error de red o conexión al servidor. Inténtalo de nuevo.');
    } finally {
        if (submitCreateBoletinButton) {
            submitCreateBoletinButton.disabled = false;
            submitCreateBoletinButton.innerHTML = 'Subir Boletín'; // Restaura el texto original
        }
    }
}

/**
 * Muestra los errores de validación en el formulario de creación.
 * @param {object} errors - Objeto de errores de la respuesta del servidor.
 */
function displayCreateFormValidationErrors(errors) {
    // Limpiar errores anteriores
    document.querySelectorAll('#createBoletinForm .border-red-500').forEach(el => {
        el.classList.remove('border-red-500');
    });
    document.querySelectorAll('#createBoletinForm .validation-error-message').forEach(el => {
        el.textContent = '';
    });

    for (const field in errors) {
        const inputField = createBoletinForm.querySelector(`[name="${field}"]`);
        if (inputField) {
            inputField.classList.add('border-red-500');
        }
        // Asume que tienes un div con id="nombre_campo_error" o data-field
        const errorDiv = document.querySelector(`.validation-error-message[data-field="${field}"]`); // Busca por data-field
        if (errorDiv) {
            errorDiv.textContent = errors[field][0];
        }
    }
}


// --- Event Listeners y Inicialización ---

document.addEventListener('DOMContentLoaded', function () {
    console.log('DEBUG: boletin-create-vanilla.js DOMContentLoaded fired.');

    // Obtener referencias a los elementos del DOM (¡Verifica que estos IDs coincidan con tu HTML!)
    createBoletinModal = document.getElementById('createBoletinModal');
    createBoletinModalContent = document.getElementById('createBoletinModalContent');
    closeCreateModalXButton = document.getElementById('closeCreateModalXButton');
    cancelCreateModalButton = document.getElementById('cancelCreateModalButton');
    pdfFileInput = document.getElementById('pdfFileInput'); // <-- Input oculto para seleccionar archivo
    createBoletinForm = document.getElementById('createBoletinForm');
    createBoletinStep1 = document.getElementById('createBoletinStep1');
    createBoletinStep2 = document.getElementById('createBoletinStep2');

    // Elementos de la UI de carga/previsualización
    fileDropArea = document.getElementById('fileDropArea'); // El área grande para drag/click
    fileUploadPreview = document.getElementById('fileUploadPreview'); // El contenedor de la barra de progreso
    previewFileName = document.getElementById('previewFileName'); // El nombre del archivo en la previsualización
    previewFileSizeDisplay = document.getElementById('previewFileSize'); // El tamaño del archivo en la previsualización
    progressBar = document.getElementById('progressBar'); // La barra de progreso visual
    progressText = document.getElementById('progressText'); // El texto del porcentaje
    removeSelectedFileButton = document.getElementById('removeSelectedFileButton'); // El botón "X" para quitar el archivo

    // Elementos de los campos del formulario
    bulletinNameInput = document.getElementById('bulletinName');
    bulletinNameCharCount = document.getElementById('bulletinNameCharCount');
    bulletinDescriptionInput = document.getElementById('bulletinDescription');
    bulletinDescriptionCharCount = document.getElementById('bulletinDescriptionCharCount');
    submitCreateBoletinButton = document.getElementById('submitCreateBoletinButton');


    // Añadir Event Listeners para los botones de cierre
    // ... (Tu lógica para closeCreateModalXButton y cancelCreateModalButton ya es correcta) ...
    if (closeCreateModalXButton) {
        closeCreateModalXButton.addEventListener('click', function () {
            if (currentStep === 2) {
                console.log('DEBUG: Clic en X en Paso 2. Reiniciando a Paso 1.');
                resetCreateBoletinForm();
            } else {
                console.log('DEBUG: Clic en X en Paso 1. Cerrando modal.');
                window.closeCreateBoletinModalVanilla();
            }
        });
    }
    if (cancelCreateModalButton) {
        cancelCreateModalButton.addEventListener('click', function () {
            if (currentStep === 2) {
                console.log('DEBUG: Clic en Cancelar en Paso 2. Reiniciando a Paso 1.');
                resetCreateBoletinForm();
            } else {
                console.log('DEBUG: Clic en Cancelar en Paso 1. Cerrando modal.');
                window.closeCreateBoletinModalVanilla();
            }
        });
    }

    // Listener para el input de tipo file (oculto)
    if (pdfFileInput) {
        pdfFileInput.addEventListener('change', (event) => handleFileChange(event.target.files));
    }

    // Listener para el formulario de envío
    if (createBoletinForm) {
        createBoletinForm.addEventListener('submit', handleCreateBoletinSubmit);
    }

    // Event listeners para el contador de caracteres
    if (bulletinNameInput) {
        bulletinNameInput.addEventListener('input', () => {
            if (bulletinNameCharCount) {
                bulletinNameCharCount.textContent = `${bulletinNameInput.value.length}/100`;
            }
        });
    }
    if (bulletinDescriptionInput) {
        bulletinDescriptionInput.addEventListener('input', () => {
            if (bulletinDescriptionCharCount) {
                bulletinDescriptionCharCount.textContent = `${bulletinDescriptionInput.value.length}/500`;
            }
        });
    }

    // Event listeners para click fuera del modal y tecla Escape
    if (createBoletinModal) {
        createBoletinModal.addEventListener('click', function (event) {
            if (event.target === createBoletinModal) {
                if (currentStep === 2) {
                    console.log('DEBUG: Clic fuera en Paso 2. Reiniciando a Paso 1.');
                    resetCreateBoletinForm();
                } else {
                    console.log('DEBUG: Clic fuera en Paso 1. Cerrando modal.');
                    window.closeCreateBoletinModalVanilla();
                }
            }
        });
    }
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && createBoletinModal && !createBoletinModal.classList.contains('hidden')) {
            if (currentStep === 2) {
                console.log('DEBUG: Tecla Escape en Paso 2. Reiniciando a Paso 1.');
                resetCreateBoletinForm();
            } else {
                console.log('DEBUG: Tecla Escape en Paso 1. Cerrando modal.');
                window.closeCreateBoletinModalVanilla();
            }
        }
    });

    // Event listeners para drag and drop
    if (fileDropArea) {
        fileDropArea.addEventListener('dragover', handleDragOver);
        fileDropArea.addEventListener('dragleave', handleDragLeave);
        fileDropArea.addEventListener('drop', handleDrop);
    }

    // Listener para el botón de eliminar el archivo seleccionado (el "X" en la barra de progreso)
    if (removeSelectedFileButton) {
        removeSelectedFileButton.addEventListener('click', () => {
            console.log('DEBUG: Botón de eliminar archivo clickeado.');
            resetCreateBoletinForm(); // Esto ocultará la barra y mostrará el área de drop
        });
    }

    // Inicializar el formulario en el estado correcto al cargar la página
    resetCreateBoletinForm();
});

// Asegurarse de que la función global para abrir el modal esté disponible
window.openCreateBoletinModal = window.openCreateBoletinModalVanilla;