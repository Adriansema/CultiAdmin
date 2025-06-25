document.addEventListener('DOMContentLoaded', async function () {
    console.log('*** formulario.js: DOMContentLoaded disparado. ***');

    const userFormModal = document.getElementById('userFormModal');
    const closeModalButton = document.getElementById('closeModalButton');
    const nextButton = document.getElementById('nextStepButton'); // Este es el único botón de navegación principal que tienes.

    const step1Content = document.getElementById('step1Content');
    const step2Content = document.getElementById('step2Content');
    const modalTitle = document.getElementById('modalTitle');
    const successMessageContainer = document.getElementById('successMessage');
    const generalErrorMessageContainer = document.getElementById('generalErrorMessage');
    const step1Indicator = document.getElementById('step1Indicator');
    const step2Indicator = document.getElementById('step2Indicator');
    const form = userFormModal ? userFormModal.querySelector('form') : null;

    // Referencias a los inputs del Paso 1
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const typeDocumentSelect = document.getElementById('type_document');
    const documentInput = document.getElementById('document');

    // Referencias a los inputs del Paso 2 (roles y permisos)
    const roleCheckboxes = document.querySelectorAll('input[name="roles[]"]');
    const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');

    // ELEMENTOS DEL MODAL DE CONFIRMACIÓN
    const confirmModal = document.getElementById('confirmModal');
    const confirmMessageBody = document.getElementById('confirmMessageBody');
    const confirmCancelButton = document.getElementById('confirmCancelButton');
    const confirmActionButton = document.getElementById('confirmActionButton');

    // Verificaciones iniciales de elementos
    if (!userFormModal) { console.error('ERROR: userFormModal no encontrado.'); return; }
    if (!nextButton) console.error('ERROR: nextStepButton no encontrado. ¡Este es crucial!');
    if (!confirmModal) console.error('ERROR: confirmModal no encontrado. Asegúrate de añadir su HTML.');
    if (!confirmMessageBody) console.error('ERROR: confirmMessageBody no encontrado.');
    if (!confirmCancelButton) console.error('ERROR: confirmCancelButton no encontrado.');
    if (!confirmActionButton) console.error('ERROR: confirmActionButton no encontrado.');


    let modalData = {
        isOpen: false,
        isEditMode: false,
        currentStep: 1,
        userId: null,
        nombre: '',
        correo: '',
        tipoDocumento: '',
        numeroDocumento: '',
        rolSeleccionado: '',

        // Estructura de permisos en el frontend (debe coincidir con tus checkboxes)
        permisos: {
            productos: { crear: false, editar: false, validar: false, eliminar: false },
            noticias: { crear: false, editar: false, validar: false, eliminar: false },
            boletines: { crear: false, editar: false, validar: false, eliminar: false },
            usuarios: { crear: false, editar: false }
        },

        // Mapeo de nombres de permiso de Spatie a sus módulos
        // Debe ser EXACTAMENTE como tus permisos de Spatie.
        modulePermissionMap: {
            'productos': ['crear producto', 'editar producto', 'validar producto', 'eliminar producto'],
            'noticias': ['crear noticia', 'editar noticia', 'validar noticia', 'eliminar noticia'],
            'boletines': ['crear boletin', 'editar boletin', 'validar boletin', 'eliminar boletin'],
            'usuarios': ['crear usuario', 'editar usuario']
        },

        // Aquí guardaremos el mapeo de rol a permisos por defecto que viene del backend
        rolePermissionsMapping: {}, // Esto se llenará con fetchRolePermissionsMapping()
        errors: {},
        successMessage: '',
        csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };

    // ! Funciones Auxiliares

    // Carga el mapeo de roles a permisos por defecto una única vez
    async function fetchRolePermissionsMapping() {
        try {
            const response = await fetch('/usuario/role-permissions-map'); 
            const data = await response.json();
            if (response.ok && data.roleDefaultPermissions) {
                modalData.rolePermissionsMapping = data.roleDefaultPermissions;
                console.log('JS: Mapeo de permisos por rol cargado globalmente:', modalData.rolePermissionsMapping);
            } else {
                console.error('Error al cargar mapeo de permisos por rol:', data.message || response.statusText);
            }
        } catch (error) {
            console.error('Error de red al cargar mapeo de permisos por rol:', error);
        }
    }
    // AWAIT la carga del mapeo para asegurar que esté disponible cuando se usen los roles
    await fetchRolePermissionsMapping();


    // Función para restablecer todos los permisos en modalData.permisos a false
    function resetPermissions() {
        for (const moduleKey in modalData.permisos) {
            for (const actionkey in modalData.permisos[moduleKey]) {
                modalData.permisos[moduleKey][actionkey] = false;
            }
        }
    }

    // se utiliza updateModalDataPermission para marcar todos los permisos que el usuario tiene.
    // asegurando que ningún módulo quede excluido.
    function updateModalDataPermission(spatiePerName, setState) {
        for (const moduleKey in modalData.modulePermissionMap) {
            //verifica si el permiso Spatie está en el mapra de este modulo
            if (modalData.modulePermissionMap[moduleKey].includes(spatiePerName)) {
                let actionKey = '';

                //determina la 'actionkey' basada en el nombre del permisos de Spatie
                if (spatiePerName.includes('crear')) {
                    actionKey = 'crear';
                } else if (spatiePerName.includes('editar')) {
                    actionKey = 'editar';
                } else if (spatiePerName.includes('eliminar')) {
                    actionKey = 'eliminar';
                } else if (spatiePerName.includes('validar')) {
                    actionKey = 'validar';
                }
                //agrega mas else if si tienes otras acciones (ej. 'ver')

                // Asegurate de que la accion y el modluo exitesn en modalData.permisos
                if (actionKey && modalData.permisos[moduleKey] && modalData.permisos[moduleKey][actionKey] !== undefined) {
                    modalData.permisos[moduleKey][actionKey] = setState;
                    console.log(`Permiso '${spatiePerName}' mapeado a ${moduleKey}.${actionKey} y establecido a ${setState}`);
                    return true; // Exito en el mapeo
                } else {
                    console.warn(`[Mapeo Fallido] Acción '${actionKey}' o modulo '${moduleKey}' no definido para '${spatiePerName}' en modalData.permisos.`);
                }
            }
        }
        console.warn(`[Mapeo Fallido] Permiso Spatie '${spatiePerName}' no encontrado en modulePermissionMap.`);
        return false; //Fallo en el mapeo
    }

    // cada vez que seleccionas un rol. ¡Esto auto-marca los permisos por defecto! (Punto 3)
    function applyRoleDefaultPermissions(roleName) {
        console.log('JS: Aplicando permisos por defecto para el rol:', roleName);
        resetPermissions(); // Primero desmarca TODOS los permisos actuales

        const defaultPermsForRole = modalData.rolePermissionsMapping[roleName];
        console.log('JS: Permisos por defecto para este rol:', defaultPermsForRole);

        if (defaultPermsForRole && defaultPermsForRole.length > 0) {
            defaultPermsForRole.forEach(permName => {
                updateModalDataPermission(permName, true); // Usa la función auxiliar para marcar
            });
        }
        updateFormValues(); // Actualiza la UI para que los checkboxes se muestren marcados
    }

    // Función para restablecer todos los datos del formulario a su estado inicial
    function resetForm() {
        modalData.currentStep = 1;
        modalData.userId = null;
        modalData.nombre = '';
        modalData.correo = '';
        modalData.tipoDocumento = '';
        modalData.numeroDocumento = '';
        modalData.rolSeleccionado = '';
        resetPermissions(); // Llama a resetPermissions para limpiar los checkboxes
        modalData.errors = {};
        modalData.successMessage = '';

        // Limpiar los inputs del DOM
        if (nameInput) nameInput.value = '';
        if (emailInput) emailInput.value = '';
        if (typeDocumentSelect) typeDocumentSelect.value = '';
        if (documentInput) documentInput.value = '';

        // Resetear visualmente los radio buttons de rol y sus estilos
        roleCheckboxes.forEach(radio => {
            radio.checked = false;
            const label = radio.closest('.role-label');
            const icon = label ? label.querySelector('.role-icon') : null;
            if (label) {
                label.classList.remove('bg-indigo-200', 'text-indigo-800');
                label.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                if (icon) icon.src = icon.src.replace('con_marca.svg', 'sin_marca.svg');
            } else {
                label.classList.remove('bg-indigo-200', 'text-indigo-800');
                label.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                if (icon) icon.src = icon.src.replace('con_marca.svg', 'sin_marca.svg');
            }
        });

        // Es importante llamar a updateFormValues después de resetear modalData
        updateFormValues();
    }
    // ! Fin de Funciones Auxiliares

    function updateModalUI() {
        console.log('JS: updateModalUI llamado. Paso actual:', modalData.currentStep, 'Modo:', modalData.isEditMode ? 'Editar' : 'Crear');

        // Control de visibilidad del modal principal (usando opacity y pointer-events)
        if (modalData.isOpen) {
            userFormModal.classList.remove('opacity-0', 'pointer-events-none');
            userFormModal.classList.add('opacity-100');
            document.body.classList.add('overflow-hidden'); // Para evitar scroll en el body
        } else {
            userFormModal.classList.remove('opacity-100');
            userFormModal.classList.add('opacity-0', 'pointer-events-none');
            document.body.classList.remove('overflow-hidden');
        }

        if (modalTitle) {
            modalTitle.textContent = modalData.isEditMode ? 'Editar Usuario' : 'Registrar Nuevo Usuario';
        }

        if (step1Content) step1Content.classList.toggle('hidden', modalData.currentStep !== 1);
        if (step2Content) step2Content.classList.toggle('hidden', modalData.currentStep !== 2);

        // Actualizar indicadores de paso  (íconos y texto)
        if (step1Indicator && step2Indicator) {
            const imgStep1 = step1Indicator.querySelector('img');
            const spanStep1 = step1Indicator.querySelector('span');
            const imgStep2 = step2Indicator.querySelector('img');
            const spanStep2 = step2Indicator.querySelector('span');

            // --- Lógica para el Paso 1 ---
            if (modalData.currentStep === 1) {
                // Si estamos en el Paso 1: icono activo para el Paso 1, texto gris oscuro (activo)
                imgStep1.src = '/images/paso1_activo.svg';
                spanStep1.classList.remove('text-gray-400'); // Asegura que no tenga gris claro
                spanStep1.classList.add('text-gray-700');   // Activo: gris oscuro
            } else if (modalData.currentStep === 2) {
                // Si estamos en el Paso 2 (Paso 1 ya completado): icono de completado para el Paso 1, texto gris oscuro (completado)
                imgStep1.src = '/images/paso1_completado.svg';
                spanStep1.classList.remove('text-gray-400'); // Asegura que no tenga gris claro
                spanStep1.classList.add('text-gray-700');   // Completado: gris oscuro (según tu indicación de solo gris)
            }

            // --- Lógica para el Paso 2 ---
            if (modalData.currentStep === 2) {
                // Si estamos en el Paso 2: icono activo para el Paso 2, texto gris oscuro (activo)
                imgStep2.src = '/images/paso2_completado.svg'; // Este es el icono de "2 activo" según tu descripción
                spanStep2.classList.remove('text-gray-400'); // Asegura que no tenga gris claro
                spanStep2.classList.add('text-gray-700');   // Activo: gris oscuro
            } else if (modalData.currentStep === 1) {
                // Si estamos en el Paso 1 (Paso 2 está inactivo/pendiente): icono inactivo para el Paso 2, texto gris claro
                imgStep2.src = '/images/paso2_inactivo.svg';
                spanStep2.classList.remove('text-gray-700'); // Asegura que no tenga gris oscuro
                spanStep2.classList.add('text-gray-400');   // Inactivo: gris claro
            }
        }

        // CONTROL DE VISIBILIDAD DE LOS BOTONES DE NAVEGACIÓN (solo nextButton en este caso)
        if (nextButton) {
            // El botón "Siguiente" ahora es el único botón de acción principal.
            // Su texto y comportamiento cambiarán según el paso.
            if (modalData.currentStep === 1) {
                nextButton.classList.remove('hidden');
                nextButton.innerHTML = `Siguiente <img src="/images/siguiente.svg" alt="siguiente" class="w-5 h-6 ml-2">`;
                // El botón "Importar CSV" que mencionaste en tu HTML estaría aquí, si es necesario ocultarlo/mostrarlo
            } else if (modalData.currentStep === 2) {
                nextButton.classList.remove('hidden');
                nextButton.innerHTML = `${modalData.isEditMode ? 'Actualizar' : 'Registrar'} <img src="/images/siguiente.svg" alt="enviar" class="w-5 h-6 ml-2">`; // Puedes cambiar el icono si quieres
            } else {
                nextButton.classList.add('hidden'); // Ocultar si hay más pasos o estados
            }
        }

        renderErrors();
        renderSuccessMessage();
        updateFormValues(); // Llama a esta función para reflejar modalData en la UI
    }

    function renderErrors() {
        document.querySelectorAll('.error-message').forEach(el => el.remove());
        document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));

        if (modalData.errors.general && generalErrorMessageContainer) {
            generalErrorMessageContainer.innerHTML = `<span class="error-message">${modalData.errors.general}</span>`;
            generalErrorMessageContainer.classList.remove('hidden');
        } else if (generalErrorMessageContainer) {
            generalErrorMessageContainer.classList.add('hidden');
        }

        for (const field in modalData.errors) {
            if (field === 'general') continue;

            const inputElement = document.getElementById(field);
            if (inputElement) {
                inputElement.classList.add('border-red-500');
                const errorMessage = document.createElement('p');
                errorMessage.classList.add('text-red-500', 'text-xs', 'mt-1', 'error-message');
                errorMessage.textContent = modalData.errors[field];
                inputElement.parentNode.appendChild(errorMessage);
            } else if (field === 'roles' || field === 'rolSeleccionado') {
                // Asumiendo que tus radio buttons de rol están en un contenedor que podemos apuntar
                const roleContainer = document.querySelector('.rolesContainer');
                if (roleContainer) {
                    const errorMessage = document.createElement('p');
                    errorMessage.classList.add('text-red-500', 'text-xs', 'mt-1', 'w-full', 'error-message');
                    errorMessage.textContent = modalData.errors[field];
                    roleContainer.appendChild(errorMessage);
                } else {
                    console.warn(`Contenedor de rol con ID 'rolesContainer' no encontrado para el campo '${field}'.`);
                }
            }
        }
    }

    function renderSuccessMessage() {
        if (modalData.successMessage && successMessageContainer) {
            successMessageContainer.innerHTML = `<span>${modalData.successMessage}</span>`;
            successMessageContainer.classList.remove('hidden');
        } else if (successMessageContainer) {
            successMessageContainer.classList.add('hidden');
        }
    }

    // Función para actualizar los valores de los inputs y el estado de los checkboxes/radios
    function updateFormValues() {
        // 1. Llenar campos de texto del Paso 1
        if (nameInput) nameInput.value = modalData.nombre;
        if (emailInput) emailInput.value = modalData.correo;
        if (typeDocumentSelect) typeDocumentSelect.value = modalData.tipoDocumento;
        if (documentInput) documentInput.value = modalData.numeroDocumento;

        // 2. Marcar el Radio Button del Rol (Punto 1 y 3)
        roleCheckboxes.forEach(radio => {
            radio.checked = (radio.value === modalData.rolSeleccionado);
            const label = radio.closest('.role-label'); // Asumo que tienes una clase 'role-label' en tu HTML
            const icon = label ? label.querySelector('.role-icon') : null; // Asumo que tienes una clase 'role-icon'

            if (label) {
                if (radio.checked) {
                    label.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    label.classList.add('bg-indigo-200', 'text-indigo-800');
                    if (icon) icon.src = icon.src.replace('sin_marca.svg', 'con_marca.svg'); //iconos de marca
                } else {
                    label.classList.remove('bg-indigo-200', 'text-indigo-800');
                    label.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    if (icon) icon.src = icon.src.replace('con_marca.svg', 'sin_marca.svg');
                }
            }
        });

        // 3. Marcar los Checkboxes de Permisos (Punto 2 y 3)
        permissionCheckboxes.forEach(checkbox => {
            const spatiePermName = checkbox.value; // El valor del checkbox es el nombre del permiso de Spatie

            let isChecked = false;
            // Recorre el modulePermissionMap para determinar si el permiso está marcado en modalData.permisos
            for (const moduleKey in modalData.modulePermissionMap) {
                if (modalData.modulePermissionMap[moduleKey].includes(spatiePermName)) {
                    // determianr la 'actionkey' para acceder a modalData.permisos
                    let actionKey = '';
                    if (spatiePermName.includes('crear')) actionKey = 'crear';
                    else if (spatiePermName.includes('editar')) actionKey = 'editar';
                    else if (spatiePermName.includes('eliminar')) actionKey = 'eliminar';
                    else if (spatiePermName.includes('validar')) actionKey = 'validar';

                    if (actionKey && modalData.permisos[moduleKey] && modalData.permisos[moduleKey][actionKey] !== undefined) {
                        isChecked = modalData.permisos[moduleKey][actionKey]; // Leer el estado de modalData.permisos
                        break; // Salir del bucle de módulos una vez encontrado
                    }
                }
            }
            checkbox.checked = isChecked;
        });
    }

    window.openCreateModal = function () {
        console.log('JS: openCreateModal función llamada.');
        resetForm();
        modalData.isEditMode = false;
        modalData.userId = null; // Asegúrate de que no haya userId de una sesión anterior
        modalData.isOpen = true;  // Esto ahora activará la lógica de 'opacity' en updateModalUI
        modalData.currentStep = 1;
        modalData.successMessage = '';
        modalData.errors = {};
        updateModalUI();
    };

    window.openEditModal = async function (userId) {
        console.log('JS: openEditModal función llamada para userId:', userId);

        resetForm(); // Limpia el formulario antes de cargar nuevos datos
        modalData.isEditMode = true;
        modalData.userId = userId; // Asigna el ID del usuario
        modalData.isOpen = true; // Esto ahora activará la lógica de 'opacity' en updateModalUI
        modalData.currentStep = 1; // Siempre iniciar en el paso 1 para edición
        modalData.successMessage = '';
        modalData.errors = {};
        updateModalUI(); // Muestra el modal (transparente inicialmente)

        try {
            // ¡Ajusta esta URL para que coincida con tu prefijo de ruta en web.php!
            const response = await fetch(`/usuario/usuarios/${userId}/data`);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Error al cargar datos del usuario para edición.');
            }

            //Asignar datos basicos
            modalData.nombre = data.name || '';
            modalData.correo = data.email || '';
            modalData.tipoDocumento = data.type_document || '';
            modalData.numeroDocumento = data.document || '';


            // 2. Asignar el rol del usuario (Punto 1)
            // userRoles es un array, tomamos el primero si existe
            modalData.rolSeleccionado = data.userRoles && data.userRoles.length > 0 ? data.userRoles[0] : '';
            console.log('Rol del usuario cargado:', modalData.rolSeleccionado);


            /* // 3. Almacenar el mapeo de permisos por rol (para Punto 3) AHORA SE COMENTO, PUESTO A QUE SE PUSO UNA NUEVA FUNCION EN EL INICO DE LAS FUNCIONES AUXILIARES
            modalData.rolePermissionsMapping = data.roleDefaultPermissions || {};
            console.log('Mapeo de permisos por rol cargado:', modalData.rolePermissionsMapping); */

            // 4. Resetear todos los permisos a false antes de marcarlos (Punto 2)
            resetPermissions(); // Primero limpiar todos los permisos

            // 5. Marcar los permisos que el usuario YA tiene asignados (Punto 2)
            if (data.allUserGrantedPermissions && data.allUserGrantedPermissions.length > 0) {
                console.log('Permisos individuales del usuario cargados:', data.allUserGrantedPermissions);
                data.allUserGrantedPermissions.forEach(permName => {
                    updateModalDataPermission(permName, true); // Usa la función auxiliar para marcar
                });
            }

            // 6. Actualizar la UI con todos los datos y selecciones
            updateFormValues(); // Esto llenará los campos y marcará el rol y los permisos

        } catch (error) {
            console.error('Error al cargar datos para edición:', error);
            modalData.errors.general = error.message;
            modalData.isOpen = false; // Esto ahora activará la lógica de 'opacity' en updateModalUI para ocultar
            updateModalUI();
        }
    };

    window.closeModal = function () {
        console.log('JS: closeModal función llamada.');
        modalData.isOpen = false; // Esto ahora activará la lógica de 'opacity' en updateModalUI para ocultar
        resetForm();
        modalData.errors = {};
        modalData.successMessage = '';
        updateModalUI();
    };

    // ************* FUNCIÓN PRINCIPAL DE AVANCE / CONFIRMACIÓN *************
    function handleNextAction() {
        console.log('JS: handleNextAction llamado. Paso actual:', modalData.currentStep);
        modalData.errors = {}; // Limpia errores antes de cada acción
        let hasError = false;

        if (modalData.currentStep === 1) {
            // Validaciones del Paso 1
            modalData.nombre = nameInput.value.trim();
            modalData.correo = emailInput.value.trim();
            modalData.tipoDocumento = typeDocumentSelect.value;
            modalData.numeroDocumento = documentInput.value.trim();

            if (!modalData.nombre) { modalData.errors.nombre = 'El nombre es obligatorio.'; hasError = true; }
            if (!modalData.correo || !/\S+@\S+\.\S+/.test(modalData.correo)) { modalData.errors.correo = 'El correo no es válido.'; hasError = true; }
            if (!modalData.tipoDocumento) { modalData.errors.tipoDocumento = 'Debe seleccionar un tipo de documento.'; hasError = true; }
            if (!modalData.numeroDocumento) { modalData.errors.numeroDocumento = 'El número de documento es obligatorio.'; hasError = true; }

            if (!hasError) {
                // Si estamos creando un usuario y los datos del paso 1 son válidos
                if (!modalData.isEditMode) {
                    // ANTES de ir al Paso 2, enviamos los datos básicos para crear el usuario
                    // Esto nos dará un userId para el Paso 2
                    submitStep1ForCreation(); // Nueva función para manejar esta petición
                    return; // Importante: Salir para no avanzar al paso 2 directamente
                } else {
                    // Si es edición o si ya creamos el usuario en un flujo anterior (aunque no lo necesitemos aquí)
                    modalData.currentStep = 2; // Avanza al paso 2
                }
            }
        } else if (modalData.currentStep === 2) {
            // Validaciones del Paso 2 (roles y permisos)
            let selectedRoleFound = false;
            roleCheckboxes.forEach(radio => {
                if (radio.checked) {
                    selectedRoleFound = true;
                    modalData.rolSeleccionado = radio.value; // Asegurarse que esté actualizado
                }
            });

            if (!selectedRoleFound) {
                modalData.errors.rolSeleccionado = 'Debe seleccionar al menos un rol.';
                hasError = true;
            }

            // Si no hay errores en el paso 2, abre el modal de confirmación
            if (!hasError) {
                openConfirmModal();
                return; // Importante: Salir para no llamar a updateModalUI dos veces o avanzar
            }
        }
        updateModalUI(); // Actualiza la UI para reflejar el cambio de paso o mostrar errores
    }

    // Maneja el envío de datos del Paso 1 en modo creación
    async function submitStep1ForCreation() {
        console.log('JS: submitStep1ForCreation llamado.');
        modalData.errors = {};
        updateModalUI();

        const url = '/usuario'; // Tu ruta POST /usuarios
        const method = 'POST';

        const formData = new FormData();
        formData.append('_token', modalData.csrfToken);
        formData.append('name', modalData.nombre);
        formData.append('email', modalData.correo);
        formData.append('type_document', modalData.tipoDocumento);
        formData.append('document', modalData.numeroDocumento);
        formData.append('form_step', 'step1'); // Indicar al backend que es el paso 1

        // Deshabilita el botón Siguiente mientras se envía
        nextButton.disabled = true;
        const originalNextBtnText = nextButton.innerHTML;
        nextButton.innerHTML = `Siguiente <img src="/images/cargando_.svg" alt="Cargando..." class="w-6 h-5 ml-2 animate-spin">`; // Icono animado

        try {
            const response = await fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (!response.ok) {
                if (response.status === 422 && data.errors) {
                    modalData.errors = data.errors;
                } else {
                    modalData.errors.general = data.message || 'Error al crear usuario básico.';
                }
                // No avanzamos, mostramos errores en el Paso 1
                modalData.currentStep = 1;
                updateModalUI();
                return;
            }

            // Éxito: Guardar el user_id retornado por el backend
            modalData.userId = data.user_id;
            console.log('Usuario básico creado. userId:', modalData.userId);

            modalData.currentStep = 2; // Avanzar al paso 2
            modalData.successMessage = data.message; // Mensaje de éxito del backend
            updateModalUI();

        } catch (error) {
            console.error('Error en el envío del Paso 1:', error);
            modalData.errors.general = 'Error de red al crear el usuario básico. Inténtalo de nuevo.';
            modalData.currentStep = 1; // Si falla, quedarse en el paso 1
            updateModalUI();
        } finally {
            nextButton.disabled = false;
            nextButton.innerHTML = originalNextBtnText;
        }
    }

    // *************  FUNCIONES PARA EL MODAL DE CONFIRMACIÓN *************
    function openConfirmModal() {
        console.log('JS: openConfirmModal llamado.');
        confirmModal.classList.remove('hidden');
        confirmModal.classList.add('flex');
        modalData.isOpen = false; // Oculta el modal principal
        updateModalUI(); // Actualiza la UI para ocultar el modal principal

        let rolesHtml = '';
        if (modalData.rolSeleccionado) {
            rolesHtml += `<li>${modalData.rolSeleccionado}</li>`;
        } else {
            rolesHtml += `<li>(Ninguno seleccionado)</li>`;
        }

        const selectedPermissionsSet = new Set(); // ¡USA UN SET PARA ASEGURAR PERMISOS ÚNICOS!
        permissionCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedPermissionsSet.add(checkbox.value); // Añade al Set, ignora duplicados
            }
        });

        let permissionsHtml = '';
        // Convierte el Set de nuevo a un Array para iterar y mostrar
        const selectedPermissionsForConfirm = Array.from(selectedPermissionsSet);

        /* const selectedPermissionsForConfirm = []; // Recopila permisos marcados para la confirmación //SE COMENTA PARA REALIZAR PRUEBAS
        permissionCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedPermissionsForConfirm.push(checkbox.value);
            }
        }); */

        if (selectedPermissionsForConfirm.length > 0) {
            selectedPermissionsForConfirm.forEach(perm => {
                permissionsHtml += `<li>${perm}</li>`;
            });
        } else {
            permissionsHtml += `<li>(Ninguno seleccionado)</li>`;
        }

        confirmMessageBody.innerHTML = `
            <p class="text-gray-700 text-lg mb-4">Estás a punto de ${modalData.isEditMode ? 'actualizar un usuario existente.' : 'crear un nuevo usuario.'}</p>
            <p class="mb-2"><strong>Nombre:</strong> ${modalData.nombre}</p>
            <p class="mb-2"><strong>Correo:</strong> ${modalData.correo}</p>
            <p class="mb-2"><strong>Documento:</strong> ${modalData.tipoDocumento} ${modalData.numeroDocumento}</p>

            <h4 class="font-semibold text-gray-800 mt-4 mb-2">Roles asignados:</h4>
            <ul class="list-disc list-inside text-gray-600">
                ${rolesHtml}
            </ul>

            <h4 class="font-semibold text-gray-800 mt-4 mb-2">Permisos directos asignados:</h4>
            <ul class="list-disc list-inside text-gray-600">
                ${permissionsHtml}
            </ul>

            <p class="text-gray-700 text-lg mt-6 font-semibold">¿Confirmas ${modalData.isEditMode ? 'la actualización' : 'la creación'} de este usuario con estas atribuciones?</p>
        `;
    }

    function closeConfirmModal() {
        console.log('JS: closeConfirmModal llamado.');
        confirmModal.classList.add('hidden'); // Esto está bien para el modal de confirmación
        confirmModal.classList.remove('flex');
        modalData.isOpen = true; // Para asegurar que el modal principal se muestre
        updateModalUI(); // Llama a updateModalUI para que aplique opacity-100 al modal principal
    }

    async function submitFormConfirmed() {
        console.log('JS: submitFormConfirmed llamado. Iniciando envío.');
        closeConfirmModal();
        modalData.errors = {};
        modalData.successMessage = '';
        updateModalUI();

        const actionButton = confirmActionButton;
        const originalBtnText = actionButton.textContent;
        actionButton.disabled = true;
        actionButton.innerHTML = `Siguiente <img src="/images/cargando.svg" alt="Cargando..." class="w-5 h-5 ml-2 animate-spin">`;

        // El método de la petición Fetch siempre será 'POST'
        // porque Laravel interpreta _method='PUT' dentro de un POST.
        const method = 'POST';

        const formData = new FormData();
        formData.append('_token', modalData.csrfToken);
        formData.append('_method', 'PUT'); // ¡Importante! Esto le dice a Laravel que es un PUT.

        // AÑADIR SIEMPRE TODOS LOS DATOS AQUI, independientemente del modo.
        // Esto se debe a que tu UsuarioController@update espera todos estos campos
        // para actualizar el usuario (tanto si es recién creado como si es edición).
        formData.append('name', modalData.nombre);
        formData.append('email', modalData.correo);
        formData.append('type_document', modalData.tipoDocumento);
        formData.append('document', modalData.numeroDocumento);

        // Manejo de Roles 
        if (modalData.rolSeleccionado) {
            formData.append('roles[]', modalData.rolSeleccionado);
        } else {
            // Si no hay rol seleccionado, envía un array vacío explícitamente para que Laravel reciba un array válido
            formData.append('roles[]', ''); // Esto resultará en [''] en el backend, que es manejable por syncRoles
        }

        // Manejo de Permisos (Punto 2 y 3)
        const selectedPermissions = [];
        // Itera sobre los módulos y acciones para construir el array de nombres de permisos. 
        //! LO COMENTE PARA HACER UNA PRUEBA , YA AHORA LA NUEVA LOGICA ES LA DE ABAJO 
        /*  for (const moduleKey in modalData.permisos) {
            for (const actionKey in modalData.permisos[moduleKey]) {
                if (modalData.permisos[moduleKey][actionKey]) {
                    const permName = `${actionKey} ${moduleKey.slice(0, -1)}`; // Ejemplo: 'crear usuario'
                    selectedPermissions.push(permName);
                }
            }
        } */
        // ! ESTA DE AQUÍ
        permissionCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedPermissions.push(checkbox.value); // Los valores ya son los nombres de Spatie
            }
        });

        // Adjunta CADA permiso individualmente para que Laravel reciba un array ('permissions[]')
        if (selectedPermissions.length > 0) {
            selectedPermissions.forEach(permission => {
                formData.append('permissions[]', permission); // Envía cada permiso como un elemento del array
            });
        } else {
            // Si no hay permisos seleccionados, envía un array vacío explícitamente
            formData.append('permissions[]', ''); // Esto resultará en [''] en el backend, manejable por syncPermissions
        }

        let url = ''; // La URL que se usará para el fetch.

        // ! LA COMENTE PARA HACER PRUBAS, AHORA SERA LA DE ABAJO QUE HAY QUE ENSAYAR
        /* if (!modalData.userId) {
            console.error('ERROR: userId no definido para la operación de actualización/finalización del formulario.');
            modalData.errors.general = 'Error interno: No se pudo obtener el ID del usuario para finalizar la operación. Por favor, cancela y vuelve a intentar.';
            userFormModal.classList.remove('opacity-0'); // Asegura visibilidad para el error
            userFormModal.classList.add('opacity-100');
            updateModalUI();
            actionButton.disabled = false;
            actionButton.textContent = originalBtnText;
            return;
        } 
        */

        // ! ESTA DE AQUÍ
        if (!modalData.userId) {
            console.error('ERROR: userId no definido para la operación de actualización/finalización del formulario.');
            modalData.errors.general = 'Error interno: ID de usuario no disponible para finalizar la operación. Por favor, cancela y vuelve a intentar.';
            modalData.isOpen = true; // Asegura visibilidad para el error
            updateModalUI();
            actionButton.disabled = false;
            actionButton.textContent = originalBtnText;
            return;
        }

        // La URL para ambas situaciones (finalizar creación y edición) apunta al método PUT de Laravel:
        url = `/usuario/${modalData.userId}`;
        console.log(`JS: Enviando solicitud de ${modalData.isEditMode ? 'EDICIÓN' : 'FINALIZACIÓN DE CREACIÓN'} a URL: ${url} con método simulado PUT.`);

        try {
            const response = await fetch(url, {
                method: method, // Siempre 'POST' en el fetch
                body: formData,
                headers: {
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (!response.ok) {
                if (response.status === 422 && data.errors) {
                    // Laravel envió errores de validación (422)
                    modalData.errors = data.errors; // Asignamos los errores específicos
                    console.error('Errores de validación del backend:', data.errors);
                    // También loguea los errores de cada campo si existen
                    for (const key in data.errors) {
                        if (data.errors.hasOwnProperty(key)) {
                            console.error(`Campo '${key}':`, data.errors[key]);
                        }
                    }

                } else {
                    // Otros errores del servidor (ej. 403, 500)
                    modalData.errors.general = data.message || `Error ${response.status}: ${response.statusText || 'Desconocido'}.`;
                    console.error('Error del servidor no 422:', data);
                }
                modalData.isOpen = true; // Asegura que el modal principal esté visible para ver errores
                updateModalUI(); // Actualiza la UI para mostrar los errores
                return; // Detener la ejecución para que el usuario vea el error
            }

            // Éxito
            modalData.successMessage = data.message;
            updateModalUI();

            // Tras el éxito, cerrar el modal y recargar la página.
            setTimeout(() => {
                closeModal();
                window.location.reload();
            }, 1500);

        } catch (error) {
            console.error('Error de red o parsing JSON:', error);
            modalData.errors.general = 'Ocurrió un error de red o inesperado. Por favor, inténtalo de nuevo.';
            modalData.isOpen = true; // Asegura visibilidad para el error
            updateModalUI();
        } finally {
            actionButton.disabled = false;
            actionButton.textContent = originalBtnText;
        }
    }

    // --- Event Listeners ---
    document.body.addEventListener('click', function (event) {
        if (event.target.closest('.create-user-button')) {
            openCreateModal();
        } else if (event.target.closest('.edit-user-button')) {
            const userId = event.target.closest('.edit-user-button').dataset.userId;
            if (userId) {
                openEditModal(userId);
            }
        }
    });

    if (closeModalButton) closeModalButton.addEventListener('click', closeModal);

    // ************* nextButton ahora maneja todo el flujo de avance *************
    if (nextButton) {
        nextButton.addEventListener('click', function (event) {
            event.preventDefault();
            handleNextAction(); // Llama a la función que decide si avanza o abre confirmación
        });
    }

    // ************* LISTENERS PARA EL MODAL DE CONFIRMACIÓN *************
    if (confirmCancelButton) confirmCancelButton.addEventListener('click', closeConfirmModal);
    if (confirmActionButton) confirmActionButton.addEventListener('click', submitFormConfirmed);

    // Actualizar `modalData` cuando los inputs cambian
    if (nameInput) nameInput.addEventListener('input', (e) => modalData.nombre = e.target.value);
    if (emailInput) emailInput.addEventListener('input', (e) => modalData.correo = e.target.value);
    if (typeDocumentSelect) typeDocumentSelect.addEventListener('change', (e) => modalData.tipoDocumento = e.target.value);
    if (documentInput) documentInput.addEventListener('input', (e) => modalData.numeroDocumento = e.target.value);

    // Listener para los cambios en los radio buttons de rol (Punto 3)
    roleCheckboxes.forEach(radio => {
        radio.addEventListener('change', function () {
            if (this.checked) { // Solo si este checkbox fue el que se marcó
                modalData.rolSeleccionado = this.value; // Actualiza el rol seleccionado en modalData
                applyRoleDefaultPermissions(modalData.rolSeleccionado); // ¡Aplica los permisos por defecto!
            }
        });
    });

    // Listener para los cambios en los checkboxes de permisos (Punto 2)
    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            // Cuando un checkbox de permiso cambia, actualiza modalData.permisos
            // Necesitas la misma lógica de mapeo para saber a qué propiedad de modalData.permisos corresponde
            updateModalDataPermission(this.value, this.checked);
        });
    });

    // Lógica para el manejo de roles (click en el label para el estilo)
    // Ya lo tienes dentro de updateFormValues, esto es para el evento click en el LABEL
    const roleLabels = document.querySelectorAll('.role-label');
    roleLabels.forEach(label => {
        label.addEventListener('click', function () {
            const radio = this.querySelector('input[name="roles[]"]');
            if (radio && !radio.checked) {
                // Si el radio no estaba marcado, el 'change' listener de arriba lo manejará.
                // Esta lógica es más para la UX de hacer clic en el label.
                radio.checked = true;
                // Disparar manualmente el evento 'change' si el navegador no lo hace automáticamente al cambiar 'checked'
                radio.dispatchEvent(new Event('change'));
            }
        });
    });
});