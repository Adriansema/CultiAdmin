@extends('layouts.app') 

@section('content')
    {{-- Contenedor principal con ancho fijo y centrado --}}
    <div class="max-w-3xl mx-auto px-4 py-8">
        {{-- Contenedor del formulario con fondo y sombra, más compacto --}}
        <div class="p-8 space-y-6 bg-white shadow-md rounded-3xl border border-gray-300">

            {{-- Encabezado y navegación por pasos (como en la imagen) --}}
            <div class="flex justify-between items-center mb-6">
                {{-- Título basado en la imagen "Nuevo usuario" --}}
                <h1 class="text-2xl font-bold text-gray-800">Nuevo usuario</h1>
                <button type="button" class="text-gray-500 hover:text-gray-700" onclick="window.history.back()">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Indicador de pasos --}}
            <div class="flex items-center justify-center mb-8">
                <div class="flex items-center text-gray-700" :class="{ 'active': currentStep === 1, 'completed': currentStep > 1 }">
                    <img src="{{ asset('images/1paso.svg') }}" alt="paso 1" class="w-7 h-10 mr-2">
                    <span class="font-semibold" :class="{ 'bg-success': currentStep > 1 }">Datos básicos</span>
                </div>
                <div class="mx-4 text-gray-400">
                    <img src="{{ asset('images/medio_1_2.svg') }}" alt="paso 1" class="w-2 h-3 mr-2">
                </div>
                <div class="flex items-center text-gray-700 font-semibold" :class="{ 'active': currentStep === 2 }">
                    <img src="{{ asset('images/2paso.svg') }}" alt="paso 2" class="w-7 h-10 mr-2">
                    <span :class="{ 'bg-success': currentStep > 2 }">Roles y permisos</span>
                </div>
            </div>

            {{-- Formulario para actualizar roles y permisos --}}
            <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Sección para mostrar errores de validación (general) --}}
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- --- SELECCIÓN DE ROLES (checkbox BUTTONS - como en la imagen) --- --}}
                {{-- La imagen sugiere checkbox buttons para roles, asumiendo un solo rol primario.
                     Si necesitas múltiples roles (Spatie los permite), deberías usar checkboxes. --}}
                <h4 class="text-md font-bold mb-6 flex items-center gap-4">
                    Rol
                    <div class="flex flex-wrap gap-2">
                        @foreach ($roles as $role)
                            <label
                                class="inline-flex items-center px-4 py-2 rounded-lg cursor-pointer transition-all duration-300 role-label
                                {{ in_array($role->name, old('roles', $userRoles))
                                    ? 'bg-indigo-200 text-indigo-800'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                                data-role-name="{{ $role->name }}" {{-- Añadimos un atributo de datos --}}>
                                {{-- Lógica para la imagen --}}
                                <img src="{{ asset('images/' . (in_array($role->name, old('roles', $userRoles)) ? 'con_marca.svg' : 'sin_marca.svg')) }}"
                                    alt="Icono de selección de rol" class="w-5 h-5 mr-2 role-icon"> {{-- Añadimos una clase para JavaScript --}}

                                <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                    id="role_{{ \Illuminate\Support\Str::slug($role->name) }}" {{-- Añadimos un ID --}}
                                    {{ in_array($role->name, old('roles', $userRoles)) ? 'checked' : '' }}
                                    class="hidden role-checkbox"> {{-- Añadimos una clase para JavaScript --}}
                                <span class="text-sm font-medium">{{ $role->name }}</span>
                            </label>
                        @endforeach
                        @error('roles')
                            <p class="text-red-500 text-xs mt-1 w-full">{{ $message }}</p>
                        @enderror
                    </div>
                </h4>

                {{-- --- TABLA PARA ASIGNACIÓN DE PERMISOS (DIRECTOS) --- --}}
                <h4 class="text-md font-bold mb-1 flex items-center">
                    Permisos directos del usuario
                </h4>
                <p class="text-gray-600 text-sm mb-6">Asigna permisos adicionales ahora o modifícalos más tarde en Gestión
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
                                // Define tus acciones estándar para las columnas de la tabla
                                $actionTypes = ['crear', 'editar', 'validar', 'eliminar'];
                                // Define un mapeo de prefijos de permisos a nombres de módulos legibles
                                // y para el orden de aparición.
                                $moduleMappings = [
                                    'usuario' => 'Usuarios',
                                    'producto' => 'Productos',
                                    'noticia' => 'Noticias',
                                    'boletin' => 'Boletines',
                                ];

                                // Primero, inicializamos la estructura para cada módulo conocido
                                foreach ($moduleMappings as $prefix => $moduleName) {
                                    $groupedPermissions[$moduleName] = [];
                                    foreach ($actionTypes as $action) {
                                        // Valor predeterminado a null (no existe un permiso para esa acción en ese módulo)
                                        $groupedPermissions[$moduleName][$action] = null;
                                    }
                                }

                                // Ahora, poblamos con los permisos REALMENTE existentes
                                // $permissions contiene todos los objetos Permission del sistema
                                foreach ($permissions as $p) {
                                    foreach ($moduleMappings as $prefix => $moduleName) {
                                        foreach ($actionTypes as $action) {
                                            $expectedPermissionName = $action . ' ' . $prefix;
                                            if ($p->name === $expectedPermissionName) {
                                                // Si el permiso existe, lo asignamos.
                                                $groupedPermissions[$moduleName][$action] = $p->name;
                                                break 2; // Salir de los bucles internos y pasar al siguiente permiso $p
                                            }
                                        }
                                    }
                                }
                            @endphp

                            @forelse ($groupedPermissions as $moduleName => $actions)
                                {{-- Solo mostrar la fila si el módulo tiene al menos un permiso que se pueda seleccionar --}}
                                @if (count(array_filter($actions)) > 0)
                                    <tr>
                                        <td
                                            class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 flex items-center">
                                            {{-- Iconos para módulos - Ajusta según tus necesidades y rutas de imágenes --}}
                                            @if ($moduleName === 'Productos')
                                                <img src="{{ asset('images/planta.svg') }}" alt="Productos"
                                                    class="w-5 h-5 mr-2">
                                            @elseif ($moduleName === 'Noticias')
                                                <img src="{{ asset('images/noticia.svg') }}" alt="Noticias"
                                                    class="w-5 h-5 mr-2">
                                            @elseif ($moduleName === 'Boletines')
                                                <img src="{{ asset('images/boletin.svg') }}" alt="Boletines"
                                                    class="w-5 h-5 mr-2">
                                            @elseif ($moduleName === 'Usuarios')
                                                <img src="{{ asset('images/gestion.svg') }}" alt="Usuarios"
                                                    class="w-5 h-5 mr-2">
                                            @else
                                                {{-- Icono por defecto si no hay un icono específico --}}
                                            @endif
                                            {{ $moduleName }}
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
                                                        {{ in_array($permissionName, old('permissions', $allUserGrantedPermissions)) ? 'checked' : '' }}
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
                                        No hay permisos definidos en el sistema o no se pudieron agrupar para esta vista.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Botón "Siguiente" --}}
                <div class="flex justify-between items-center">
                    <button type="button"
                        class="flex justify-start py-2 px-4 border border-gray-200 font-medium text-gray-700 rounded-full">
                        <img src="{{ asset('images/Importar.svg') }}" alt="siguiente" class="w-5 h-5 mr-3">
                        Importar CSV
                    </button>
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 justify-end text-white font-bold py-2 px-3 rounded-full focus:outline-none focus:shadow-outline flex items-center text-md">
                        Siguiente
                        <img src="{{ asset('images/siguiente.svg') }}" alt="siguiente" class="w-5 h-6 ml-2">
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

 