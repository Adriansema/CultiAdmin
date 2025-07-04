// Estado global para el flujo de importación CSV
let importCsvState = {
    // Control de visibilidad de los modales principales del flujo de importación
    uploadModalOpen: false, // Modal de carga de archivo
    previewModalOpen: false, // Modal de previsualización (Rol y Nombre)
    confirmModalOpen: false, // Modal de confirmación final

    // Control de visibilidad de los modales de mensaje de error
    emptyModalOpen: false, // Modal de archivo vacío
    duplicatesModalOpen: false, // Modal de usuarios duplicados
    missingDataModalOpen: false, // Modal de datos faltantes/inválidos

    parsedUsers: [], // Todos los usuarios parseados del CSV
    validUsers: [], // Usuarios que pasaron la validación del frontend y se enviarán al backend
    invalidUsers: [], // Usuarios con errores de validación en el frontend

    // Datos de errores específicos para mostrar en los modales
    duplicateErrors: [], // Errores de duplicados detectados por el backend (tanto tempranos como finales)
    backendValidationErrors: [], // Otros errores de validación detectados por el backend (campos faltantes/invalidos)

    csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
};

// Referencias a los elementos del DOM de todos los modales
const importCsvUploadModal = document.getElementById('importCsvUploadModal');
const closeUploadModalButton = document.getElementById('closeUploadModalButton');
const csvFileInput = document.getElementById('csvFileInput');
const dropArea = importCsvUploadModal ? importCsvUploadModal.querySelector('.border-dashed') : null;

const importCsvPreviewModal = document.getElementById('importCsvPreviewModal');
const closePreviewModalButton = document.getElementById('closePreviewModalButton');
const csvPreviewTableContainer = document.getElementById('csvPreviewTableContainer');
const csvPreviewTableBody = document.getElementById('csvPreviewTableBody');
const detectedUsersCount = document.getElementById('detectedUsersCount');
const previewPrevButton = document.getElementById('previewPrevButton');
const previewNextButton = document.getElementById('previewNextButton');

const importCsvConfirmModal = document.getElementById('importCsvConfirmModal');
const closeConfirmImportModalButton = document.getElementById('closeConfirmImportModalButton');
const importSummaryContent = document.getElementById('importSummaryContent');
const confirmImportPrevButton = document.getElementById('confirmImportPrevButton');
const confirmImportActionButton = document.getElementById('confirmImportActionButton');

const importCsvEmptyModal = document.getElementById('importCsvEmptyModal');
const closeEmptyModalButton = document.getElementById('closeEmptyModalButton');
const returnFromEmptyModalButton = document.getElementById('returnFromEmptyModalButton');

const importCsvDuplicatesModal = document.getElementById('importCsvDuplicatesModal');
const closeDuplicatesModalButton = document.getElementById('closeDuplicatesModalButton');
const returnFromDuplicatesModalButton = document.getElementById('returnFromDuplicatesModalButton');
const duplicatesList = document.getElementById('duplicatesList'); // Este es el ELEMENTO DOM

const importCsvMissingDataModal = document.getElementById('importCsvMissingDataModal');
const closeMissingDataModalButton = document.getElementById('closeMissingDataModalButton');
const returnFromMissingDataModalButton = document.getElementById('returnFromMissingDataModalButton');
const missingDataList = document.getElementById('missingDataList');
const missingDataModalTitle = document.getElementById('missingDataModalTitle'); 
const missingDataDescription = document.getElementById('missingDataDescription');

// Columnas requeridas y sus nombres amigables para los mensajes de error (frontend validation)
const REQUIRED_COLUMNS = {
    name: 'Nombre',
    lastname: 'Apellido',
    email: 'Correo',
    phone: 'Teléfono',
    type_document: 'Tipo de Documento',
    document: 'Documento',
    role: 'Rol'
};

/**
 * Resetea el estado de importación y cierra todos los modales.
 */
