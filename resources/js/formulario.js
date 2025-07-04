// Importamos el nuevo módulo ImportaCsv
import { openImportCsvModal } from './ImportaCsv.js';

document.addEventListener('DOMContentLoaded', async function () {
    console.log('*** formulario.js: DOMContentLoaded disparado. ***');

    const userFormModal = document.getElementById('userFormModal');
    const closeModalButton = document.getElementById('closeModalButton');
    const nextButton = document.getElementById('nextStepButton');
    const prevButton = document.getElementById('prevStepButton');
    const generatePasswordButton = document.getElementById('generatePasswordButton');
    const cancelButton = document.getElementById('cancelButton');
    const importCsvButton = document.getElementById('importCsvButton'); // Referencia al botón Importar CSV

    const step1Content = document.getElementById('step1Content');
    const step2Content = document.getElementById('step2Content');
    const step3Content = document.getElementById('step3Content');

    const modalTitle = document.getElementById('modalTitle');
    const successMessageContainer = document.getElementById('successMessage');
    const generalErrorMessageContainer = document.getElementById('generalErrorMessage');

    const step1Indicator = document.getElementById('step1Indicator');
    const step2Indicator = document.getElementById('step2Indicator');
    const step3Indicator = document.getElementById('step3Indicator');

    // Referencias a los inputs del Paso 1
    const nameInput = document.getElementById('name');
    const lastnameInput = document.getElementById('lastname');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const typeDocumentSelect = document.getElementById('type_document');
    const documentInput = document.getElementById('document');

    // Referencias a los inputs del Paso 2 (roles y permisos)
    const roleCheckboxes = document.querySelectorAll('input[name="roles[]"]');
    const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');

    // Referencias a los inputs del Paso 3 (contraseña)
    const passwordInput = document.getElementById('password');
    const passwordConfirmationInput = document.getElementById('password_confirmation');
    const togglePasswordVisibility = document.getElementById('togglePasswordVisibility');
    const toggleConfirmPasswordVisibility = document.getElementById('toggleConfirmPasswordVisibility');

    // ELEMENTOS DEL MODAL DE CONFIRMACIÓN
    const confirmModal = document.getElementById('confirmModal');
    const confirmMessageBody = document.getElementById('confirmMessageBody');
    const confirmCancelButton = document.getElementById('confirmCancelButton');
    const confirmActionButton = document.getElementById('confirmActionButton');

    // REFERENCIAS A LOS ELEMENTOS DEL MODAL DE NOTIFICACIÓN GLOBAL
    const appNotificationModal = document.getElementById('appNotificationModal');
    const appNotificationIconContainer = document.getElementById('appNotificationIconContainer');
    const appNotificationSuccessIcon = document.getElementById('appNotificationSuccessIcon');
    const appNotificationErrorIcon = document.getElementById('appNotificationErrorIcon');
    const appNotificationText = document.getElementById('appNotificationText');
    const appNotificationCloseButton = document.getElementById('appNotificationCloseButton');

    // Verificaciones iniciales de elementos (importante para depuración)
    if (!userFormModal) { console.error('ERROR: userFormModal no encontrado.'); return; }
    if (!nextButton) console.error('ERROR: nextStepButton no encontrado. ¡Este es crucial!');
    if (!prevButton) console.error('ERROR: prevButton no encontrado.');
    if (!generatePasswordButton) console.error('ERROR: generatePasswordButton no encontrado.');
    if (!importCsvButton) console.error('ERROR: importCsvButton no encontrado.');
    if (!step3Content) console.error('ERROR: step3Content no encontrado.');
    if (!step3Indicator) console.error('ERROR: step3Indicator no encontrado.');
    if (!passwordInput) console.error('ERROR: passwordInput no encontrado.');
    if (!passwordConfirmationInput) console.error('ERROR: passwordConfirmationInput no encontrado.');
    if (!confirmModal) console.error('ERROR: confirmModal no encontrado.');
    if (!confirmMessageBody) console.error('ERROR: confirmMessageBody no encontrado.');
    if (!confirmCancelButton) console.error('ERROR: confirmCancelButton no encontrado.');
    if (!confirmActionButton) console.error('ERROR: confirmActionButton no encontrado.');
    if (!lastnameInput) console.error('ERROR: lastnameInput no encontrado.');
    if (!phoneInput) console.error('ERROR: phoneInput no encontrado.');

    // Verificaciones para el nuevo modal de notificación
    if (!appNotificationModal) console.error('ERROR: appNotificationModal no encontrado.');
    if (!appNotificationIconContainer) console.error('ERROR: appNotificationIconContainer no encontrado.');
    if (!appNotificationSuccessIcon) console.error('ERROR: appNotificationSuccessIcon no encontrado.');
    if (!appNotificationErrorIcon) console.error('ERROR: appNotificationErrorIcon no encontrado.');
    if (!appNotificationText) console.error('ERROR: appNotificationText no encontrado.');
    if (!appNotificationCloseButton) console.error('ERROR: appNotificationCloseButton no encontrado.');


    // Estado global del modal
    let modalData = {
        isOpen: false, // Indica si el modal principal está abierto
        isEditMode: false,
        currentStep: 1,
        userId: null,
        name: '',
        lastname: '',
        email: '',
        phone: '',
        type_document: '',
        document: '',
        selectedRole: '',
        password: '',
        passwordConfirmation: '',

        // Estructura de permisos en el frontend
        permisos: {
            productos: { crear: false, editar: false, validar: false, eliminar: false },
            noticias: { crear: false, editar: false, validar: false, eliminar: false },
            boletines: { crear: false, editar: false, validar: false, eliminar: false },
            usuarios: { crear: false, editar: false }
        },

        // Mapeo de nombres de permiso de Spatie a sus módulos
        modulePermissionMap: {
            'productos': ['crear producto', 'editar producto', 'validar producto', 'eliminar producto'],
            'noticias': ['crear noticia', 'editar noticia', 'validar noticia', 'eliminar noticia'],
            'boletines': ['crear boletin', 'editar boletin', 'validar boletin', 'eliminar boletin'],
            'usuarios': ['crear usuario', 'editar usuario']
        },

        // Mapeo de rol a permisos por defecto que viene del backend
        rolePermissionsMapping: {},
        errors: {},
        successMessage: '',
        csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

        // ESTADOS PARA EL MODAL DE NOTIFICACIÓN GLOBAL
        isAppNotificationModalOpen: false,
        appNotificationMessage: '',
        appNotificationIsSuccess: true, // true para éxito (verde), false para error (rojo)
    };

    // ! Funciones Auxiliares

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
    await fetchRolePermissionsMapping();

    function resetPermissions() {
        for (const moduleKey in modalData.permisos) {
            for (const actionkey in modalData.permisos[moduleKey]) {
                modalData.permisos[moduleKey][actionkey] = false;
            }
        }
    }

    function updateModalDataPermission(spatiePerName, setState) {
        for (const moduleKey in modalData.modulePermissionMap) {
            if (modalData.modulePermissionMap[moduleKey].includes(spatiePerName)) {
                let actionKey = '';
                if (spatiePerName.includes('crear')) {
                    actionKey = 'crear';
                } else if (spatiePerName.includes('editar')) {
                    actionKey = 'editar';
                } else if (spatiePerName.includes('eliminar')) {
                    actionKey = 'eliminar';
                } else if (spatiePerName.includes('validar')) {
                    actionKey = 'validar';
                }

                if (actionKey && modalData.permisos[moduleKey] && modalData.permisos[moduleKey][actionKey] !== undefined) {
                    modalData.permisos[moduleKey][actionKey] = setState;
                    console.log(`Permiso '${spatiePerName}' mapeado a ${moduleKey}.${actionKey} y establecido a ${setState}`);
                    return true;
                } else {
                    console.warn(`[Mapeo Fallido] Acción '${actionKey}' o módulo '${moduleKey}' no definido para '${spatiePerName}' en modalData.permisos.`);
                }
            }
        }
        console.warn(`[Mapeo Fallido] Permiso Spatie '${spatiePerName}' no encontrado en modulePermissionMap.`);
        return false;
    }

    function applyRoleDefaultPermissions(roleName) {
        console.log('JS: Aplicando permisos por defecto para el rol:', roleName);
        resetPermissions();

        const defaultPermsForRole = modalData.rolePermissionsMapping[roleName];
        console.log('JS: Permisos por defecto para este rol:', defaultPermsForRole);

        if (defaultPermsForRole && defaultPermsForRole.length > 0) {
            defaultPermsForRole.forEach(permName => {
                updateModalDataPermission(permName, true);
            });
        }
        updateFormValues();
    }

    function generateRandomPassword() {
        const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+~`|}{[]:;?><,./-=";
        let password = "";
        const length = 12;
        for (let i = 0; i < length; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return password;
    }

    function resetForm() {
        modalData.currentStep = 1;
        modalData.userId = null;
        modalData.name = '';
        modalData.lastname = '';
        modalData.email = '';
        modalData.phone = '';
        modalData.type_document = '';
        modalData.document = '';
        modalData.selectedRole = '';
        modalData.password = '';
        modalData.passwordConfirmation = '';
        resetPermissions();
        modalData.errors = {};
        modalData.successMessage = '';

        if (nameInput) nameInput.value = '';
        if (lastnameInput) lastnameInput.value = '';
        if (emailInput) emailInput.value = '';
        if (phoneInput) phoneInput.value = '';
        if (typeDocumentSelect) typeDocumentSelect.value = '';
        if (documentInput) documentInput.value = '';
        if (passwordInput) passwordInput.value = '';
        if (passwordConfirmationInput) passwordConfirmationInput.value = '';

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

        updateFormValues();
    }
    // ! Fin de Funciones Auxiliares

    /**
     * Actualiza la visibilidad del modal principal de usuario.
     */
    function updateModalVisibility() {
        if (modalData.isOpen) {
            userFormModal.classList.remove('opacity-0', 'pointer-events-none');
            userFormModal.classList.add('opacity-100');
            document.body.classList.add('overflow-hidden');
        } else {
            userFormModal.classList.remove('opacity-100');
            userFormModal.classList.add('opacity-0');
            // Usa transitionend para asegurarse de que pointer-events-none se aplique después de la animación de cierre
            const onTransitionEnd = (event) => {
                if (event.propertyName === 'opacity') {
                    userFormModal.classList.add('pointer-events-none');
                    // Solo remover overflow-hidden si NO hay otro modal abierto (ej. el de notificación global)
                    if (!modalData.isAppNotificationModalOpen) {
                        document.body.classList.remove('overflow-hidden');
                    }
                    userFormModal.removeEventListener('transitionend', onTransitionEnd);
                }
            };
            userFormModal.addEventListener('transitionend', onTransitionEnd);
        }
    }

    /**
     * Gestiona la visibilidad y el contenido del modal de notificación global.
     */
    function updateAppNotificationModalVisibility() {
        if (appNotificationModal) {
            if (modalData.isAppNotificationModalOpen) {
                appNotificationModal.classList.remove('hidden');
                appNotificationModal.classList.add('flex'); // Añadir flex para centrar contenido
                document.body.classList.add('overflow-hidden'); // Asegurar que el scroll del body esté desactivado

                appNotificationText.textContent = modalData.appNotificationMessage;
                if (appNotificationSuccessIcon && appNotificationErrorIcon) { // Asegurarse de que los iconos existan
                    if (modalData.appNotificationIsSuccess) {
                        appNotificationSuccessIcon.classList.remove('hidden');
                        appNotificationErrorIcon.classList.add('hidden');
                    } else {
                        appNotificationSuccessIcon.classList.add('hidden');
                        appNotificationErrorIcon.classList.remove('hidden');
                    }
                }
            } else {
                appNotificationModal.classList.add('hidden');
                appNotificationModal.classList.remove('flex');
                // Solo remover overflow-hidden si NO hay otro modal abierto (ej. userFormModal)
                if (!modalData.isOpen) { // Verificar si el modal principal del formulario también está cerrado
                    document.body.classList.remove('overflow-hidden');
                }
            }
        }
    }

    /**
     * Muestra el modal de notificación global.
     * @param {string} message - El mensaje de texto a mostrar.
     * @param {boolean} isSuccess - True para éxito (icono verde), false para error (icono rojo).
     */
    function showAppNotification(message, isSuccess) {
        modalData.appNotificationMessage = message;
        modalData.appNotificationIsSuccess = isSuccess;
        modalData.isAppNotificationModalOpen = true;
        updateAppNotificationModalVisibility();
    }

    function updateModalUI() {
        console.log('JS: updateModalUI llamado. Paso actual:', modalData.currentStep, 'Modo:', modalData.isEditMode ? 'Editar' : 'Crear');

        // Control de visibilidad del modal principal
        updateModalVisibility(); // Ahora se llama a la función dedicada

        if (modalTitle) {
            modalTitle.textContent = modalData.isEditMode ? 'Editar Usuario' : 'Registrar Nuevo Usuario';
        }

        // Control de visibilidad del contenido de los pasos
        if (step1Content) step1Content.classList.toggle('hidden', modalData.currentStep !== 1);
        if (step2Content) step2Content.classList.toggle('hidden', modalData.currentStep !== 2);
        if (step3Content) step3Content.classList.toggle('hidden', modalData.currentStep !== 3);

        // Actualizar indicadores de paso (íconos y texto)
        if (step1Indicator && step2Indicator && step3Indicator) {
            const imgStep1 = step1Indicator.querySelector('img');
            const spanStep1 = step1Indicator.querySelector('span');
            const imgStep2 = step2Indicator.querySelector('img');
            const spanStep2 = step2Indicator.querySelector('span');
            const imgStep3 = step3Indicator.querySelector('img');
            const spanStep3 = step3Indicator.querySelector('span');

            // Resetear todos los estados visuales (poner todos en inactivo primero)
            imgStep1.src = '/images/paso1_inactivo.svg';
            spanStep1.classList.remove('text-gray-700');
            spanStep1.classList.add('text-gray-400');

            imgStep2.src = '/images/paso2_inactivo.svg';
            spanStep2.classList.remove('text-gray-700');
            spanStep2.classList.add('text-gray-400');

            imgStep3.src = '/images/paso3_inactivo.svg';
            spanStep3.classList.remove('text-gray-700');
            spanStep3.classList.add('text-gray-400');

            // Establecer estados según el paso actual
            if (modalData.currentStep >= 1) {
                imgStep1.src = (modalData.currentStep === 1) ? '/images/paso1_activo.svg' : '/images/paso1_completado.svg';
                spanStep1.classList.remove('text-gray-400');
                spanStep1.classList.add('text-gray-700');
            }

            if (modalData.currentStep >= 2) {
                imgStep2.src = (modalData.currentStep === 2) ? '/images/paso2_completado.svg' : '/images/paso1_completado.svg';
                spanStep2.classList.remove('text-gray-400');
                spanStep2.classList.add('text-gray-700');
            }

            if (modalData.currentStep >= 3) {
                imgStep3.src = (modalData.currentStep === 3) ? '/images/paso3_activo.svg' : '/images/paso2_completado.svg';
                spanStep3.classList.remove('text-gray-400');
                spanStep3.classList.add('text-gray-700');
            }
        }

        // CONTROL DE VISIBILIDAD DE LOS BOTONES DE NAVEGACIÓN
        if (nextButton) {
            nextButton.classList.remove('hidden');

            if (modalData.currentStep === 1 || modalData.currentStep === 2) {
                nextButton.innerHTML = `Siguiente <img src="/images/siguiente.svg" alt="siguiente" class="w-5 h-6 ml-2">`;
            } else if (modalData.currentStep === 3) {
                nextButton.innerHTML = `${modalData.isEditMode ? 'Actualizar' : 'Asignar'} <img src="/images/siguiente.svg" alt="enviar" class="w-5 h-6 ml-2">`;
            } else {
                nextButton.classList.add('hidden');
            }
        }

        // Botón "Atrás" (Regresar): visible en Paso 2 y 3, oculto en Paso 1
        if (prevButton) {
            prevButton.classList.toggle('hidden', modalData.currentStep === 1);
        }

        // Botón "Generar Contraseña"
        if (generatePasswordButton) {
            generatePasswordButton.classList.toggle('hidden', modalData.currentStep !== 3 || modalData.isEditMode);
        }

        // Botón "Importar CSV": visible solo en Paso 1, oculto en Paso 2 y 3
        if (importCsvButton) {
            importCsvButton.classList.toggle('hidden', modalData.currentStep !== 1);
        }

        if (cancelButton) {
            cancelButton.classList.add('hidden');
        }

        renderErrors();
        renderSuccessMessage();
        updateFormValues();
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
            } else if (field === 'roles' || field === 'selectedRole') {
                const roleContainer = document.querySelector('#rolesContainer');
                if (roleContainer) {
                    const errorMessage = document.createElement('p');
                    errorMessage.classList.add('text-red-500', 'text-xs', 'mt-1', 'w-full', 'error-message');
                    errorMessage.textContent = modalData.errors[field];
                    roleContainer.appendChild(errorMessage);
                } else {
                    console.warn(`Contenedor de rol con ID 'rolesContainer' no encontrado para el campo '${field}'.`);
                }
            } else if (field === 'password' || field === 'password_confirmation') {
                const passwordInputParent = document.getElementById('password').closest('.relative');
                if (passwordInputParent) {
                    const errorMessage = document.createElement('p');
                    errorMessage.classList.add('text-red-500', 'text-xs', 'mt-1', 'error-message');
                    errorMessage.textContent = modalData.errors[field];
                    passwordInputParent.parentNode.appendChild(errorMessage);
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
        if (nameInput) nameInput.value = modalData.name;
        if (lastnameInput) lastnameInput.value = modalData.lastname;
        if (emailInput) emailInput.value = modalData.email;
        if (phoneInput) phoneInput.value = modalData.phone;
        if (typeDocumentSelect) typeDocumentSelect.value = modalData.type_document;
        if (documentInput) documentInput.value = modalData.document;

        // 2. Llenar campos de contraseña del Paso 3
        if (passwordInput) passwordInput.value = modalData.password;
        if (passwordConfirmationInput) passwordConfirmationInput.value = modalData.passwordConfirmation;

        // 3. Marcar el Radio Button del Rol
        roleCheckboxes.forEach(radio => {
            radio.checked = (radio.value === modalData.selectedRole);
            const label = radio.closest('.role-label');
            const icon = label ? label.querySelector('.role-icon') : null;

            if (label) {
                if (radio.checked) {
                    label.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    label.classList.add('bg-indigo-200', 'text-indigo-800');
                    if (icon) icon.src = icon.src.replace('sin_marca.svg', 'con_marca.svg');
                } else {
                    label.classList.remove('bg-indigo-200', 'text-indigo-800');
                    label.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    if (icon) icon.src = icon.src.replace('con_marca.svg', 'sin_marca.svg');
                }
            }
        });

        // 4. Marcar los Checkboxes de Permisos
        permissionCheckboxes.forEach(checkbox => {
            const spatiePermName = checkbox.value;

            let isChecked = false;
            for (const moduleKey in modalData.modulePermissionMap) {
                if (modalData.modulePermissionMap[moduleKey].includes(spatiePermName)) {
                    let actionKey = '';
                    if (spatiePermName.includes('crear')) actionKey = 'crear';
                    else if (spatiePermName.includes('editar')) actionKey = 'editar';
                    else if (spatiePermName.includes('eliminar')) actionKey = 'eliminar';
                    else if (spatiePermName.includes('validar')) actionKey = 'validar';

                    if (actionKey && modalData.permisos[moduleKey] && modalData.permisos[moduleKey][actionKey] !== undefined) {
                        isChecked = modalData.permisos[moduleKey][actionKey];
                        break;
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
        modalData.userId = null;
        modalData.isOpen = true;
        modalData.currentStep = 1;
        modalData.successMessage = '';
        modalData.errors = {};
        updateModalUI();
    };

    window.openEditModal = async function (userId) {
        console.log('JS: openEditModal función llamada para userId:', userId);
        resetForm();
        modalData.isEditMode = true;
        modalData.userId = userId;
        modalData.isOpen = true;
        modalData.currentStep = 1;
        modalData.successMessage = '';
        modalData.errors = {};
        updateModalUI();

        try {
            const response = await fetch(`/usuario/usuarios/${userId}/data`);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Error al cargar datos del usuario para edición.');
            }

            // Asignar datos básicos (incluyendo nuevos campos)
            modalData.name = data.name || '';
            modalData.lastname = data.lastname || '';
            modalData.email = data.email || '';
            modalData.phone = data.phone || '';
            modalData.type_document = data.type_document || '';
            modalData.document = data.document || '';

            // Asignar el rol del usuario
            modalData.selectedRole = data.userRoles && data.userRoles.length > 0 ? data.userRoles[0] : '';
            console.log('Rol del usuario cargado:', modalData.selectedRole);

            // Asignar los permisos que el usuario YA tiene asignados
            resetPermissions();
            if (data.allUserGrantedPermissions && data.allUserGrantedPermissions.length > 0) {
                console.log('Permisos individuales del usuario cargados:', data.allUserGrantedPermissions);
                data.allUserGrantedPermissions.forEach(permName => {
                    updateModalDataPermission(permName, true);
                });
            }

            // En modo edición, los campos de contraseña se mantienen vacíos,
            modalData.password = '';
            modalData.passwordConfirmation = '';

            updateFormValues();
        } catch (error) {
            console.error('Error al cargar datos para edición:', error);
            modalData.errors.general = error.message;
            modalData.isOpen = false;
            updateModalUI();
        }
    };

    window.closeModal = function () {
        console.log('JS: closeModal función llamada.');
        modalData.isOpen = false;
        resetForm();
        modalData.errors = {};
        modalData.successMessage = '';
        updateModalUI();
    };

    // ************* FUNCIÓN PRINCIPAL DE AVANCE / CONFIRMACIÓN *************
    function handleNextAction() {
        console.log('JS: handleNextAction llamado. Paso actual:', modalData.currentStep);
        modalData.errors = {};
        let hasError = false;

        if (modalData.currentStep === 1) {
            // Validaciones del Paso 1
            modalData.name = nameInput.value.trim();
            modalData.lastname = lastnameInput.value.trim();
            modalData.email = emailInput.value.trim();
            modalData.phone = phoneInput.value.trim();
            modalData.type_document = typeDocumentSelect.value;
            modalData.document = documentInput.value.trim();

            if (!modalData.name) { modalData.errors.name = 'El nombre es obligatorio.'; hasError = true; }
            if (!modalData.lastname) { modalData.errors.lastname = 'El apellido es obligatorio.'; hasError = true; }
            if (!modalData.email || !/\S+@\S+\.\S+/.test(modalData.email)) { modalData.errors.email = 'El correo no es válido.'; hasError = true; }
            if (!modalData.phone) { modalData.errors.phone = 'El teléfono es obligatorio.'; hasError = true; }
            if (!modalData.type_document) { modalData.errors.type_document = 'Debe seleccionar un tipo de documento.'; hasError = true; }
            if (!modalData.document) { modalData.errors.document = 'El número de documento es obligatorio.'; hasError = true; }

            if (!hasError) {
                modalData.currentStep = 2;
            }
        } else if (modalData.currentStep === 2) {
            // Validaciones del Paso 2 (roles y permisos)
            let selectedRoleFound = false;
            roleCheckboxes.forEach(radio => {
                if (radio.checked) {
                    selectedRoleFound = true;
                    modalData.selectedRole = radio.value;
                }
            });
            if (!selectedRoleFound) {
                modalData.errors.selectedRole = 'Debe seleccionar al menos un rol.';
                hasError = true;
            }
            if (!hasError) {
                modalData.currentStep = 3;
            }
        } else if (modalData.currentStep === 3) {
            modalData.password = passwordInput.value;
            modalData.passwordConfirmation = passwordConfirmationInput.value;

            // Validaciones de contraseña (solo si NO estamos en modo edición o si la contraseña no está vacía)
            if (!modalData.isEditMode || (modalData.isEditMode && (modalData.password || modalData.passwordConfirmation))) {
                if (!modalData.password) {
                    modalData.errors.password = 'La contraseña es obligatoria.';
                    hasError = true;
                } else if (modalData.password.length < 8) {
                    modalData.errors.password = 'La contraseña debe tener al menos 8 caracteres.';
                    hasError = true;
                }
                if (modalData.password !== modalData.passwordConfirmation) {
                    modalData.errors.password_confirmation = 'Las contraseñas no coinciden.';
                    hasError = true;
                }
            }

            if (!hasError) {
                openConfirmModal();
                return;
            }
        }
        updateModalUI();
    }

    // ************* FUNCIÓN DE RETROCESO DE PASO *************
    function handlePrevAction() {
        console.log('JS: handlePrevAction llamado. Paso actual:', modalData.currentStep);
        modalData.errors = {};
        if (modalData.currentStep > 1) {
            modalData.currentStep--;
        }
        updateModalUI();
    }

    // ************* NUEVA FUNCIÓN PARA IMPORTAR CSV *************
    function handleImportCsv() {
        console.log('JS: handleImportCsv llamado. Iniciando flujo de importación CSV.');
        // Ocultar el modal principal de creación/edición de usuario
        if (userFormModal) {
            userFormModal.classList.remove('opacity-100');
            userFormModal.classList.add('opacity-0', 'pointer-events-none');
            // NO REMOVER document.body.classList.remove('overflow-hidden') aquí, el modal de importación lo manejará
        }
        // Llamar a la función para abrir el modal de importación CSV del nuevo módulo
        openImportCsvModal();
    }

    // ************* FUNCIONES PARA EL MODAL DE CONFIRMACIÓN *************
    function openConfirmModal() {
        console.log('JS: openConfirmModal llamado.');
        // Ocultar el modal principal antes de mostrar el de confirmación
        userFormModal.classList.remove('opacity-100');
        userFormModal.classList.add('opacity-0', 'pointer-events-none');

        confirmModal.classList.remove('opacity-0', 'pointer-events-none');
        confirmModal.classList.add('opacity-100', 'flex');
        document.body.classList.add('overflow-hidden');

        let rolesHtml = '';
        if (modalData.selectedRole) {
            // Replicando el estilo del radio button seleccionado: bg-indigo-200, text-indigo-800, con_marca.svg
            rolesHtml += `
                <span class="bg-indigo-200 text-indigo-800 px-3 py-1 rounded-full text-sm font-semibold inline-flex items-center mr-2 mb-2">
                    <img src="/images/con_marca.svg" alt="icono de verificación" class="w-4 h-4 mr-1">
                    ${modalData.selectedRole}
                </span>
            `;
        } else {
            rolesHtml += `<span class="text-gray-600">(Ninguno seleccionado)</span>`;
        }

        const selectedPermissionsSet = new Set();
        permissionCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedPermissionsSet.add(checkbox.value);
            }
        });
        let permissionsHtml = '';
        const selectedPermissionsForConfirm = Array.from(selectedPermissionsSet);
        if (selectedPermissionsForConfirm.length > 0) {
            // Generar los permisos como tags/chips con clases de Tailwind
            permissionsHtml = selectedPermissionsForConfirm.map(perm =>
                `<span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold inline-block mr-2 mb-2">${perm}</span>`
            ).join(''); // Unir sin comas, los márgenes ya separan
        } else {
            permissionsHtml += `<span class="text-gray-600">(Ninguno seleccionado)</span>`;
        }

        let passwordDisplayHtml = '';
        if (!modalData.isEditMode && modalData.password) {
            passwordDisplayHtml = `
                <h4 class="font-semibold text-gray-700 mt-4 mb-2">Contraseña generada:</h4>
                <div class="relative flex items-center mb-4 bg-gray-100 p-3 rounded-lg border border-gray-200">
                    <input type="text" value="${modalData.password}" readonly
                        class="w-full bg-transparent text-gray-800 text-base font-mono focus:outline-none cursor-not-allowed" />
                    <button type="button" class="absolute right-3 text-gray-500 hover:text-gray-700" title="Copiar contraseña" onclick="copyPasswordToClipboard('${modalData.password}')">
                        <img src="/images/copy.svg" alt="copiar" class="w-5 h-5" />
                    </button>
                </div>
                <p class="text-gray-600 text-sm mb-4">Esta contraseña se enviará al correo del usuario.</p>
            `;
        } else if (modalData.isEditMode && modalData.password) {
            passwordDisplayHtml = `
                <h4 class="font-semibold text-gray-700 mt-4 mb-2">Contraseña a actualizar:</h4>
                <p class="text-gray-600 text-sm mb-4">Se actualizará la contraseña a la ingresada.</p>
            `;
        }


        confirmMessageBody.innerHTML = `
            <p class="text-gray-700 text-lg mb-6">Estás a punto de <span class="font-bold">${modalData.isEditMode ? 'actualizar' : 'registrar'}</span> un usuario con la siguiente información:</p>
            
            <div class="bg-gray-50 p-4 rounded-lg shadow-inner mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Nombre Completo:</p>
                        <p class="text-gray-900 font-semibold">${modalData.name} ${modalData.lastname}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Documento:</p>
                        <p class="text-gray-900 font-semibold">${modalData.type_document} ${modalData.document}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Correo Electrónico:</p>
                        <p class="text-gray-900 font-semibold">${modalData.email}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Teléfono:</p>
                        <p class="text-gray-900 font-semibold">${modalData.phone}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-gray-500 mb-1">Rol Asignado:</p>
                        <div class="flex flex-wrap gap-2">
                            ${rolesHtml}
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="font-semibold text-gray-700 mb-2">Permisos directos asignados:</h4>
            <div class="flex flex-wrap gap-2 mb-6 p-2 bg-gray-50 rounded-lg border border-gray-200">
                ${permissionsHtml}
            </div>
            
            ${passwordDisplayHtml}
            
            <p class="text-gray-700 text-lg mt-6 font-semibold">¿Confirmas ${modalData.isEditMode ? 'la actualización' : 'la creación'} de este usuario con estas atribuciones?</p>
        `;

        // Ajustar el contenedor de botones para el espacio
        const confirmModalFooter = confirmModal.querySelector('.flex.justify-end.gap-3.mt-4');
        if (confirmModalFooter) {
            confirmModalFooter.classList.remove('justify-end', 'gap-3');
            confirmModalFooter.classList.add('justify-between', 'w-full', 'mt-6');
        }
    }

    function closeConfirmModal() {
        console.log('JS: closeConfirmModal llamado.');
        confirmModal.classList.remove('opacity-100', 'flex');
        confirmModal.classList.add('opacity-0', 'pointer-events-none');
        // Mostrar el modal principal si aún debería estar abierto
        if (modalData.isOpen) {
            userFormModal.classList.remove('opacity-0', 'pointer-events-none');
            userFormModal.classList.add('opacity-100');
        }
    }

    // Función para copiar la contraseña al portapapeles con tooltip
    window.copyPasswordToClipboard = function (buttonElement, password) {
        // 1. Copiar el texto al portapapeles
        const textarea = document.createElement('textarea');
        textarea.value = password;
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            console.log('¡Contraseña copiada al portapapeles!');
        } catch (err) {
            console.error('Error al copiar la contraseña: ', err);
            console.log('No se pudo copiar la contraseña automáticamente. Por favor, cópiala manualmente.');
        }
        document.body.removeChild(textarea);

        // 2. Crear el elemento del tooltip
        const tooltip = document.createElement('div');
        tooltip.textContent = 'Copiado!';
        // Clases de Tailwind para el estilo y la transición del tooltip
        tooltip.className = 'absolute bg-gray-800 text-white text-xs px-2 py-1 rounded-md shadow-lg opacity-0 transition-opacity duration-300 ease-in-out z-50 whitespace-nowrap';

        // 3. Posicionar el tooltip relativo al botón que fue clicado
        const buttonRect = buttonElement.getBoundingClientRect();

        // Posicionar el tooltip ligeramente por encima del botón, centrado horizontalmente sobre él
        tooltip.style.top = `${buttonRect.top - 35}px`; // 35px por encima del botón
        tooltip.style.left = `${buttonRect.left + buttonRect.width / 2}px`;
        tooltip.style.transform = 'translateX(-50%)'; // Centrar horizontalmente

        document.body.appendChild(tooltip);

        // 4. Animar la aparición del tooltip
        // Pequeño retraso para asegurar que la transición de opacidad se aplique correctamente
        setTimeout(() => {
            tooltip.classList.remove('opacity-0');
            tooltip.classList.add('opacity-100');
        }, 10);

        // 5. Animar la desaparición y remover el tooltip después de un tiempo
        setTimeout(() => {
            tooltip.classList.remove('opacity-100');
            tooltip.classList.add('opacity-0');
            // Remover el tooltip del DOM una vez que la transición de salida haya terminado
            tooltip.addEventListener('transitionend', () => tooltip.remove(), { once: true });
        }, 1500); // Mostrar por 1.5 segundos
    };

    async function submitFormConfirmed() {
        console.log('JS: submitFormConfirmed llamado. Iniciando envío.');
        
        // Deshabilitar botón y mostrar spinner en el botón del modal de confirmación
        const actionButton = confirmActionButton;
        const originalBtnText = actionButton.innerHTML;
        actionButton.disabled = true;
        actionButton.innerHTML = `
            <span class="flex items-center justify-between w-full">
                <span>Confirmando</span>
                <img src="/images/cargando_.svg" alt="Cargando..." class="w-5 h-5 animate-spin">
            </span>
        `;

        // NO cerramos el modal de confirmación aquí todavía.
        // Se mantendrá abierto con el spinner visible mientras se envía la solicitud.

        const formData = new FormData();
        formData.append('_token', modalData.csrfToken);
        
        let url = '';
        let method = 'POST';
        
        if (modalData.isEditMode) {
            url = `/usuario/${modalData.userId}`;
            formData.append('_method', 'PUT');
        } else {
            url = `/usuario`;
        }

        // AÑADIR TODOS LOS DATOS RECOPILADOS DE TODOS LOS PASOS
        formData.append('name', modalData.name); 
        formData.append('lastname', modalData.lastname);
        formData.append('email', modalData.email); 
        formData.append('phone', modalData.phone);
        formData.append('type_document', modalData.type_document); 
        formData.append('document', modalData.document); 
        
        formData.append('password', modalData.password);
        formData.append('password_confirmation', modalData.passwordConfirmation);
        
        // Rol
        if (modalData.selectedRole) {
            formData.append('roles[]', modalData.selectedRole);
        } else {
            formData.append('roles[]', '');
        }

        // Permisos
        const selectedPermissions = [];
        permissionCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedPermissions.push(checkbox.value);
            }
        });
        if (selectedPermissions.length > 0) {
            selectedPermissions.forEach(permission => {
                formData.append('permissions[]', permission);
            });
        } else {
            formData.append('permissions[]', '');
        }

        console.log(`JS: Enviando solicitud de ${modalData.isEditMode ? 'EDICIÓN' : 'CREACIÓN'} a URL: ${url} con método ${method}.`);

        try {
            const response = await fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            // Volver a habilitar el botón del modal de confirmación
            actionButton.disabled = false;
            actionButton.innerHTML = originalBtnText;

            // AHORA SÍ: Cerrar el modal de confirmación
            closeConfirmModal(); 

            // Asegurarse de que el modal principal del formulario esté completamente oculto
            modalData.isOpen = false; 
            updateModalVisibility(); // Esto activará la lógica para ocultar userFormModal

            if (response.ok) {
                // Éxito: Mostrar el nuevo modal de notificación global
                const successMessage = modalData.isEditMode ? '¡Tu usuario se ha actualizado correctamente!' : '¡Tu usuario se ha creado correctamente!';
                showAppNotification(successMessage, true); // True para éxito
            } else {
                // Errores: Mostrar mensaje de error en el nuevo modal de notificación global
                console.error('Error en la respuesta del envío:', data);
                let errorMessage = 'Hubo un error al ' + (modalData.isEditMode ? 'actualizar' : 'crear') + ' el usuario.';

                if (data.errors) {
                    const validationErrors = Object.values(data.errors).flat();
                    errorMessage = validationErrors.join('<br>');
                } else if (data.message) {
                    errorMessage = data.message;
                }
                showAppNotification(errorMessage, false); // False para error
            }

        } catch (error) {
            console.error('Error de red o inesperado:', error);
            // Volver a habilitar el botón
            actionButton.disabled = false;
            actionButton.innerHTML = originalBtnText;

            // AHORA SÍ: Cerrar el modal de confirmación
            closeConfirmModal(); 

            // Asegurarse de que el modal principal del formulario esté completamente oculto
            modalData.isOpen = false; 
            updateModalVisibility(); // Esto activará la lógica para ocultar userFormModal

            showAppNotification('Error de conexión o inesperado. Por favor, inténtalo de nuevo.', false); // False para error
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

    if (nextButton) {
        nextButton.addEventListener('click', function (event) {
            event.preventDefault();
            handleNextAction();
        });
    }

    if (prevButton) {
        prevButton.addEventListener('click', function (event) {
            event.preventDefault();
            handlePrevAction();
        });
    }

    if (generatePasswordButton) {
        generatePasswordButton.addEventListener('click', function () {
            const newPassword = generateRandomPassword();
            passwordInput.value = newPassword;
            passwordConfirmationInput.value = newPassword;
            modalData.password = newPassword;
            modalData.passwordConfirmation = newPassword;
            delete modalData.errors.password;
            delete modalData.errors.password_confirmation;
            renderErrors();
        });
    }

    // NUEVO: Event Listener para el botón Importar CSV
    if (importCsvButton) {
        importCsvButton.addEventListener('click', handleImportCsv);
    }

    if (confirmCancelButton) confirmCancelButton.addEventListener('click', closeConfirmModal);
    if (confirmActionButton) confirmActionButton.addEventListener('click', submitFormConfirmed);

    // Actualizar `modalData` cuando los inputs cambian
    if (nameInput) nameInput.addEventListener('input', (e) => modalData.name = e.target.value);
    if (lastnameInput) lastnameInput.addEventListener('input', (e) => modalData.lastname = e.target.value);
    if (emailInput) emailInput.addEventListener('input', (e) => modalData.email = e.target.value);
    if (phoneInput) phoneInput.addEventListener('input', (e) => modalData.phone = e.target.value);
    if (typeDocumentSelect) typeDocumentSelect.addEventListener('change', (e) => modalData.type_document = e.target.value);
    if (documentInput) documentInput.addEventListener('input', (e) => modalData.document = e.target.value);
    if (passwordInput) passwordInput.addEventListener('input', (e) => modalData.password = e.target.value);
    if (passwordConfirmationInput) passwordConfirmationInput.addEventListener('input', (e) => modalData.passwordConfirmation = e.target.value);

    // Listener para los cambios en los radio buttons de rol
    roleCheckboxes.forEach(radio => {
        radio.addEventListener('change', function () {
            if (this.checked) {
                modalData.selectedRole = this.value;
                applyRoleDefaultPermissions(modalData.selectedRole);
            }
        });
    });

    // Listener para los cambios en los checkboxes de permisos
    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            updateModalDataPermission(this.value, this.checked);
        });
    });

    // Lógica para el manejo de roles (click en el label para el estilo)
    const roleLabels = document.querySelectorAll('.role-label');
    roleLabels.forEach(label => {
        label.addEventListener('click', function () {
            const radio = this.querySelector('input[name="roles[]"]');
            if (radio && !radio.checked) {
                radio.checked = true;
                radio.dispatchEvent(new Event('change'));
            }
        });
    });

    // ************* Funciones para mostrar/ocultar contraseña (Paso 3) *************
    if (togglePasswordVisibility) {
        togglePasswordVisibility.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('img').src = type === 'password' ? '/images/ojo-close.svg' : '/images/ojo-open.svg';
        });
    }

    if (toggleConfirmPasswordVisibility) {
        toggleConfirmPasswordVisibility.addEventListener('click', function () {
            const type = passwordConfirmationInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmationInput.setAttribute('type', type);
            this.querySelector('img').src = type === 'password' ? '/images/ojo-close.svg' : '/images/ojo-open.svg';
        });
    }

    // LISTENER PARA EL BOTÓN CERRAR DEL MODAL GLOBAL DE NOTIFICACIONES
    if (appNotificationCloseButton) {
        appNotificationCloseButton.addEventListener('click', function () {
            modalData.isAppNotificationModalOpen = false; // Oculta el modal global
            updateAppNotificationModalVisibility(); // Actualiza la visibilidad
            window.location.reload(); // Recarga la página para refrescar la tabla de usuarios
        });
    }
});