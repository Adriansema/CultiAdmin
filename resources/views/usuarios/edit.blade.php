@extends('layouts.app') {{-- Asegúrate de que esto apunta a tu layout principal --}}

@section('content')
    <div class="container mx-auto px-4 py-8">

        <h1 class="text-4xl font-extrabold text-gray-900 mb-6 pb-2 border-b-2 border-gray-200 flex items-center">
                <img src="{{ asset('images/user.svg') }}" alt="persona" class="w-10 h-7">
                Editar Usuario: <span class="ml-2 text-indigo-700">{{ $usuario->name }}</span>
            </h1>
        
        <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST"
            class="bg-white shadow-md rounded-lg p-6 mt-4">
            @csrf
            @method('PUT')

            {{-- Sección para mostrar errores de validación --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h4 class="text-lg font-semibold mb-4 mt-6">Roles del usuario</h4>
            <p class="text-gray-600 mb-6">Asigna o revoca los roles de este usuario.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                @foreach ($roles as $role) {{-- Itera sobre TODOS los roles disponibles --}}
                    <div class="flex items-center">
                        <input type="checkbox" id="role_{{ $role->id }}" name="roles[]" value="{{ $role->name }}"
                            {{ in_array($role->name, old('roles', $userRoles)) ? 'checked' : '' }}
                            class="form-checkbox h-5 w-5 text-indigo-600 rounded">
                        <label for="role_{{ $role->id }}" class="ml-2 text-gray-700 text-sm">
                            {{ $role->name }}
                        </label>
                    </div>
                @endforeach
            </div>

            <h4 class="text-lg font-semibold mb-4 mt-6">Permisos del Usuario</h4>
            <p class="text-gray-600 mb-6">Marca los permisos que este usuario debe tener. **NOTA IMPORTANTE:** Los permisos que un usuario obtiene a través de sus roles asignados no pueden ser revocados desde esta sección. Para modificar esos permisos, edita el rol correspondiente en la <a href="{{ route('usuarios.index') }}" class="text-blue-600 hover:underline">Gestión de Roles</a>.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                @forelse ($permissions as $permission) {{-- Iteramos sobre TODOS los permisos disponibles --}}
                    <div class="flex items-center">
                        <input type="checkbox" id="permission_{{ $permission->id }}" name="permissions[]"
                            value="{{ $permission->name }}"
                            {{ in_array($permission->name, old('permissions', $allUserGrantedPermissions)) ? 'checked' : '' }}
                            class="form-checkbox h-5 w-5 text-indigo-600 rounded"> {{-- Checkbox editable --}}
                        <label for="permission_{{ $permission->id }}" class="ml-2 text-gray-700 text-sm">
                            {{ $permission->name }}
                        </label>
                    </div>
                @empty
                    <p class="text-gray-500 col-span-3">No hay permisos definidos en el sistema.</p>
                @endforelse
            </div>

            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('usuarios.index') }}"
                    class="bg-[var(--color-iconos)] hover:bg-[var(--color-iconos6)] text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Volver
                </a>
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Actualizar Usuario
                </button>
            </div>
        </form>
    </div>
@endsection