function resetImportCsvFlow() {
    console.log('ImportaCsv.js: Reseteando flujo de importación...');
    importCsvState = {
        uploadModalOpen: false,
        previewModalOpen: false,
        confirmModalOpen: false,
        emptyModalOpen: false,
        duplicatesModalOpen: false,
        missingDataModalOpen: false,
        parsedUsers: [],
        validUsers: [],
        invalidUsers: [],
        duplicateErrors: [],
        backendValidationErrors: [],
        csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };

    // Limpiar input file
    if (csvFileInput) csvFileInput.value = '';
    // Limpiar tabla de previsualización
    if (csvPreviewTableBody) csvPreviewTableBody.innerHTML = '';
    if (detectedUsersCount) detectedUsersCount.textContent = '0';
    if (csvPreviewTableContainer) csvPreviewTableContainer.classList.add('hidden'); // Ocultar tabla
    if (importSummaryContent) importSummaryContent.innerHTML = ''; // Limpiar resumen

    updateModalVisibility();
    document.body.classList.remove('overflow-hidden'); // Asegurarse de liberar el scroll del body
    
    // === IMPORTANTE: SE ELIMINÓ LA LÓGICA QUE RE-ABRÍA userFormModal AQUÍ ===
    // La decisión de qué modal abrir después de un reset se maneja en los listeners
    // o en la función que inicia el flujo (ej. openImportCsvModal).
    /* const userFormModal = document.getElementById('userFormModal');
    if (userFormModal) {
        userFormModal.classList.remove('opacity-0', 'pointer-events-none');
        userFormModal.classList.add('opacity-100');
    } */
}

/**
 * Actualiza la visibilidad de todos los modales basados en el estado 'importCsvState'.
 * Solo un modal debe tener su 'isOpen' en true a la vez para evitar sobreposiciones.
 */
function updateModalVisibility() {
    const modals = [
        { element: importCsvUploadModal, isOpen: importCsvState.uploadModalOpen },
        { element: importCsvPreviewModal, isOpen: importCsvState.previewModalOpen },
        { element: importCsvConfirmModal, isOpen: importCsvState.confirmModalOpen },
        { element: importCsvEmptyModal, isOpen: importCsvState.emptyModalOpen },
        { element: importCsvDuplicatesModal, isOpen: importCsvState.duplicatesModalOpen },
        { element: importCsvMissingDataModal, isOpen: importCsvState.missingDataModalOpen },
    ];

    let anyModalOpen = false;
    modals.forEach(modal => {
        if (modal.element) {
            // Remover cualquier listener de transición existente para evitar múltiples ejecuciones
            const existingListener = modal.element._transitionEndListener;
            if (existingListener) {
                modal.element.removeEventListener('transitionend', existingListener);
                modal.element._transitionEndListener = null; // Limpiar la referencia
            }

            if (modal.isOpen) {
                // Abrir modal:
                // 1. Asegurarse de que sea interactivo (quitar pointer-events-none)
                // 2. Establecer opacidad a 100 para iniciar la transición de entrada
                // 3. Asegurarse de que 'flex' esté presente para el centrado.
                //    (Debería estar permanentemente en el HTML del modal para evitar el "disparo").
                modal.element.classList.remove('pointer-events-none');
                modal.element.classList.add('opacity-100');
                anyModalOpen = true;
            } else {
                // Cerrar modal:
                // 1. Establecer opacidad a 0 para iniciar la transición de salida
                modal.element.classList.remove('opacity-100');
                modal.element.classList.add('opacity-0');

                // 2. Añadir pointer-events-none SOLO después de que la transición de opacidad termine.
                //    Esto asegura que el modal mantenga su posición centrada mientras se desvanece.
                const onTransitionEnd = (event) => {
                    // Solo actuar si la transición es de 'opacity'
                    if (event.propertyName === 'opacity') {
                        modal.element.classList.add('pointer-events-none'); // Hacerlo no interactuable
                        modal.element.removeEventListener('transitionend', onTransitionEnd); // Remover el listener
                        modal.element._transitionEndListener = null; // Limpiar la referencia
                    }
                };
                modal.element.addEventListener('transitionend', onTransitionEnd);
                modal.element._transitionEndListener = onTransitionEnd; // Almacenar referencia al listener
            }
        }
    });

    // Controlar el overflow del body
    if (anyModalOpen) {
        document.body.classList.add('overflow-hidden');
    } else {
        document.body.classList.remove('overflow-hidden');
    }
}


