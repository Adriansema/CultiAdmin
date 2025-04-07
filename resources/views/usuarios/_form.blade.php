@csrf

<div class="mb-4">
    <x-label for="name" :value="'Nombre'" />
    <x-input id="name" name="name" type="text" class="w-full" value="{{ old('name', $usuario->name ?? '') }}" required />
</div>

<div class="mb-4">
    <x-label for="email" :value="'Email'" />
    <x-input id="email" name="email" type="email" class="w-full" value="{{ old('email', $usuario->email ?? '') }}" required />
</div>

@if(!isset($usuario))
<div class="mb-4">
    <x-label for="password" :value="'Contraseña'" />
    <x-input id="password" name="password" type="password" class="w-full" required />
</div>

<div class="mb-4">
    <x-label for="password_confirmation" :value="'Confirmar Contraseña'" />
    <x-input id="password_confirmation" name="password_confirmation" type="password" class="w-full" required />
</div>
@endif

<div class="mb-4">
    <x-label for="role" :value="'Rol'" />
    <select name="role" class="w-full border-gray-300 rounded shadow-sm">
        @foreach($roles as $role)
            <option value="{{ $role->name }}" @selected(old('role', $usuario->roles->first()->name ?? '') === $role->name)>
                {{ ucfirst($role->name) }}
            </option>
        @endforeach
    </select>
</div>
