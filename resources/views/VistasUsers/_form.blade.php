{{-- resources/views/VistasUsers/_form.blade.php --}}
<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="block font-semibold">Nombre</label>
        <input name="name" value="{{ old('name', $usuario?->name) }}" class="w-full border-gray-300 rounded" required />
    </div>

    <div>
        <label class="block font-semibold">Correo</label>
        <input name="email" type="email" value="{{ old('email', $usuario?->email) }}" class="w-full border-gray-300 rounded" required />
    </div>

    <div>
        <label class="block font-semibold">Contraseña</label>
        <input name="password" type="password" autocomplete="new-password" class="w-full border-gray-300 rounded" />
    </div>

    <div>
        <label class="block font-semibold">Teléfono</label>
        <input name="telefono" value="{{ old('telefono', $usuario?->telefono) }}" class="w-full border-gray-300 rounded" />
    </div>

    <div>
        <label class="block font-semibold">Estado</label>
        <select name="estado" class="w-full border-gray-300 rounded">
            <option value="activo" @selected(old('estado', $usuario?->estado) === 'activo')>Activo</option>
            <option value="inactivo" @selected(old('estado', $usuario?->estado) === 'inactivo')>Inactivo</option>
        </select>
    </div>

    <div>
        <label class="block font-semibold">Roles</label>
        <select name="roles[]" multiple class="w-full border-gray-300 rounded">
            @foreach($roles as $id => $name)
                <option value="{{ $id }}" @if(isset($usuario) && $usuario->roles->pluck('id')->contains($id)) selected @endif>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