// Exporta esta función para que pueda ser llamada desde formulario.js
export function openImportCsvModal() {
    console.log('ImportaCsv.js: openImportCsvModal función llamada (abre modal de carga).');
    resetImportCsvFlow();
    importCsvState.uploadModalOpen = true; // Esto asegura que el modal de carga de archivo se abra.
    updateModalVisibility(); // Esto actualiza la visibilidad de los modales.

    // Asegurarse de que el modal principal de creación de usuario se oculte
    const userFormModal = document.getElementById('userFormModal');
    if (userFormModal) {
        userFormModal.classList.remove('opacity-100');
        userFormModal.classList.add('opacity-0', 'pointer-events-none');
    }
}


/**
 * Función para parsear el archivo CSV usando PapaParse.
 * @param {File} file - El archivo CSV a parsear.
 * @returns {Promise<Array<Object>>} Una promesa que resuelve con los datos parseados.
 */
function parseCsvFile(file) {
    return new Promise((resolve, reject) => {
        Papa.parse(file, {
            header: true,
            skipEmptyLines: true,
            trimHeaders: true,
            complete: function (results) {
                if (results.errors.length > 0) {
                    reject(new Error(`Errores estructurales en CSV: ${results.errors.map(e => e.message).join(', ')}`));
                } else {
                    resolve(results.data);
                }
            },
            error: function (err) {
                reject(err);
            }
        });
    });
}

/**
 * Valida cada fila de usuario parseada del CSV en el frontend.
 * @param {Array<Object>} usersData - Array de objetos parseados del CSV.
 * @returns {{valid: Array<Object>, invalid: Array<{data: Object, errors: Array<string>, lineNumber: number}>}}
 */
function validateCsvRows(usersData) {
    const validUsers = [];
    const invalidUsers = [];

    usersData.forEach((userData, index) => {
        const errors = [];
        const lineNumber = index + 2;

        // Verificar campos requeridos
        for (const key in REQUIRED_COLUMNS) {
            if (userData[key] === undefined || userData[key] === null || String(userData[key]).trim() === '') {
                errors.push(`Falta valor en columna '${REQUIRED_COLUMNS[key]}'`);
            }
        }

        // Validación de formato de email (mínima)
        if (userData.email && !/\S+@\S+\.\S+/.test(userData.email)) {
            errors.push(`Valor no válido en columna 'Correo electrónico'`);
        }

        // Ejemplo: Validar que el campo 'document' solo contenga dígitos
        if (userData.document && !/^\d+$/.test(userData.document)) {
            errors.push(`El documento '${userData.document}' solo debe contener dígitos.`);
        }

        if (errors.length > 0) {
            invalidUsers.push({ data: userData, errors: errors, lineNumber: lineNumber });
        } else {
            validUsers.push(userData);
        }
    });

    return { valid: validUsers, invalid: invalidUsers };
}

/**
 * Realiza una validación temprana de duplicados contactando al backend.
 * @param {Array<Object>} usersData - Los usuarios válidos del frontend para verificar duplicados.
 * @returns {Promise<Array<string>>} Una promesa que resuelve con una lista de errores de duplicados.
 */
