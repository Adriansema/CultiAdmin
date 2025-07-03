<!-- Modal de paso a paso creacion de usuarios -->
<div id="userFormModal"
    class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center 
    transition-opacity duration-300 ease-out opacity-0 pointer-events-none">

    <div class="relative p-8 bg-white shadow-lg rounded-3xl border border-gray-300 max-w-2xl w-full m-4"
        onclick="event.stopPropagation();">

        <button id="closeModalButton"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold leading-none focus:outline-none">&times;
        </button>

        <div class="modal-header text-center mb-6">
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-800"></h2>
        </div>

        <!-- Indicadores de paso actualizados para 3 pasos -->
        <div class="flex items-center justify-center mb-8">
            <div id="step1Indicator" class="flex items-center text-gray-700">
                <img src="{{ asset('images/paso1_activo.svg') }}" alt="paso 1" class="w-7 h-10 mr-2">
                <span class="font-semibold">Datos básicos</span>
            </div>
            <!-- Flecha entre paso 1 y 2 -->
            <div class="mx-4 text-gray-400">
                <img src="{{ asset('images/medio_1_2.svg') }}" alt="flecha" class="w-2 h-3 mr-2">
            </div>
            <div id="step2Indicator" class="flex items-center text-gray-400">
                <img src="{{ asset('images/paso2_inactivo.svg') }}" alt="paso 2" class="w-7 h-10 mr-2">
                <span class="font-semibold">Roles y permisos</span>
            </div>
            <!-- Flecha entre paso 2 y 3 -->
            <div class="mx-4 text-gray-400">
                <img src="{{ asset('images/medio_1_2.svg') }}" alt="flecha" class="w-2 h-3 mr-2">
            </div>
            <div id="step3Indicator" class="flex items-center text-gray-400">
                <img src="{{ asset('images/paso3_inactivo.svg') }}" alt="paso 3" class="w-7 h-10 mr-2">
                <span class="font-semibold">Contraseña</span>
            </div>
        </div>

        <div id="successMessage"
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 hidden"
            role="alert">
            <span></span>
        </div>

        <div id="generalErrorMessage"
            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 hidden" role="alert">
            <span></span>
        </div>

        <form class="flex flex-col h-full">
            <!-- Paso 1: Datos básicos (con Apellido y Teléfono) -->
            <div id="step1Content" class="step-content">
                <div class="mb-6">
                    <label for="name" class="block mb-1 text-sm font-bold text-gray-700">
                        <span class="inline-flex items-center">
                            <img src="{{ asset('images/user.svg') }}" alt="persona" class="w-4 h-4 mr-2"> Nombre:
                        </span>
                    </label>
                    <div class="relative">
                        <input id="name" type="text" name="name" placeholder="ingrese su nombre"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-2xl focus:outline-none
                            focus:ring-2 focus:ring-green-500 focus:border-transparent" />
                    </div>
                </div>

                <div class="mb-6">
                    <label for="lastname" class="block mb-1 text-sm font-bold text-gray-700">
                        <span class="inline-flex items-center">
                            <img src="{{ asset('images/last.svg') }}" alt="persona" class="w-4 h-4 mr-2"> Apellido:
                        </span>
                    </label>
                    <div class="relative">
                        <input id="lastname" type="text" name="lastname" placeholder="ingrese su apellido"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-2xl focus:outline-none
                            focus:ring-2 focus:ring-green-500 focus:border-transparent" />
                    </div>
                </div>

                <div class="mb-6">
                    <label for="email" class="block mb-1 text-sm font-bold text-gray-700">
                        <span class="inline-flex items-center">
                            <img src="{{ asset('images/email.svg') }}" alt="email" class="w-4 h-4 mr-2"> Correo:
                        </span>
                    </label>
                    <div class="relative">
                        <input id="email" type="email" name="email" placeholder="ingrese su correo electronico"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-2xl focus:outline-none
                            focus:ring-2 focus:ring-green-500 focus:border-transparent" />
                    </div>
                </div>

                <div class="mb-6">
                    <label for="phone" class="block mb-1 text-sm font-bold text-gray-700">
                        <span class="inline-flex items-center">
                            <img src="{{ asset('images/phone.svg') }}" alt="telefono" class="w-4 h-4 mr-2"> Teléfono:
                        </span>
                    </label>
                    <div class="relative">
                        <input id="phone" type="text" name="phone" placeholder="ingrese su numero de telefono"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-2xl focus:outline-none
                            focus:ring-2 focus:ring-green-500 focus:border-transparent" />
                    </div>
                </div>

                <div class="mb-6">
                    <label for="type_document" class="block mb-1 text-sm font-bold text-gray-700">
                        <span class="inline-flex items-center">
                            <img src="{{ asset('images/tipo_docs.svg') }}" alt="tipo de documento"
                                class="w-4 h-4 mr-2">
                            Tipo de
                            documento:
                        </span>
                    </label>
                    <div class="relative">
                        <select name="type_document" id="type_document"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-2xl focus:outline-none
                            focus:ring-2 focus:ring-green-500 focus:border-transparent text-gray-500">
                            <option value="">Seleccione el tipo de documento</option>
                            <option value="CC">Cédula de Ciudadanía</option>
                            <option value="TI">Tarjeta de Identidad</option>
                            <option value="CE">Cédula de Extranjería</option>
                            <option value="PEP">Permiso Especial de Permanencia</option>
                            <option value="PPT">Permiso de Protección Temporal</option>
                        </select>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="document" class="block mb-1 text-sm font-bold text-gray-700">
                        <span class="inline-flex items-center">
                            <img src="{{ asset('images/docs.svg') }}" alt="documento" class="w-4 h-4 mr-2">
                            Documento:
                        </span>
                    </label>
                    <div class="relative">
                        <input id="document" type="text" name="document"
                            placeholder="ingrese su numero de documento"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-2xl focus:outline-none focus:ring-2
                            focus:ring-green-500 focus:border-transparent" />
                    </div>
                </div>
            </div>

            <!-- Paso 2: Roles y Permisos -->
            <div id="step2Content" class="step-content hidden">
                <h4 class="text-md font-bold mb-6 flex items-center gap-4">
                    Rol
                    <div id="rolesContainer" class="flex flex-wrap gap-2">
                        @foreach ($roles as $role)
                            <label
                                class="inline-flex items-center px-4 py-2 rounded-lg cursor-pointer transition-all duration-300 role-label
                                bg-gray-100 text-gray-700 hover:bg-gray-200">
                                <img src="{{ asset('images/sin_marca.svg') }}" alt="Icono de selección de rol"
                                    class="w-5 h-5 mr-2 role-icon">

                                <input type="radio" name="roles[]" value="{{ $role->name }}"
                                    class="hidden role-checkbox">
                                <span class="text-sm font-medium">{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </h4>

                <h4 class="text-md font-bold mb-1 flex items-center">
                    Permisos directos del usuario
                </h4>
                <p class="text-gray-600 text-sm mb-6">Asigna permisos adicionales ahora o modifícalos más tarde en
                    Gestión
                    de Usuarios.</p>

                <div class="overflow-x-auto mb-12 rounded-2xl">
                    <table class="min-w-full divide-y divide-gray-300 border border-gray-300">
                        <thead class="bg-[var(--color-Gestion)]">
                            <tr>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Módulo
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Crear
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Editar
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Validar
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Eliminar
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $groupedPermissions = [];
                                $actionTypes = ['crear', 'editar', 'validar', 'eliminar'];
                                $moduleMappings = [
                                    'producto' => 'productos',
                                    'noticia' => 'noticias',
                                    'boletin' => 'boletines',
                                    'usuario' => 'usuarios',
                                ];

                                foreach ($moduleMappings as $prefix => $moduleJsKey) {
                                    $groupedPermissions[$moduleJsKey] = [];
                                    foreach ($actionTypes as $action) {
                                        $groupedPermissions[$moduleJsKey][$action] = null;
                                    }
                                }

                                foreach ($permissions as $p) {
                                    foreach ($moduleMappings as $prefix => $moduleJsKey) {
                                        foreach ($actionTypes as $action) {
                                            $expectedPermissionName = $action . ' ' . $prefix;
                                            if ($p->name === $expectedPermissionName) {
                                                $groupedPermissions[$moduleJsKey][$action] = $p->name;
                                                break 2;
                                            }
                                        }
                                    }
                                }
                            @endphp

                            @forelse ($groupedPermissions as $moduleJsKey => $actions)
                                @if (count(array_filter($actions)) > 0)
                                    <tr>
                                        <td
                                            class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 flex items-center">
                                            @if ($moduleJsKey === 'productos')
                                                <img src="{{ asset('images/planta.svg') }}" alt="Productos"
                                                    class="w-5 h-5 mr-2"> Productos
                                            @elseif ($moduleJsKey === 'noticias')
                                                <img src="{{ asset('images/noticia.svg') }}" alt="Noticias"
                                                    class="w-5 h-5 mr-2"> Noticias
                                            @elseif ($moduleJsKey === 'boletines')
                                                <img src="{{ asset('images/boletin.svg') }}" alt="Boletines"
                                                    class="w-5 h-5 mr-2"> Boletines
                                            @elseif ($moduleJsKey === 'usuarios')
                                                <img src="{{ asset('images/gestion.svg') }}" alt="Usuarios"
                                                    class="w-5 h-5 mr-2"> Usuarios
                                            @endif
                                        </td>
                                        @foreach ($actionTypes as $actionType)
                                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                                @php
                                                    $permissionName = $actions[$actionType];
                                                @endphp
                                                @if ($permissionName)
                                                    <input type="checkbox"
                                                        id="permission_{{ str_replace(' ', '_', $permissionName) }}"
                                                        name="permissions[]" value="{{ $permissionName }}"
                                                        class="form-checkbox h-5 w-5 text-indigo-600 rounded cursor-pointer">
                                                @else
                                                    <span class="h-5 w-5 rounded opacity-30 cursor-not-allowed"
                                                        type="checkbox" disabled
                                                        title="Permiso no disponible para este módulo">
                                                    </span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No hay permisos definidos en el sistema o no se pudieron agrupar para esta
                                        vista.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paso 3: Generar Contraseña (Nuevo) -->
            <div id="step3Content" class="step-content hidden">
                <div class="mb-4">
                    <label for="password" class="block mb-1 text-sm font-bold text-gray-700">
                        <span class="inline-flex items-center">
                            <img src="{{ asset('images/candado.svg') }}" alt="contraseña" class="w-4 h-4 mr-2">
                            Contraseña:
                        </span>
                    </label>
                    <div class="relative">
                        <input id="password" type="password" name="password" placeholder="ingrese su contraseña"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-2xl focus:outline-none
                            focus:ring-2 focus:ring-green-500 focus:border-transparent" />
                        <span
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 cursor-pointer"
                            id="togglePasswordVisibility">
                            <img src="{{ asset('images/ojo-close.svg') }}" alt="mostrar"
                                class="w-5 h-5 text-gray-500" />
                        </span>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="block mb-1 text-sm font-bold text-gray-700">
                        <span class="inline-flex items-center">
                            <img src="{{ asset('images/candado.svg') }}" alt="confirmar contraseña"
                                class="w-4 h-4 mr-2"> Confirmar Contraseña:
                        </span>
                    </label>
                    <div class="relative">
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            placeholder="confirme su contraseña"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-2xl focus:outline-none
                            focus:ring-2 focus:ring-green-500 focus:border-transparent" />
                        <span
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 cursor-pointer"
                            id="toggleConfirmPasswordVisibility">
                            <img src="{{ asset('images/ojo-close.svg') }}" alt="mostrar"
                                class="w-5 h-5 text-gray-500" />
                        </span>
                    </div>
                </div>
            </div>

            <!-- Botones de navegación -->
            <div class="flex justify-between items-center mt-auto pt-8">
                <div class="flex space-x-3">
                    <!-- Botón Importar CSV (Ajustado para consistencia) -->
                    <button type="button" id="importCsvButton"
                        class="inline-flex justify-start py-2 px-4 border hover:border-[var(--color-hover)] font-medium group rounded-full focus:outline-none focus:shadow-outline items-center text-md hover:bg-gray-50 transition duration-150 ease-in-out">
                        <img src="{{ asset('images/Importar.svg') }}"
                            class="w-5 h-5 mr-2 relative inset-0 block group-hover:hidden" alt="Icono de Importar">
                        <img src="{{ asset('images/Importar-hover.svg') }}"
                            class="w-5 h-5 mr-2 relative inset-0 hidden group-hover:block"
                            alt="Icono de importar hover">
                        <span class="text-md font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                            {{ __('Importar Csv') }}
                        </span>
                    </button>

                    <!-- Botón Regresar (visible en Paso 2 y 3) -->
                    <button type="button" id="prevStepButton"
                        class="bg-[var(--color-regresar)] hover:bg-[var(--color-hoveregresar)] py-2 px-4 rounded-full text-md font-bold text-white focus:outline-none focus:shadow-outline inline-flex items-center transition duration-150 ease-in-out">
                        <!-- CAMBIOS: px-4 para más espacio, inline-flex items-center para alineación -->
                        <img src="{{ asset('images/regresar.svg') }}" alt="Regresar" class="w-5 h-6 mr-2">
                        <span class="whitespace-nowrap text-inherit">{{ __('Regresar') }}</span>
                    </button>

                    <!-- Botón Generar Contraseña (solo en Paso 3 y modo creación) -->
                    <button type="button" id="generatePasswordButton"
                        class="bg-[var(--color-generador)] hover:bg-[var(--color-hovergener)] py-2 px-4 rounded-full text-md font-bold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 inline-flex items-center">
                        <!-- CAMBIOS: px-4, inline-flex items-center, y mr-2 para el icono -->
                        <img src="{{ asset('images/gener_pass.svg') }}" alt="generar contraseña"
                            class="w-5 h-6 mr-2">
                        <span class="whitespace-nowrap text-inherit">{{ __('Generar Contraseña') }}</span>
                    </button>
                </div>

                <div class="flex space-x-3">
                    <!-- Botón Cancelar (se mantiene oculto/visible) -->
                    <button type="button" id="cancelButton"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-full hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500inline-flex items-center">
                        <!-- CAMBIOS: px-4, inline-flex items-center, y mr-2 para el icono -->
                        <img src="{{ asset('images/equis.svg') }}" alt="cancelar" class="w-5 h-6 mr-2">
                        <span class="whitespace-nowrap text-inherit">{{ __('Cancelar') }}</span>
                    </button>

                    <!-- Botón Siguiente / Asignar Contraseña (Ajustado para consistencia) -->
                    <button type="button" id="nextStepButton"
                        class="bg-[var(--color-sgt)] hover:bg-[var(--color-hoversgt)] py-2 px-4 rounded-full text-md font-bold text-white focus:outline-none focus:shadow-outline inline-flex items-center transition duration-150 ease-in-out">
                        <!-- CAMBIOS: px-4, inline-flex items-center -->
                        <span class="whitespace-nowrap text-inherit">{{ __('Siguiente') }}</span>
                        <img src="{{ asset('images/siguiente.svg') }}" alt="siguiente" class="w-5 h-6 ml-2">
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Confirmación -->
<div id="confirmModal"
    class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50
    transition-opacity duration-300 ease-out opacity-0 pointer-events-none">

    <div class="relative p-8 bg-white w-full max-w-2xl mx-auto rounded-xl shadow-xl transform transition-all sm:my-8"
        role="dialog" aria-modal="true" aria-labelledby="confirmModalTitle">

        <div class="text-center">
            <h3 class="text-xl font-bold text-gray-900 mb-4" id="confirmModalTitle">Confirmar Acción</h3>

            <div id="confirmMessageBody" class="text-left text-gray-700 leading-relaxed mb-6">
                <!-- El contenido se llenará dinámicamente con JavaScript -->
            </div>

            <div class="flex justify-end gap-3 mt-4">
                <button type="button" id="confirmCancelButton"
                    class="btn-secondary px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancelar
                </button>
                <button type="button" id="confirmActionButton"
                    class="btn-primary bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<div id="importCsvUploadModal"
    class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center 
    transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
    <div class="relative p-8 bg-white shadow-lg rounded-3xl border border-gray-300 w-full max-w-xl mx-4 my-8"
        role="dialog" aria-modal="true" aria-labelledby="uploadModalTitle" onclick="event.stopPropagation();">
        <button id="closeUploadModalButton"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold leading-none focus:outline-none">&times;
        </button>

        <h2 id="uploadModalTitle" class="text-2xl font-bold text-gray-800 text-center mb-6">Importar usuarios</h2>
        <p class="text-gray-700 mb-6 text-center">Selecciona un archivo CSV para importar usuarios.</p>
        <div
            class="flex flex-col items-center group justify-center border-2 border-dashed border-gray-300 rounded-lg p-8 mb-6 cursor-pointer hover:border-green-500 transition-colors duration-200">
            <input type="file" id="csvFileInput" accept=".csv" class="hidden">
            <label for="csvFileInput" class="cursor-pointer text-gray-600 hover:text-green-700">
                <img src="{{ asset('images/Importar.svg') }}"class="w-12 h-12 mx-auto mb-3 relative inset-0 block group-hover:hidden"
                    alt="Icono de Importar">
                <img src="{{ asset('images/Importar-hover.svg') }}"class="w-12 h-12 mx-auto mb-3 relative inset-0 hidden group-hover:block"
                    alt="Icono de importar hover">
                <p class="font-semibold text-lg">Arrastra tu archivo aquí o haz clic para seleccionar</p>
                <p class="text-sm text-gray-500 mt-1">(Solo archivos .csv)</p>
            </label>
        </div>
    </div>
</div>

<!-- Modal de Previsualización CSV (Corresponde a la tabla de tu Imagen 1) -->
<div id="importCsvPreviewModal"
    class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center 
    transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
    <div class="relative p-8 bg-white shadow-lg rounded-3xl border border-gray-300 w-full max-w-3xl mx-4 my-8"
        role="dialog" aria-modal="true" aria-labelledby="previewModalTitle" onclick="event.stopPropagation();">
        <button id="closePreviewModalButton"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold leading-none focus:outline-none">&times;
        </button>

        <h2 id="previewModalTitle" class="text-2xl font-bold text-gray-800 text-center mb-6">Importar usuarios</h2>

        <p class="text-gray-700 text-sm mb-4">
            Se han detectado (<span id="detectedUsersCount" class="font-bold">0</span>) usuarios en el archivo CSV con
            sus <span class="font-bold">roles asignados</span>. Confirme que toda la información sea correcta antes de
            continuar.
        </p>
        <div id="csvPreviewTableContainer"
            class="overflow-auto max-h-80 bg-gray-50 rounded-lg p-2 border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nombre</th>
                    </tr>
                </thead>
                <tbody id="csvPreviewTableBody" class="bg-white divide-y divide-gray-200">
                    <!-- Filas del CSV se insertarán aquí -->
                </tbody>
            </table>
        </div>

        <div class="flex justify-between items-center mt-6">
            <button type="button" id="previewPrevButton"
                class="btn-secondary px-4 py-2 border border-gray-300 rounded-full text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2 transform rotate-180"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd" />
                </svg>
                Regresar
            </button>
            <button type="button" id="previewNextButton"
                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-3 rounded-full focus:outline-none focus:shadow-outline flex items-center text-md
                        transition duration-150 ease-in-out">
                Siguiente
                <img src="{{ asset('images/siguiente.svg') }}" alt="siguiente" class="w-5 h-6 ml-2">
            </button>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Importación (Tu anterior Paso 2, Imagen 2/6) -->
<div id="importCsvConfirmModal"
    class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center 
    transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
    <div class="relative p-8 bg-white shadow-lg rounded-3xl border border-gray-300 w-full max-w-xl mx-4 my-8"
        role="dialog" aria-modal="true" aria-labelledby="confirmImportModalTitle"
        onclick="event.stopPropagation();">
        <button id="closeConfirmImportModalButton"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold leading-none focus:outline-none">&times;
        </button>

        <h2 id="confirmImportModalTitle" class="text-2xl font-bold text-gray-800 text-center mb-6">Confirmar
            Importación</h2>
        <p class="text-gray-700 text-lg mb-6 text-center">Al continuar con esta acción se crearán:</p>
        <div id="importSummaryContent" class="bg-gray-50 p-4 rounded-lg shadow-inner mb-6">
            <!-- Resumen de importación se llenará aquí -->
        </div>
        <div class="flex justify-between items-center mt-6">
            <button type="button" id="confirmImportPrevButton"
                class="btn-secondary px-4 py-2 border border-gray-300 rounded-full text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2 transform rotate-180"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd" />
                </svg>
                Regresar
            </button>
            <button type="button" id="confirmImportActionButton"
                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-3 rounded-full focus:outline-none focus:shadow-outline flex items-center text-md
                        transition duration-150 ease-in-out">
                Aceptar
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </button>
        </div>
    </div>
</div>


<!-- MODALES DE MENSAJE PARA ERRORES ESPECÍFICOS -->

<!-- Modal de Archivo Vacío (Imagen 3) -->
<div id="importCsvEmptyModal"
    class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center 
    transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
    <div class="relative p-8 bg-white shadow-lg rounded-3xl border border-gray-300 w-full max-w-md mx-4 my-8"
        role="dialog" aria-modal="true" aria-labelledby="emptyModalTitle" onclick="event.stopPropagation();">
        <button id="closeEmptyModalButton"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold leading-none focus:outline-none">&times;
        </button>
        <h2 id="emptyModalTitle" class="text-2xl font-bold text-gray-800 text-center mb-6">Archivo vacío</h2>
        <p class="text-gray-700 text-center mb-6">
            No se encontraron usuarios en el archivo CSV. Verifique que el formato sea el correcto y que el CSV contenga
            al menos una fila con nombre y rol. ¿Necesita ayuda con el formato del CSV? Consulte nuestra <a
                href="#" class="text-green-600 hover:text-green-800 font-semibold" target="_blank">guía de
                importación</a>.
        </p>
        <div class="flex justify-center mt-6">
            <button type="button" id="returnFromEmptyModalButton"
                class="btn-secondary px-4 py-2 border border-gray-300 rounded-full text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2 transform rotate-180"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd" />
                </svg>
                Regresar
            </button>
        </div>
    </div>
</div>

<!-- Modal de Usuarios Duplicados (Imagen 4) -->
<div id="importCsvDuplicatesModal"
    class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center 
    transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
    <div class="relative p-8 bg-white shadow-lg rounded-3xl border border-gray-300 w-full max-w-lg mx-4 my-8"
        role="dialog" aria-modal="true" aria-labelledby="duplicatesModalTitle" onclick="event.stopPropagation();">
        <button id="closeDuplicatesModalButton"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold leading-none focus:outline-none">&times;
        </button>
        <h2 id="duplicatesModalTitle" class="text-2xl font-bold text-gray-800 text-center mb-6">Usuarios duplicados
        </h2>
        <p class="text-gray-700 text-center mb-6">
            Se han identificado usuarios duplicados (con el mismo correo o el mismo número de documento). Asegúrese de
            que cada usuario sea único para evitar conflictos. Estas son las filas con información duplicada:
        </p>
        <div id="duplicatesList" class="bg-gray-50 p-4 rounded-lg shadow-inner mb-6 overflow-auto max-h-60">
            <!-- La lista de duplicados se llenará aquí con JavaScript -->
        </div>
        <div class="flex justify-center mt-6">
            <button type="button" id="returnFromDuplicatesModalButton"
                class="btn-secondary px-4 py-2 border border-gray-300 rounded-full text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2 transform rotate-180"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd" />
                </svg>
                Regresar
            </button>
        </div>
    </div>
</div>

<!-- Modal de Datos Faltantes (Imagen 5) -->
<div id="importCsvMissingDataModal"
    class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center 
    transition-opacity duration-300 ease-out opacity-0 pointer-events-none">
    <div class="relative p-8 bg-white shadow-lg rounded-3xl border border-gray-300 w-full max-w-lg mx-4 my-8"
        role="dialog" aria-modal="true" aria-labelledby="missingDataModalTitle" onclick="event.stopPropagation();">
        <button id="closeMissingDataModalButton"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold leading-none focus:outline-none">&times;
        </button>
        <h2 id="missingDataModalTitle" class="text-2xl font-bold text-gray-800 text-center mb-6">Datos faltantes</h2>
        <p class="text-gray-700 text-center mb-6">
            Se han detectado filas con campos faltantes o formatos inválidos. Por favor, corrija el CSV o excluya esas
            filas antes de continuar. Estos son los campos a corregir:
        </p>
        <div id="missingDataList" class="bg-gray-50 p-4 rounded-lg shadow-inner mb-6 overflow-auto max-h-60">
            <!-- La lista de datos faltantes se llenará aquí con JavaScript -->
        </div>
        <div class="flex justify-center mt-6">
            <button type="button" id="returnFromMissingDataModalButton"
                class="btn-secondary px-4 py-2 border border-gray-300 rounded-full text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2 transform rotate-180"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd" />
                </svg>
                Regresar
            </button>
        </div>
    </div>
</div>
