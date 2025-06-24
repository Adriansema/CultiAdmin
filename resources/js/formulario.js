document.addEventListener('DOMContentLoaded', function () {
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
    const roleCheckboxes = document.querySelectorAll('.role-checkbox');
    const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');

    // ELEMENTOS DEL MODAL DE CONFIRMACIÓN (asegúrate de que su HTML esté presente)
    const confirmModal = document.getElementById('confirmModal');
    const confirmMessageBody = document.getElementById('confirmMessageBody');
    const confirmCancelButton = document.getElementById('confirmCancelButton');
    const confirmActionButton = document.getElementById('confirmActionButton');

    // Verificaciones iniciales de elementos (solo para los que esperas que existan)
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
        userId: null, // ¡Este será clave para el flujo de la Opción B!
        nombre: '',
        correo: '',
        tipoDocumento: '',
        numeroDocumento: '',
        rolSeleccionado: '',
        permisos: {
            productos: { crear: false, editar: false, validar: false, eliminar: false },
            noticias: { crear: false, editar: false, validar: false, eliminar: false },
            boletines: { crear: false, editar: false, validar: false, eliminar: false },
            usuarios: { crear: false, editar: false, validar: false, eliminar: false }
        },
        errors: {},
        successMessage: '',
        modulePermissionMap: {
            'productos': ['crear producto', 'editar producto', 'validar producto', 'eliminar producto'],
            'noticias': ['crear noticia', 'editar noticia', 'validar noticia', 'eliminar noticia'],
            'boletines': ['crear boletin', 'editar boletin', 'validar boletin', 'eliminar boletin'],
            'usuarios': ['crear usuario', 'editar usuario', 'validar usuario', 'eliminar usuario']
        },
        csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };

    function updateModalUI() {
        console.log('JS: updateModalUI llamado. Paso actual:', modalData.currentStep, 'Modo:', modalData.isEditMode ? 'Editar' : 'Crear');

        modalData.isOpen ? userFormModal.classList.remove('hidden') : userFormModal.classList.add('hidden');
        if (modalData.isOpen) {
            document.body.classList.add('overflow-hidden');
        } else {
            document.body.classList.remove('overflow-hidden');
        }

        if (modalTitle) {
            modalTitle.textContent = modalData.isEditMode ? 'Editar Usuario' : 'Registrar Nuevo Usuario';
        }

        if (step1Content) step1Content.classList.toggle('hidden', modalData.currentStep !== 1);
        if (step2Content) step2Content.classList.toggle('hidden', modalData.currentStep !== 2);

        // Actualizar indicadores de paso
        if (step1Indicator && step2Indicator) {
            const imgStep1 = step1Indicator.querySelector('img');
            const spanStep1 = step1Indicator.querySelector('span');
            const imgStep2 = step2Indicator.querySelector('img');
            const spanStep2 = step2Indicator.querySelector('span');

            imgStep1.src = modalData.currentStep >= 1 ? '/images/1paso.svg' : '/images/1paso_gray.svg';
            spanStep1.classList.toggle('text-green-700', modalData.currentStep >= 1);
            spanStep1.classList.toggle('text-gray-400', modalData.currentStep < 1);

            imgStep2.src = modalData.currentStep === 2 ? '/images/2paso.svg' : '/images/2paso_gray.svg';
            spanStep2.classList.toggle('text-green-700', modalData.currentStep === 2);
            spanStep2.classList.toggle('text-gray-400', modalData.currentStep < 2);
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
            } else if (field === 'roles' || field === 'rolSeleccionado') {
                const roleContainer = roleCheckboxes[0]?.closest('.flex.flex-wrap.gap-2');
                if (roleContainer) {
                    const errorMessage = document.createElement('p');
                    errorMessage.classList.add('text-red-500', 'text-xs', 'mt-1', 'w-full', 'error-message');
                    errorMessage.textContent = modalData.errors[field];
                    roleContainer.appendChild(errorMessage);
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

    function updateFormValues() {
        if (nameInput) nameInput.value = modalData.nombre;
        if (emailInput) emailInput.value = modalData.correo;
        if (typeDocumentSelect) typeDocumentSelect.value = modalData.tipoDocumento;
        if (documentInput) documentInput.value = modalData.numeroDocumento;

        roleCheckboxes.forEach(checkbox => {
            checkbox.checked = checkbox.value === modalData.rolSeleccionado;
            const label = checkbox.closest('.role-label');
            const icon = label ? label.querySelector('.role-icon') : null;

            if (label) {
                if (checkbox.checked) {
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

        permissionCheckboxes.forEach(checkbox => {
            let isChecked = false;
            for (const moduleKey in modalData.permisos) {
                for (const actionKey in modalData.permisos[moduleKey]) {
                    const expectedPermissionName = `${actionKey} ${moduleKey.slice(0, -1)}`;
                    if (checkbox.value === expectedPermissionName && modalData.permisos[moduleKey][actionKey]) {
                        isChecked = true;
                        break;
                    }
                }
                if (isChecked) break;
            }
            checkbox.checked = isChecked;
        });
    }

    // --- Funciones públicas para interactuar con el modal principal ---

    window.openCreateModal = function () {
        console.log('JS: openCreateModal función llamada.');
        resetForm();
        modalData.isEditMode = false;
        modalData.isOpen = true;
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
        modalData.isOpen = true;
        modalData.currentStep = 1; // Siempre iniciar en el paso 1 para edición
        modalData.successMessage = '';
        modalData.errors = {};
        updateModalUI(); // Muestra el modal vacío por un momento

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

            //Asignar rol
            modalData.rolSeleccionado = data.userRoles && data.userRoles.length > 0 ? data.userRoles[0].name : '';

            //Asignar permisos
            resetPermissions(); // Primero limpiar todos los permisos
            if (data.allUserGrantedPermissions) { // Usar 'allUserGrantedPermissions' que es lo que devuelve el backend
                data.allUserGrantedPermissions.forEach(userPermName => {
                    for (const moduleKey in modalData.permisos) {
                        for (const actionKey in modalData.permisos[moduleKey]) {
                            const expectedPermissionName = `${actionKey} ${moduleKey.slice(0, -1)}`; // Formato 'crear producto'
                            if (userPermName === expectedPermissionName) {
                                modalData.permisos[moduleKey][actionKey] = true;
                                break; // Romper el bucle interno una vez encontrado
                            }
                        }
                    }
                });
            }
            updateFormValues(); // Llenar los campos del formulario con los datos cargados
        } catch (error) {
            console.error('Error al cargar datos para edición:', error);
            modalData.errors.general = error.message;
            modalData.isOpen = false; // Cerrar el modal si hay un error crítico
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
            let selectedRole = false;
            roleCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedRole = true;
                    modalData.rolSeleccionado = checkbox.value; // Asegurarse que esté actualizado
                }
            });
            if (!selectedRole) {
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

    // NUEVA FUNCIÓN: Maneja el envío de datos del Paso 1 en modo creación
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
        nextButton.innerHTML = `Validando... <img src="/images/siguiente.svg" alt="cargando" class="w-5 h-6 ml-2">`; // Puedes poner un spinner aquí

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
    // ************* FIN FUNCIÓN PRINCIPAL DE AVANCE / CONFIRMACIÓN *************


    // ************* NUEVAS FUNCIONES PARA EL MODAL DE CONFIRMACIÓN *************
    function openConfirmModal() {
        console.log('JS: openConfirmModal llamado.');
        userFormModal.classList.add('hidden'); // Ocultar el modal principal
        confirmModal.classList.remove('hidden');
        confirmModal.classList.add('flex');

        let rolesHtml = '';
        if (modalData.rolSeleccionado) {
            rolesHtml += `<li>${modalData.rolSeleccionado}</li>`;
        } else {
            rolesHtml += `<li>(Ninguno seleccionado)</li>`;
        }

        let permissionsHtml = '';
        let selectedPermissions = [];
        for (const moduleKey in modalData.permisos) {
            for (const actionKey in modalData.permisos[moduleKey]) {
                if (modalData.permisos[moduleKey][actionKey]) {
                    const permName = `${actionKey} ${moduleKey.slice(0, -1)}`;
                    selectedPermissions.push(permName);
                }
            }
        }
        if (selectedPermissions.length > 0) {
            selectedPermissions.forEach(perm => {
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
        confirmModal.classList.add('hidden');
        confirmModal.classList.remove('flex');
        userFormModal.classList.remove('hidden'); // Volver a mostrar el modal principal
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
        actionButton.textContent = 'Enviando...';

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
        // Si hay un rol seleccionado, lo adjuntamos como un elemento de array.
        // Si no hay rol seleccionado, no adjuntamos el campo 'roles[]' al FormData.
        // Laravel, con tu validación 'nullable|array', interpretará la ausencia como null,
        // y tu backend con `syncRoles($request->roles ?? [])` lo manejará como un array vacío.
        if (modalData.rolSeleccionado) {
            formData.append('roles[]', modalData.rolSeleccionado);
        }

        // Manejo de Permisos
        const selectedPermissions = [];
        // Itera sobre los módulos y acciones para construir el array de nombres de permisos.
        for (const moduleKey in modalData.permisos) {
            for (const actionKey in modalData.permisos[moduleKey]) {
                if (modalData.permisos[moduleKey][actionKey]) {
                    const permName = `${actionKey} ${moduleKey.slice(0, -1)}`; // Ejemplo: 'crear usuario'
                    selectedPermissions.push(permName);
                }
            }
        }

        // ¡ESTE ES EL CAMBIO CRÍTICO Y CORRECTO PARA PERMISOS!
        // Si hay permisos seleccionados, adjunta cada uno individualmente como 'permissions[]'.
        // Si no hay permisos, no adjuntes el campo 'permissions[]' al FormData.
        // Laravel, con tu validación 'nullable|array', interpretará la ausencia como null,
        // y tu backend con `syncPermissions($request->permissions ?? [])` lo manejará como un array vacío.
        if (selectedPermissions.length > 0) {
            selectedPermissions.forEach(permission => {
                formData.append('permissions[]', permission); // Envía cada permiso como un elemento del array
            });
        }

        let url = ''; // La URL que se usará para el fetch.

        // La lógica para la URL es ahora más simple: siempre será una actualización a un usuario existente.
        // Ya sea que se acabe de crear (modalData.userId ya está asignado) o se esté editando.
        if (!modalData.userId) {
            console.error('ERROR: userId no definido para la operación de actualización/finalización del formulario.');
            modalData.errors.general = 'Error interno: No se pudo obtener el ID del usuario para finalizar la operación. Por favor, cancela y vuelve a intentar.';
            userFormModal.classList.remove('hidden');
            updateModalUI();
            actionButton.disabled = false;
            actionButton.textContent = originalBtnText;
            return;
        }

        // La URL para ambas situaciones (finalizar creación y edición) apunta al método PUT de Laravel:
        // Route::put('/{usuario}', [UsuarioController::class, 'update'])->name('update');
        // que está dentro de Route::prefix('usuario'), por lo tanto, la URL es /usuario/{id}
        url = `/usuario/${modalData.userId}`; // ESTE ES EL CAMBIO CLAVE EN LA URL

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
                    console.error('Errores de validación del backend:', data.errors); // *** ¡ESTO ES LO QUE NECESITAMOS VER! ***
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
                userFormModal.classList.remove('hidden'); // Asegura que el modal principal esté visible
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
            userFormModal.classList.remove('hidden');
            updateModalUI();
        } finally {
            actionButton.disabled = false;
            actionButton.textContent = originalBtnText;
        }
    }
    // ************* FIN NUEVAS FUNCIONES PARA EL MODAL DE CONFIRMACIÓN *************


    function resetForm() {
        modalData.currentStep = 1;
        modalData.userId = null;
        modalData.nombre = '';
        modalData.correo = '';
        modalData.tipoDocumento = '';
        modalData.numeroDocumento = '';
        modalData.rolSeleccionado = '';
        resetPermissions();
        modalData.errors = {};
        modalData.successMessage = '';

        if (nameInput) nameInput.value = '';
        if (emailInput) emailInput.value = '';
        if (typeDocumentSelect) typeDocumentSelect.value = '';
        if (documentInput) documentInput.value = '';

        roleCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
            const label = checkbox.closest('.role-label');
            const icon = label ? label.querySelector('.role-icon') : null;
            if (label) {
                label.classList.remove('bg-indigo-200', 'text-indigo-800');
                label.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                if (icon) icon.src = icon.src.replace('con_marca.svg', 'sin_marca.svg');
            }
        });
        permissionCheckboxes.forEach(checkbox => checkbox.checked = false);
        updateFormValues();
    }

    function resetPermissions() {
        for (const moduleName in modalData.permisos) {
            for (const actionType in modalData.permisos[moduleName]) {
                modalData.permisos[moduleName][actionType] = false;
            }
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

    // Lógica para el manejo de roles (click en labels)
    const roleLabels = document.querySelectorAll('.role-label');
    roleLabels.forEach(label => {
        label.addEventListener('click', function (e) {
            e.preventDefault();
            const checkbox = this.querySelector('.role-checkbox');

            roleCheckboxes.forEach(cb => {
                if (cb !== checkbox) {
                    cb.checked = false;
                    const otherLabel = cb.closest('.role-label');
                    const otherIcon = otherLabel ? otherLabel.querySelector('.role-icon') : null;
                    if (otherLabel) {
                        otherLabel.classList.remove('bg-indigo-200', 'text-indigo-800');
                        otherLabel.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                        if (otherIcon) otherIcon.src = otherIcon.src.replace('con_marca.svg', 'sin_marca.svg');
                    }
                }
            });

            checkbox.checked = !checkbox.checked;
            modalData.rolSeleccionado = checkbox.checked ? checkbox.value : '';

            const icon = this.querySelector('.role-icon');
            if (checkbox.checked) {
                this.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                this.classList.add('bg-indigo-200', 'text-indigo-800');
                if (icon) icon.src = icon.src.replace('sin_marca.svg', 'con_marca.svg');
            } else {
                this.classList.remove('bg-indigo-200', 'text-indigo-800');
                this.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                if (icon) icon.src = icon.src.replace('con_marca.svg', 'sin_marca.svg');
            }
        });
    });

    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', (e) => {
            const idParts = checkbox.id.split('_');
            if (idParts.length >= 3 && idParts[0] === 'permission') {
                const action = idParts[1];
                const moduleSingular = idParts[2];
                const modulePlural = moduleSingular + 's';
                if (modalData.permisos[modulePlural] && typeof modalData.permisos[modulePlural][action] !== 'undefined') {
                    modalData.permisos[modulePlural][action] = e.target.checked;
                }
            }
        });
    });

    // Inicializar el formulario al cargar la página
    resetForm();
    updateModalUI();
});