async function checkDuplicatesEarly(usersData) {
    console.log('ImportaCsv.js: Realizando validación temprana de duplicados con el backend.');
    const formData = new FormData();
    formData.append('_token', importCsvState.csrfToken);
    formData.append('users_data', JSON.stringify(usersData));

    try {
        const response = await fetch('/usuario/check-duplicates', { // NUEVA RUTA PARA VALIDAR DUPLICADOS
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
            },
        });

        const data = await response.json();
        console.log('Respuesta del backend para duplicados tempranos:', data);

        if (!response.ok) {
            if (data.detailed_errors) {
                const duplicates = [];
                for (const lineNumberKey in data.detailed_errors) {
                    data.detailed_errors[lineNumberKey].forEach(errorMsg => {
                        const displayLineNumber = lineNumberKey.replace('Línea ', '');
                        const cleanErrorMsg = errorMsg.replace(/La fila \d+:\s*/, '');

                        if (cleanErrorMsg.includes('ya existe en el sistema')) {
                            // Intentamos obtener los datos del usuario original parseado para mostrar el email/documento
                            // Nota: lineNumberKey es "Línea X", necesitamos "X" para el índice
                            const originalUserIndex = parseInt(displayLineNumber) - 2; // -2 por index 0 y encabezado
                            const originalUser = importCsvState.parsedUsers[originalUserIndex];

                            let errorDetail = '';
                            if (cleanErrorMsg.includes('El correo electrónico')) {
                                errorDetail = `Correo [${originalUser ? originalUser.email : 'N/A'}]`;
                            } else if (cleanErrorMsg.includes('El número de documento')) {
                                errorDetail = `Número de documento [${originalUser ? originalUser.document : 'N/A'}]`;
                            }
                            duplicates.push(`Fila ${displayLineNumber}: ${errorDetail}`);
                        }
                    });
                }
                return duplicates; // Retornar la lista de duplicados
            }
        }
        return []; // No hay duplicados o no se detectaron en la respuesta
    } catch (error) {
        console.error('Error de red durante la validación temprana de duplicados:', error);
        // Si hay un error de red, lo tratamos como un error general en el flujo principal
        return [`Error de red al verificar duplicados: ${error.message}`];
    }
}


/**
 * Renderiza la tabla de previsualización con solo Rol y Nombre.
 * Corresponde a la Imagen 1 del último set (la tabla dentro del modal).
 * @param {Array<Object>} validData - Usuarios que pasaron la validación del frontend.
 */
function renderCsvPreview(validData) {
    if (!csvPreviewTableBody) {
        console.error("csvPreviewTableBody no encontrado. No se puede renderizar la previsualización.");
        return;
    }

    csvPreviewTableBody.innerHTML = '';

    validData.forEach((row) => {
        const name = row.name || '';
        const lastname = row.lastname || '';
        const role = row.role || 'N/A';

        const rowHtml = `
            <tr class="bg-white hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm font-semibold">${role}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${name} ${lastname}</td>
            </tr>
        `;
        csvPreviewTableBody.insertAdjacentHTML('beforeend', rowHtml);
    });

    if (detectedUsersCount) {
        detectedUsersCount.textContent = validData.length;
    }
    if (csvPreviewTableContainer) {
        csvPreviewTableContainer.classList.remove('hidden');
    }
}

/**
 * Maneja el archivo CSV seleccionado o arrastrado. Orquesta el flujo de modales.
 * @param {File} file - El archivo CSV.
 */
