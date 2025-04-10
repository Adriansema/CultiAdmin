@csrf

<!-- actualizacion 09/04/2025-->

<div class="mb-4">
    <x-label for="name" value="Nombre" />
    <x-input id="name" name="name" type="text" class="w-full" value="{{ old('name', $usuario->name ?? '') }}" required />
</div>

<div class="mb-4">
    <x-label for="email" value="Correo" />
    <x-input id="email" name="email" type="email" class="w-full" value="{{ old('email', $usuario->email ?? '') }}" required />
</div>

@if(!isset($usuario))
<div class="mb-4">
    <x-label for="password" value="Contraseña" />
    <x-input id="password" name="password" type="password" class="w-full" required />
</div>

<div class="mb-4">
    <x-label for="password_confirmation" value="Confirmar contraseña" />
    <x-input id="password_confirmation" name="password_confirmation" type="password" class="w-full" required />
</div>
@endif

<div class="mb-4">
    <x-label for="role" value="Rol" />
    <select name="role" id="role" class="w-full border-gray-300 rounded shadow-sm" required>
        <option value="">Seleccione un rol</option>
        @foreach($roles as $rol)
            <option value="{{ $rol->name }}" {{ old('role', $usuario->roles->pluck('name')->first() ?? '') == $rol->name ? 'selected' : '' }}>
                {{ ucfirst($rol->name) }}
            </option>
        @endforeach
    </select>
</div>

@php
    $rolActual = old('role') ?? ($usuario->roles->first()->name ?? null);
@endphp

<option value="{{ $rol->name }}" {{ $rolActual === $rol->name ? 'selected' : '' }}>
    {{ ucfirst($rol->name) }}
</option>


