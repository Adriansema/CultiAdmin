let createBoletinModal;
let createBoletinModalContent;
let closeCreateModalXButton;
let cancelCreateModalButton;
let pdfFileInput;
let createBoletinForm;
let createBoletinStep1;
let createBoletinStep2;
let selectedFileNameDisplay;
let fileUploadPreview;
let previewFileNameDisplay;
let previewFileSizeDisplay;
let progressBar;
let progressText;
let bulletinNameInput;
let bulletinNameCharCount;
let bulletinDescriptionInput;
let bulletinDescriptionCharCount;
let submitCreateBoletinButton;
let fileDropArea;

let currentFile = null;
let currentStep = 1; // Controla el paso actual del formulario (1 o 2)
let isDragging = false;

// --- Funciones para manejar el modal ---

/**
 * Abre el modal de creación de boletines.
 * Siempre inicia en el Paso 1.
 */
window.openCreateBoletinModalVanilla = function() {
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
window.closeCreateBoletinModalVanilla = function() {
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
    currentFile = null;
    currentStep = 1; // Asegura que el paso es el 1

    // Resetear campos del formulario
    if (createBoletinForm) {
        createBoletinForm.reset();
    }
    if (pdfFileInput) {
        pdfFileInput.value = ''; // Limpia el input de archivo
    }
    if (selectedFileNameDisplay) {
        selectedFileNameDisplay.textContent = 'Ningún archivo seleccionado';
    }
    if (bulletinNameCharCount) {
        bulletinNameCharCount.textContent = '0/100';
    }
    if (bulletinDescriptionCharCount) {
        bulletinDescriptionCharCount.textContent = '0/500';
    }
    if (progressBar) {
        progressBar.style.width = '0%';
    }
    if (progressText) {
        progressText.textContent = '0%';
    }
    if (fileUploadPreview) {
        fileUploadPreview.classList.add('hidden'); // Oculta la vista previa
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
    document.querySelectorAll('#createBoletinForm [id$="_error"]').forEach(el => {
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

        if (selectedFileNameDisplay) {
            selectedFileNameDisplay.textContent = currentFile.name;
        }

        // Simular progreso y avanzar al Paso 2
        let simulatedProgress = 0;
        const interval = setInterval(() => {
            simulatedProgress += 10;
            if (simulatedProgress <= 100) {
                if (progressBar) progressBar.style.width = `${simulatedProgress}%`;
                if (progressText) progressText.textContent = `${simulatedProgress}%`;
            } else {
                clearInterval(interval);
                currentStep = 2; // Avanza al Paso 2
                if (createBoletinStep1) createBoletinStep1.classList.add('hidden');
                if (createBoletinStep2) createBoletinStep2.classList.remove('hidden');
                if (submitCreateBoletinButton) submitCreateBoletinButton.classList.remove('hidden'); // Muestra el botón de subir
                
                // Mostrar vista previa del archivo
                if (fileUploadPreview) fileUploadPreview.classList.remove('hidden');
                if (previewFileNameDisplay) previewFileNameDisplay.textContent = currentFile.name;
                if (previewFileSizeDisplay) previewFileSizeDisplay.textContent = `${(currentFile.size / (1024 * 1024)).toFixed(2)} MB`;
                
                console.log('DEBUG: Transición a Paso 2.');
            }
        }, 100);
    } else {
        console.log('DEBUG: No files found in FileList.');
        resetCreateBoletinForm();
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
        console.log('DEBUG: fileDropArea current classes BEFORE add:', fileDropArea.className);
        fileDropArea.classList.add('border-green-500', 'border-2', 'bg-green-50');
        fileDropArea.classList.remove('border-gray-400'); // Asegura que el borde gris se quita
        console.log('DEBUG: fileDropArea classes AFTER add:', fileDropArea.className);
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
        console.log('DEBUG: fileDropArea current classes BEFORE remove:', fileDropArea.className);
        fileDropArea.classList.remove('border-green-500', 'border-2', 'bg-green-50');
        fileDropArea.classList.add('border-gray-400'); // Vuelve a añadir el borde gris
        console.log('DEBUG: fileDropArea classes AFTER remove:', fileDropArea.className);
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
        console.log('DEBUG: fileDropArea current classes BEFORE remove (drop):', fileDropArea.className);
        fileDropArea.classList.remove('border-green-500', 'border-2', 'bg-green-50');
        fileDropArea.classList.add('border-gray-400'); // Vuelve a añadir el borde gris
        console.log('DEBUG: fileDropArea classes AFTER remove (drop):', fileDropArea.className);
    } else {
        console.warn('WARNING: fileDropArea is null in handleDrop.');
    }
    handleFileChange(event.dataTransfer.files); // CAMBIO CLAVE: Pasar event.dataTransfer.files directamente
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
        return;
    }
    if ((hasPrecioBajo && !hasLugarBajo) || (!hasPrecioBajo && hasLugarBajo)) {
        window.showGlobalMessage('error', 'Para el precio más bajo, por favor ingresa tanto el precio como el lugar, o déjalos ambos vacíos.');
        return;
    }

    if (hasPrecioAlto) formData.set('precio_mas_alto', processedPrecioMasAlto);
    if (hasLugarAlto) formData.set('lugar_precio_mas_alto', lugarPrecioMasAltoVal);
    if (hasPrecioBajo) formData.set('precio_mas_bajo', processedPrecioMasBajo);
    if (hasLugarBajo) formData.set('lugar_precio_mas_bajo', lugarPrecioMasBajoVal);


    if (submitCreateBoletinButton) {
        submitCreateBoletinButton.disabled = true;
        submitCreateBoletinButton.textContent = 'Subiendo...';
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
                const rowResponse = await fetch(`/boletin/${result.boletin_id}/row-html`, {
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
            submitCreateBoletinButton.textContent = 'Subir Boletín';
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
    document.querySelectorAll('#createBoletinForm [id$="_error"]').forEach(el => {
        el.textContent = '';
    });

    for (const field in errors) {
        const inputField = createBoletinForm.querySelector(`[name="${field}"]`);
        if (inputField) {
            inputField.classList.add('border-red-500');
        }
        // Asume que tienes un div con id="nombre_campo_error" para cada campo
        const errorDiv = document.getElementById(`${field}_error`);
        if (errorDiv) {
            errorDiv.textContent = errors[field][0];
        }
    }
}


// --- Event Listeners y Inicialización ---

document.addEventListener('DOMContentLoaded', function() {
    console.log('DEBUG: boletin-create-vanilla.js DOMContentLoaded fired.');

    // Obtener referencias a los elementos del DOM
    createBoletinModal = document.getElementById('createBoletinModal');
    createBoletinModalContent = document.getElementById('createBoletinModalContent');
    closeCreateModalXButton = document.getElementById('closeCreateModalXButton');
    cancelCreateModalButton = document.getElementById('cancelCreateModalButton');
    pdfFileInput = document.getElementById('pdfFileInput');
    createBoletinForm = document.getElementById('createBoletinForm');
    createBoletinStep1 = document.getElementById('createBoletinStep1');
    createBoletinStep2 = document.getElementById('createBoletinStep2');
    selectedFileNameDisplay = document.getElementById('selectedFileName');
    fileUploadPreview = document.getElementById('fileUploadPreview');
    previewFileNameDisplay = document.getElementById('previewFileName');
    previewFileSizeDisplay = document.getElementById('previewFileSize');
    progressBar = document.getElementById('progressBar');
    progressText = document.getElementById('progressText');
    bulletinNameInput = document.getElementById('bulletinName');
    bulletinNameCharCount = document.getElementById('bulletinNameCharCount');
    bulletinDescriptionInput = document.getElementById('bulletinDescription');
    bulletinDescriptionCharCount = document.getElementById('bulletinDescriptionCharCount');
    submitCreateBoletinButton = document.getElementById('submitCreateBoletinButton');
    fileDropArea = document.getElementById('fileDropArea');


    // Añadir Event Listeners para los botones de cierre
    if (closeCreateModalXButton) {
        closeCreateModalXButton.addEventListener('click', function() {
            if (currentStep === 2) {
                console.log('DEBUG: Clic en X en Paso 2. Reiniciando a Paso 1.');
                resetCreateBoletinForm(); // Reinicia el formulario al Paso 1, manteniendo el modal abierto
            } else { // currentStep === 1
                console.log('DEBUG: Clic en X en Paso 1. Cerrando modal.');
                window.closeCreateBoletinModalVanilla(); // Cierra el modal completamente
            }
        });
    }
    if (cancelCreateModalButton) {
        cancelCreateModalButton.addEventListener('click', function() {
            if (currentStep === 2) {
                console.log('DEBUG: Clic en Cancelar en Paso 2. Reiniciando a Paso 1.');
                resetCreateBoletinForm(); // Reinicia el formulario al Paso 1, manteniendo el modal abierto
            } else { // currentStep === 1
                console.log('DEBUG: Clic en Cancelar en Paso 1. Cerrando modal.');
                window.closeCreateBoletinModalVanilla(); // Cierra el modal completamente
            }
        });
    }

    // Resto de Event Listeners
    if (pdfFileInput) {
        pdfFileInput.addEventListener('change', (event) => handleFileChange(event.target.files)); // CAMBIO: Pasar event.target.files
    }
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
        createBoletinModal.addEventListener('click', function(event) {
            // Si el clic fue directamente en el fondo del modal (no en su contenido)
            if (event.target === createBoletinModal) {
                // Aplica la misma lógica condicional que los botones de cierre
                if (currentStep === 2) {
                    console.log('DEBUG: Clic fuera en Paso 2. Reiniciando a Paso 1.');
                    resetCreateBoletinForm();
                } else { // currentStep === 1
                    console.log('DEBUG: Clic fuera en Paso 1. Cerrando modal.');
                    window.closeCreateBoletinModalVanilla();
                }
            }
        });
    }
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && createBoletinModal && !createBoletinModal.classList.contains('hidden')) {
            // Aplica la misma lógica condicional que los botones de cierre
            if (currentStep === 2) {
                console.log('DEBUG: Tecla Escape en Paso 2. Reiniciando a Paso 1.');
                resetCreateBoletinForm();
            } else { // currentStep === 1
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

    // Inicializar el formulario en el estado correcto al cargar la página
    resetCreateBoletinForm();
});

// Asegurarse de que la función global para abrir el modal esté disponible
// Esta función será llamada desde el botón "Crear / Importar Boletín" en tu index.blade.php
window.openCreateBoletinModal = window.openCreateBoletinModalVanilla;