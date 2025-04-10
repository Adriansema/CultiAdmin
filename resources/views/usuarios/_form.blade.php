@csrf

<div class="mb-4">
    <x-label for="name" value="Nombre" />
    <x-input id="name" name="name" type="text" class="block w-full mt-1"
             value="{{ old('name', $usuario->name ?? '') }}" required />
    @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <x-label for="email" value="Correo electrónico" />
    <x-input id="email" name="email" type="email" class="block w-full mt-1"
             value="{{ old('email', $usuario->email ?? '') }}" required />
    @error('email')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

@if(Route::currentRouteName() === 'usuarios.create')
    <div class="mb-4">
        <x-label for="password" value="Contraseña" />
        <x-input id="password" name="password" type="password" class="block w-full mt-1" required />
        @error('password')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <x-label for="password_confirmation" value="Confirmar Contraseña" />
        <x-input id="password_confirmation" name="password_confirmation" type="password" class="block w-full mt-1" required />
    </div>
@endif

<div class="mb-4">
    <x-label for="role" value="Rol" />
    <select name="role" id="role" class="block w-full mt-1 border-gray-300 rounded shadow-sm">
        <option value="">-- Seleccionar rol --</option>
        @foreach($roles as $rol)
            <option value="{{ $rol->name }}"
                {{ old('role', $usuario->roles->first()->name ?? '') === $rol->name ? 'selected' : '' }}>
                {{ ucfirst($rol->name) }}
            </option>
        @endforeach
    </select>
    @error('role')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>


