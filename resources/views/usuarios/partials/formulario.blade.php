<div id="userFormModal"
    class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center 
    transition-opacity duration-300 ease-out opacity-0 pointer-events-none">

    <div class="relative p-8 bg-white shadow-lg rounded-3xl border border-gray-300 max-w-lg w-full m-4"
        onclick="event.stopPropagation();">

        <button id="closeModalButton"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold leading-none focus:outline-none">&times;</button>

        <div class="modal-header text-center mb-6">
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-800"></h2>
        </div>

        <div class="flex items-center justify-center mb-8">
            <div id="step1Indicator" class="flex items-center text-gray-700">
                <img src="{{ asset('images/paso1_activo.svg') }}" alt="paso 1" class="w-7 h-10 mr-2">
                <span class="font-semibold">Datos básicos</span>
            </div>
            <div class="mx-4 text-gray-400">
                <img src="{{ asset('images/medio_1_2.svg') }}" alt="flecha" class="w-2 h-3 mr-2">
            </div>
            <div id="step2Indicator" class="flex items-center text-gray-400">
                <img src="{{ asset('images/paso2_inactivo.svg') }}" alt="paso 2" class="w-7 h-10 mr-2">
                <span class="font-semibold">Roles y permisos</span>
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
            <div id="step1Content" class="step-content">
                <div class="mb-4">
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

                <div class="mb-4">
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

                <div class="mb-4">
                    <label for="type_document" class="block mb-1 text-sm font-bold text-gray-700">
                        <span class="inline-flex items-center">
                            <img src="{{ asset('images/tipo_docs.svg') }}" alt="tipo de documento" class="w-4 h-4 mr-2">
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
                            <option value="NIT">NIT</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="document" class="block mb-1 text-sm font-bold text-gray-700">
                        <span class="inline-flex items-center">
                            <img src="{{ asset('images/docs.svg') }}" alt="documento" class="w-4 h-4 mr-2"> Documento:
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
                                    // Cambiado $moduleName a $moduleJsKey
                                    $groupedPermissions[$moduleJsKey] = []; // Usa la clave JS aquí
                                    foreach ($actionTypes as $action) {
                                        $groupedPermissions[$moduleJsKey][$action] = null;
                                    }
                                }

                                foreach ($permissions as $p) {
                                    foreach ($moduleMappings as $prefix => $moduleJsKey) {
                                        // Iterar con $moduleJsKey
                                        foreach ($actionTypes as $action) {
                                            $expectedPermissionName = $action . ' ' . $prefix; // Construye el nombre de Spatie
                                            if ($p->name === $expectedPermissionName) {
                                                $groupedPermissions[$moduleJsKey][$action] = $p->name; // Asigna con la clave JS
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
                                                <!-- Compara con la clave JS -->
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
                                                    <span class="text-gray-400"
                                                        title="Permiso no disponible para este módulo">-</span>
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

            <div class="flex justify-between items-center mt-auto pt-8">
                <button type="button"
                    class="flex justify-start py-2 px-4 border border-gray-200 font-medium text-gray-700 rounded-full focus:outline-none focus:shadow-outline items-center text-md
                            hover:bg-gray-50 transition duration-150 ease-in-out">
                    <img src="{{ asset('images/Importar.svg') }}" alt="importar csv" class="w-5 h-5 mr-3">
                    Importar CSV
                </button>

                <div class="flex space-x-3">
                    <button type="button" id="prevStepButton"
                        class="btn-secondary px-4 py-2 border border-gray-300 rounded-full text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2 transform rotate-180"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        Atrás
                    </button>

                    <button type="button" id="cancelButton"
                        class="btn-secondary px-4 py-2 border border-gray-300 rounded-full text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 hidden">
                        Cancelar
                    </button>

                    <button type="button" id="nextStepButton"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-3 rounded-full focus:outline-none focus:shadow-outline flex items-center text-md
                                transition duration-150 ease-in-out">
                        Siguiente
                        <img src="{{ asset('images/siguiente.svg') }}" alt="siguiente" class="w-5 h-6 ml-2">
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="confirmModal"
    class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden flex items-center justify-center z-50">
    <div class="relative p-8 bg-white w-full max-w-lg mx-auto rounded-lg shadow-xl transform transition-all sm:my-8 sm:w-full sm:max-w-md"
        role="dialog" aria-modal="true" aria-labelledby="confirmModalTitle">
        <div class="text-center">
            <h3 class="text-xl font-bold text-gray-900 mb-4" id="confirmModalTitle">Confirmar Acción</h3>
            <div id="confirmMessageBody" class="text-left text-gray-700 leading-relaxed mb-6">
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