async function handleFileProcessing(file) {
    // Primero, ocultar todos los modales para preparar el escenario
    importCsvState.uploadModalOpen = false;
    importCsvState.previewModalOpen = false;
    importCsvState.confirmModalOpen = false;
    importCsvState.emptyModalOpen = false;
    importCsvState.duplicatesModalOpen = false;
    importCsvState.missingDataModalOpen = false;
    updateModalVisibility(); // Aplicar los cambios de visibilidad inmediatamente

    if (!file) {
        importCsvState.emptyModalOpen = true;
        updateModalVisibility();
        return;
    }

    try {
        const parsedData = await parseCsvFile(file);
        importCsvState.parsedUsers = parsedData;

        // --- Paso 1: Validación Frontend (vacío, campos faltantes/formato inválido) ---
        if (importCsvState.parsedUsers.length === 0) {
            importCsvState.emptyModalOpen = true;
            updateModalVisibility();
            return;
        }

        const validationResult = validateCsvRows(parsedData);
        importCsvState.validUsers = validationResult.valid;
        importCsvState.invalidUsers = validationResult.invalid;

        if (importCsvState.invalidUsers.length > 0) {
            importCsvState.backendValidationErrors = importCsvState.invalidUsers.map(item => ({
                lineNumber: item.lineNumber,
                errors: item.errors
            }));

            // Lógica para determinar el tipo de error predominante
            let missingCount = 0;
            let invalidCount = 0;
            importCsvState.invalidUsers.forEach(userError => {
                userError.errors.forEach(errorMsg => {
                    if (errorMsg.includes('Falta valor')) {
                        missingCount++;
                    } else {
                        invalidCount++;
                    }
                });
            });

            let errorCategory = 'mixtos'; // Por defecto, si hay ambos o no se puede clasificar
            if (missingCount > 0 && invalidCount === 0) {
                errorCategory = 'faltantes';
            } else if (invalidCount > 0 && missingCount === 0) {
                errorCategory = 'invalidos';
            }

            renderMissingDataErrors(importCsvState.backendValidationErrors, errorCategory); // pasar la categoria
            importCsvState.missingDataModalOpen = true;
            updateModalVisibility();
            return;
        }

        // --- Paso 2: Validación de Duplicados Temprana (con el Backend) ---
        // Solo si no hay errores de frontend y hay usuarios válidos
        if (importCsvState.validUsers.length > 0) {
            const earlyDuplicates = await checkDuplicatesEarly(importCsvState.validUsers);
            console.log('Duplicados detectados en checkDuplicatesEarly:', earlyDuplicates); // LOG AÑADIDO

            if (earlyDuplicates && earlyDuplicates.length > 0) {
                importCsvState.duplicateErrors = earlyDuplicates;
                renderDuplicatesErrors(importCsvState.duplicateErrors);
                importCsvState.duplicatesModalOpen = true;
                updateModalVisibility();
                return;
            }
        } else {
            // Esto ocurre si parsedData tiene datos pero todos son inválidos por el frontend.
            // Ya se manejó arriba en invalidUsers.length > 0, pero como fallback.
            importCsvState.emptyModalOpen = true; // Considerar como vacío efectivo si no hay válidos.
            updateModalVisibility();
            return;
        }

        // --- Paso 3: Si todo es válido (frontend y duplicados), mostrar previsualización ---
        renderCsvPreview(importCsvState.validUsers);
        importCsvState.previewModalOpen = true;
        updateModalVisibility();

    } catch (error) {
        console.error('Error general durante el procesamiento del archivo CSV:', error);
        importCsvState.parsedUsers = [];
        importCsvState.validUsers = [];
        importCsvState.invalidUsers = [];
        renderCsvPreview([]);

        importCsvState.backendValidationErrors = [{ lineNumber: 'Procesamiento', errors: [`Error al procesar el archivo: ${error.message}`] }];
        renderMissingDataErrors(importCsvState.backendValidationErrors , 'general'); // Usar 'general' para errores de procesamiento
        importCsvState.missingDataModalOpen = true;
        updateModalVisibility();
    }
}


/**
 * Renderiza la lista de errores de datos faltantes/inválidos en el modal correspondiente.
 * @param {Array<{lineNumber: string|number, errors: Array<string>}>} errorsList
 */
