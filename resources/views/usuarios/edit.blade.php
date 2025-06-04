@extends('layouts.app') {{-- Asegúrate de que esto apunte a tu layout base --}}

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">
        Editar Usuario: <span class="text-blue-600">{{ $usuario->name }}</span>
    </h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">¡Éxito!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <strong class="font-bold">¡Error!</strong>
            <span class="block sm:inline">Por favor, corrige los siguientes problemas:</span>
            <ul class="mt-3 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-lg p-8 max-w-4xl mx-auto">
        <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- ¡Importante para enviar la solicitud como PUT! --}}

            <h2 class="text-2xl font-semibold mb-4 text-gray-700">Datos del Usuario</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nombre:</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $usuario->name) }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $usuario->email) }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Nueva Contraseña (dejar en blanco para no cambiar):</label>
                    <input type="password" name="password" id="password"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Confirmar Nueva Contraseña:</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <hr class="my-8 border-gray-300">

            <h2 class="text-2xl font-semibold mb-4 text-gray-700">Roles del Usuario</h2>
            <p class="text-gray-600 text-sm mb-4">Selecciona los roles que este usuario debe tener. Un usuario puede tener múltiples roles.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-4 gap-x-6 mb-8">
                @forelse ($roles as $role)
                    <div class="flex items-center">
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}"
                               class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                               @checked(in_array($role->name, $userRoles))>
                        <label for="role_{{ $role->id }}" class="ml-3 text-gray-700 font-medium">{{ $role->name }}</label>
                    </div>
                @empty
                    <p class="text-gray-500 col-span-full">No hay roles disponibles. Asegúrate de haber ejecutado tus seeders.</p>
                @endforelse
            </div>

            <hr class="my-8 border-gray-300">

            <h2 class="text-2xl font-semibold mb-4 text-gray-700">Permisos Directos del Usuario</h2>
            <p class="text-gray-600 text-sm mb-4">
                Asigna permisos individuales al usuario. Ten en cuenta que los permisos asignados directamente
                **prevalecen** sobre los permisos otorgados o negados por los roles. Usa esto con precaución.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-4 gap-x-6 mb-8">
                @forelse ($permissions as $permission)
                    <div class="flex items-center">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="permission_{{ $permission->id }}"
                               class="h-5 w-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                               @checked(in_array($permission->name, $userDirectPermissions))>
                        <label for="permission_{{ $permission->id }}" class="ml-3 text-gray-700 font-medium">{{ $permission->name }}</label>
                    </div>
                @empty
                    <p class="text-gray-500 col-span-full">No hay permisos disponibles. Asegúrate de haber ejecutado tus seeders.</p>
                @endforelse
            </div>

            <div class="flex items-center justify-end mt-8 space-x-4">
                <a href="{{ route('usuarios.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection