@extends('layouts.app')

@section('content')
    <div class="container px-4 py-8 mx-auto">
        <h1 class="mb-6 text-3xl font-bold text-gray-800">
            Editar Usuario: <span class="text-blue-600">{{ $usuario->name }}</span>
        </h1>
        {{-- Integración de Breadcrumbs si usas el paquete diglactic/laravel-breadcrumbs --}}
        @if (isset($breadcrumbs))
            {!! Breadcrumbs::render('usuarios.edit', $usuario) !!}
        @endif

        {{-- Mensajes de sesión --}}
        @if (session('success'))
            <div class="relative px-4 py-3 mb-4 text-green-700 bg-green-100 border border-green-400 rounded" role="alert">
                <strong class="font-bold">¡Éxito!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="relative px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
                <strong class="font-bold">¡Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if (session('info'))
            <div class="relative px-4 py-3 mb-4 text-blue-700 bg-blue-100 border border-blue-400 rounded" role="alert">
                <strong class="font-bold">¡Información!</strong>
                <span class="block sm:inline">{{ session('info') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="relative px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded">
                <strong class="font-bold">¡Error de Validación!</strong>
                <span class="block sm:inline">Por favor, corrige los siguientes problemas:</span>
                <ul class="mt-3 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Protección principal para todo el formulario de edición de usuario --}}
        {{-- El @can('update', $usuario) en el controlador ya debería manejar el acceso a esta página --}}
        {{-- Este @can aquí es más para mostrar/ocultar el formulario si la política lo permite --}}
        @can('update', $usuario)
            <div class="max-w-4xl p-8 mx-auto bg-white rounded-lg shadow-lg">
                <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- **INICIO: Mensaje de Advertencia para administrador** --}}
                    {{-- Comprueba si el usuario logueado es el que se está editando Y si es administrador --}}
                    @if (Auth::id() === $usuario->id && Auth::user()->hasRole('administrador'))
                        <div class="p-4 mb-6 text-yellow-700 bg-yellow-100 border-l-4 border-yellow-500 rounded-md"
                            role="alert">
                            <p class="font-bold">¡Advertencia: Estás editando tu propio perfil de administrador!</p>
                            <p class="text-sm">Ten extrema precaución al modificar tus propios roles o permisos. Si te quitas el
                                rol de "administrador" o los permisos esenciales, podrías perder el acceso a funciones críticas
                                del sistema. Asegúrate de tener al menos otro administrador o una forma de recuperar el control.
                            </p>
                        </div>
                    @endif
                    {{-- **FIN: Mensaje de Advertencia** --}}

                    {{-- Sección de Datos Básicos del Usuario --}}
                    <h2 class="mb-4 text-2xl font-semibold text-gray-700">Datos del Usuario</h2>
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nombre:</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $usuario->name) }}"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @error('name')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $usuario->email) }}"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @error('email')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700">Contraseña (dejar en blanco para
                            no cambiar):</label>
                        <input type="password" name="password" id="password"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @error('password')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar
                            Contraseña:</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>

                    {{-- Sección de Roles y Permisos (condicional a 'gestionar_roles_y_permisos') --}}
                    {{-- Esto asegura que solo los usuarios autorizados vean y modifiquen roles/permisos --}}
                    @can('editar roles y permisos de usuario', $usuario)
                        {{-- 'editar roles y permisos de usuario' es el permiso que necesitas en tu UserPolicy --}}
                        <hr class="my-8 border-gray-300">

                        <h2 class="mb-4 text-2xl font-semibold text-gray-700">Roles del Usuario</h2>
                        <p class="mb-4 text-sm text-gray-600">Selecciona los roles que este usuario debe tener. Los roles confieren
                            conjuntos de permisos.</p>
                        <div class="grid grid-cols-1 mb-8 md:grid-cols-2 lg:grid-cols-3 gap-y-4 gap-x-6">
                            @forelse ($roles as $role)
                                <div class="flex items-center">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                        id="role_{{ $role->id }}"
                                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        @checked(in_array($role->name, $userRoles))>
                                    <label for="role_{{ $role->id }}" class="ml-3 text-sm font-medium text-gray-700">
                                        {{ $role->name }}
                                    </label>
                                </div>
                            @empty
                                <p class="text-gray-500 col-span-full">No hay roles disponibles. Asegúrate de haber ejecutado tus
                                    seeders.</p>
                            @endforelse
                        </div>

                        <hr class="my-8 border-gray-300">

                        <h2 class="mb-4 text-2xl font-semibold text-gray-700">Permisos Directos del Usuario</h2>
                        <p class="mb-4 text-sm text-gray-600">
                            Marca un permiso para asignarlo directamente a este usuario. Desmárcalo para revocarlo.
                            Si un permiso se obtiene a través de un rol, aparecerá con "(Vía Rol)" y no podrá ser modificado
                            directamente desde aquí.
                        </p>
                        <div class="grid grid-cols-1 mb-8 md:grid-cols-2 lg:grid-cols-3 gap-y-4 gap-x-6">
                            @forelse ($permissions as $permission)
                                {{-- Definimos las variables para cada permiso dentro del bucle --}}
                                @php
                                    $isDirect = in_array($permission->name, $userDirectPermissions);
                                    $isViaRole = in_array($permission->name, $userPermissionsViaRoles);
                                    // El checkbox se deshabilita si el permiso se obtiene VIA ROL y NO está asignado DIRECTAMENTE
                                    $isDisabled = $isViaRole && !$isDirect;
                                @endphp

                                <div class="flex items-center">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        id="permission_{{ $permission->id }}"
                                        class="h-5 w-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500 {{ $isDisabled ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        @checked($isDirect || ($isViaRole && !$isDirect)) {{-- Marcado si es directo O si es vía rol pero NO directo --}} {{ $isDisabled ? 'disabled' : '' }}>
                                    {{-- Atributo disabled --}}

                                    <label for="permission_{{ $permission->id }}" class="ml-3 text-sm font-medium text-gray-700">
                                        {{ $permission->name }}
                                        @if ($isViaRole && !$isDirect)
                                            <span class="ml-2 text-xs font-medium text-blue-600">(Vía Rol)</span>
                                        @endif
                                    </label>
                                </div>
                            @empty
                                <p class="text-gray-500 col-span-full">No hay permisos disponibles. Asegúrate de haber ejecutado tus
                                    seeders.</p>
                            @endforelse
                        </div>
                    @else
                        {{-- Mensaje si el usuario logueado no tiene permiso para gestionar roles/permisos --}}
                        <div class="p-4 mb-6 text-gray-700 bg-red-400 border-l-4 border-gray-300 rounded-lg" role="alert">
                            <p class="font-bold">Acceso Restringido</p>
                            <p class="text-sm">No tienes los permisos necesarios para modificar los roles y permisos de este
                                usuario.</p>
                        </div>
                    @endcan

                    <div class="flex flex-col gap-4 mt-6 sm:flex-row sm:justify-between sm:items-center">
                        <a href="{{ route('usuarios.index') }}"
                            class="px-4 py-2 text-center text-white bg-gray-600 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Volver a la lista de usuarios
                        </a>

                        <button type="submit"
                            class="inline-flex items-center px-6 py-3 text-sm font-medium text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        @else
            {{-- Mensaje si el usuario logueado no tiene permiso para editar al usuario (incluso los datos básicos) --}}
            <div class="p-4 mb-6 text-red-700 bg-red-100 border-l-4 border-red-500 rounded-lg" role="alert">
                <p class="font-bold">Acceso Denegado</p>
                <p class="text-sm">No tienes permiso para editar este perfil de usuario.</p>
                <a href="{{ route('usuarios.index') }}" class="inline-block mt-4 text-blue-600 hover:text-blue-800">Volver a
                    la lista de usuarios</a>
            </div>
        @endcan
    </div>
@endsection