function renderMissingDataErrors(errorsList, errorCategory) {
    let listHtml = '';

    // Actualiza el titulo del modal segun la categoria de error
    if (missingDataModalTitle) {
        if (errorCategory === 'faltantes') {
            missingDataModalTitle.textContent = 'Campos Faltantes';
        } else if (errorCategory === 'invalidos') {
            missingDataModalTitle.textContent = 'Datos Inválidos';
        } else if (errorCategory === 'mixtos') {
            missingDataModalTitle.textContent = 'Datos Faltantes o Inválidos';
        } else {
            missingDataModalTitle.textContent = 'Error en Datos'; // Para errores generales de procesamiento
        }
    }

    // Actualizar el texto descriptivo del modal según la categoría de error
    if (missingDataDescription) {
        if (errorCategory === 'faltantes') {
            missingDataDescription.textContent = 'Se han detectado filas con campos faltantes. Por favor, corrija el CSV o excluya esas filas antes de continuar. Estos son los campos a corregir:';
        } else if (errorCategory === 'invalidos') {
            missingDataDescription.textContent = 'Se han detectado filas con datos inválidos. Por favor, corrija el CSV o excluya esas filas antes de continuar. Estos son los campos a corregir:';
        } else if (errorCategory === 'mixtos') {
            missingDataDescription.textContent = 'Se han detectado filas con campos faltantes o formatos inválidos. Por favor, corrija el CSV o excluya esas filas antes de continuar. Estos son los campos a corregir:';
        } else { // 'general' o cualquier otro caso
            missingDataDescription.textContent = 'Se han detectado errores al procesar el archivo. Por favor, corrija el CSV o excluya esas filas antes de continuar. Estos son los detalles:';
        }
    }

    if (errorsList.length > 0) {
        listHtml = '<ul class="list-disc pl-5 text-gray-800">'; // Clases para estilo de lista
        errorsList.forEach(item => {
            const linePrefix = (item.lineNumber === 'Varias' || item.lineNumber === 'CSV' || item.lineNumber === 'General' || item.lineNumber === 'Procesamiento' || isNaN(parseInt(item.lineNumber))) ? '' : `Fila ${item.lineNumber}: `;
            listHtml += `<li><strong>${linePrefix}</strong> ${item.errors.join('<br>')}</li>`;
        });
        listHtml += '</ul>';
    } else {
        listHtml = '<p class="text-gray-600">No se encontraron errores de datos faltantes/inválidos.</p>';
    }
    if (missingDataList) {
        missingDataList.innerHTML = listHtml;
    }
}

/**
 * Renderiza la lista de errores de usuarios duplicados en el modal correspondiente.
 * @param {Array<string>} errorsArray - Ej: ["Fila 3: Correo [test@example.com]", "Fila 7: Número de documento [12345]"]
 */
function renderDuplicatesErrors(errorsArray) { // Renombrado el parámetro para evitar conflicto
    let listHtml = '';
    if (errorsArray.length > 0) {
        listHtml = '<ul class="list-disc pl-5 text-gray-800">';
        errorsArray.forEach(errorMsg => {
            listHtml += `<li>${errorMsg}</li>`;
        });
        listHtml += '</ul>';
    } else {
        listHtml = '<p class="text-gray-600">No se encontraron usuarios duplicados.</p>';
    }
    if (duplicatesList) { // Referencia al elemento DOM global
        duplicatesList.innerHTML = listHtml;
    }
}


// Event Listeners (DOM Content Loaded)
document.addEventListener('DOMContentLoaded', function () {
    // === LISTENERS PARA LOS BOTONES DEL MODAL DE CARGA (importCsvUploadModal) ===
    if (closeUploadModalButton) {
        closeUploadModalButton.addEventListener('click', resetImportCsvFlow);
    }
    if (csvFileInput) {
        csvFileInput.addEventListener('change', async function (event) {
            const file = event.target.files[0];
            await handleFileProcessing(file);
        });
    }
    if (dropArea) { // Drag and Drop
        dropArea.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropArea.classList.add('border-green-500', 'bg-green-50');
        });
        dropArea.addEventListener('dragleave', (event) => {
            event.preventDefault();
            dropArea.classList.remove('border-green-500', 'bg-green-50');
        });
        dropArea.addEventListener('drop', async (event) => {
            event.preventDefault();
            dropArea.classList.remove('border-green-500', 'bg-green-50');
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                await handleFileProcessing(files[0]);
            }
        });
    }

    // === LISTENERS PARA LOS BOTONES DEL MODAL DE PREVISUALIZACIÓN (importCsvPreviewModal) ===
    if (closePreviewModalButton) {
        closePreviewModalButton.addEventListener('click', resetImportCsvFlow);
    }
    if (previewPrevButton) {
        previewPrevButton.addEventListener('click', function () {
            importCsvState.previewModalOpen = false; // Cierra la previsualización
            importCsvState.uploadModalOpen = true; // Abre el modal de carga
            updateModalVisibility();
        });
    }
    if (previewNextButton) {
        previewNextButton.addEventListener('click', function () {
            if (importCsvState.validUsers.length > 0) {
                importCsvState.previewModalOpen = false; // Cierra la previsualización
                importCsvState.confirmModalOpen = true; // Abre el modal de confirmación
                renderImportSummary(); // Prepara el resumen para la confirmación
                updateModalVisibility();
            } else {
                console.warn('Intentó avanzar sin usuarios válidos en la previsualización.');
            }
        });
    }

    // === LISTENERS PARA LOS BOTONES DEL MODAL DE CONFIRMACIÓN (importCsvConfirmModal) ===
    if (closeConfirmImportModalButton) {
        closeConfirmImportModalButton.addEventListener('click', resetImportCsvFlow);
    }
    if (confirmImportPrevButton) {
        confirmImportPrevButton.addEventListener('click', function () {
            importCsvState.confirmModalOpen = false; // Cierra la confirmación
            importCsvState.previewModalOpen = true; // Abre la previsualización
            updateModalVisibility();
        });
    }
    if (confirmImportActionButton) {
        confirmImportActionButton.addEventListener('click', async function () {
            console.log('ImportaCsv.js: Confirmar importación de usuarios.');
            // Deshabilitar botón y mostrar spinner
            const originalBtnText = confirmImportActionButton.innerHTML;
            confirmImportActionButton.disabled = true;
            confirmImportActionButton.innerHTML = `Importando <img src="/images/cargando_.svg" alt="Cargando..." class="w-5 h-5 ml-2 animate-spin">`;

            const formData = new FormData();
            formData.append('_token', importCsvState.csrfToken);
            formData.append('users_data', JSON.stringify(importCsvState.validUsers));

            try {
                const response = await fetch('/usuario/importar-csv', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();
                console.log('Respuesta del backend (Importación Final):', data); // LOG CLAVE PARA DEBUGGING

                // Restablecer el botón
                confirmImportActionButton.disabled = false;
                confirmImportActionButton.innerHTML = originalBtnText;

                if (!response.ok) {
                    importCsvState.duplicateErrors = [];
                    importCsvState.backendValidationErrors = [];

                    // Ocultar el modal de confirmación
                    importCsvState.confirmModalOpen = false;

                    if (data.detailed_errors) {
                        const newDuplicateErrors = [];
                        const newBackendValidationErrors = [];

                        for (const lineNumberKey in data.detailed_errors) {
                            const lineErrors = data.detailed_errors[lineNumberKey];
                            lineErrors.forEach(errorMsg => {
                                const displayLineNumber = lineNumberKey.replace('Línea ', '');
                                 // La expresión regular ahora incluye el espacio después del número de fila y el ":"
                                const cleanErrorMsg = errorMsg.replace(/La fila \d+:\s*/, '');

                                if (cleanErrorMsg.includes('ya existe en el sistema')) {
                                    // Intentamos obtener los datos del usuario original parseado para mostrar el email/documento
                                    const originalUserIndex = parseInt(displayLineNumber) - 2; // -2 por index 0 y encabezado
                                    const originalUser = importCsvState.parsedUsers[originalUserIndex];

                                    let errorDetail = '';
                                    if (cleanErrorMsg.includes('El correo electrónico')) {
                                        errorDetail = `Correo [${originalUser ? originalUser.email : 'N/A'}]`;
                                    } else if (cleanErrorMsg.includes('El número de documento')) {
                                        errorDetail = `Número de documento [${originalUser ? originalUser.document : 'N/A'}]`;
                                    }
                                    newDuplicateErrors.push(`Fila ${displayLineNumber}: ${errorDetail}`);
                                } else {
                                    newBackendValidationErrors.push({
                                        lineNumber: displayLineNumber,
                                        errors: [cleanErrorMsg]
                                    });
                                }
                            });
                        }

                        if (newDuplicateErrors.length > 0) {
                            importCsvState.duplicateErrors = newDuplicateErrors;
                            renderDuplicatesErrors(importCsvState.duplicateErrors); // <--- Llamada con la lista de duplicados
                            importCsvState.duplicatesModalOpen = true;
                        } else if (newBackendValidationErrors.length > 0) {
                            importCsvState.backendValidationErrors = newBackendValidationErrors;
                            renderMissingDataErrors(importCsvState.backendValidationErrors, 'mixtos');
                            importCsvState.missingDataModalOpen = true;
                        } else {
                            // Fallback para errores no clasificados pero con detailed_errors
                            importCsvState.backendValidationErrors = [{ lineNumber: 'General', errors: [data.message || 'Error desconocido del servidor con datos detallados.'] }];
                            renderMissingDataErrors(importCsvState.backendValidationErrors, 'general');
                            importCsvState.missingDataModalOpen = true;
                        }
                    } else {
                        // Error general del servidor sin detailed_errors
                        importCsvState.backendValidationErrors = [{ lineNumber: 'General', errors: [data.message || 'Error desconocido del servidor.'] }];
                        renderMissingDataErrors(importCsvState.backendValidationErrors, 'general');
                        importCsvState.missingDataModalOpen = true;
                    }
                    updateModalVisibility(); // Asegurar que el modal de error se muestre
                } else {
                    // Éxito: Cerrar todos los modales de importación y recargar la página
                    resetImportCsvFlow();
                    setTimeout(() => {
                        window.location.reload();
                    }, 500); // Recargar rápidamente
                }
            } catch (error) {
                console.error('Error de red o inesperado durante la importación CSV:', error);
                // Restablecer el botón
                confirmImportActionButton.disabled = false;
                confirmImportActionButton.innerHTML = originalBtnText;

                importCsvState.confirmModalOpen = false; // Ocultar modal de confirmación
                importCsvState.backendValidationErrors = [{ lineNumber: 'Red', errors: [`Error de red o del servidor: ${error.message}`] }];
                renderMissingDataErrors(importCsvState.backendValidationErrors);
                importCsvState.missingDataModalOpen = true;
                updateModalVisibility();
            }
        });
    }

    // Función para renderizar el resumen de importación (en importCsvConfirmModal)
    function renderImportSummary() {
        if (!importSummaryContent) return;
        const roleCounts = {};

        importCsvState.validUsers.forEach(user => {
            const role = user.role || 'Sin rol';
            roleCounts[role] = (roleCounts[role] || 0) + 1;
        });

        let summaryHtml = '<p class="text-gray-900 text-lg font-semibold mb-4">Se importarán los siguientes usuarios:</p>';
        summaryHtml += '<ul class="list-disc pl-5 mb-4">';
        for (const role in roleCounts) {
            summaryHtml += `<li class="text-gray-800"><span class="font-bold">${roleCounts[role]}</span> usuario(s) con el rol <span class="font-bold">${role}</span>.</li>`;
        }
        summaryHtml += '</ul>';
        summaryHtml += `<p class="text-gray-900 font-bold mt-4">Total de usuarios a importar: ${importCsvState.validUsers.length}</p>`;

        importSummaryContent.innerHTML = summaryHtml;
    }

    // === LISTENERS PARA LOS BOTONES DE LOS MODALES DE MENSAJE (Regresar / X) ===
    // Todos estos botones regresan al flujo principal abriendo el modal de carga (uploadModal)
    if (closeEmptyModalButton) closeEmptyModalButton.addEventListener('click', openImportCsvModal);
    if (returnFromEmptyModalButton) returnFromEmptyModalButton.addEventListener('click', openImportCsvModal);

    if (closeDuplicatesModalButton) closeDuplicatesModalButton.addEventListener('click', openImportCsvModal);
    if (returnFromDuplicatesModalButton) returnFromDuplicatesModalButton.addEventListener('click', openImportCsvModal);

    if (closeMissingDataModalButton) closeMissingDataModalButton.addEventListener('click', openImportCsvModal);
    if (returnFromMissingDataModalButton) returnFromMissingDataModalButton.addEventListener('click', openImportCsvModal);
});

