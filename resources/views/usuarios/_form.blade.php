@csrf
<!-- Verifica si hay un mensaje de éxito -->
    @if (session('success'))
    <div class="flex items-center p-4 mb-4 text-green-700 bg-green-100 border-l-4 border-green-500">
        <!-- Icono de éxito en SVG -->
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4m0 0l6-6M5 13l2 2l4-4m0 0l6-6"></path>
        </svg>
        <p class="font-semibold">{{ session('success') }}</p>
    </div>
    @endif


@if ($errors->any())
<div class="mt-2 text-red-600">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="mb-4">
    <x-label for="name" value="Nombre" />
    <x-input id="name" name="name" type="text" class="block w-full mt-1" value="{{ old('name', $usuario->name ?? '') }}"
        required />
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
    <x-input id="password_confirmation" name="password_confirmation" type="password" class="block w-full mt-1"
        required />
</div>
@endif

<div class="mb-4">
    <x-label for="role" value="Rol" />
    <select name="role" id="role" class="block w-full mt-1 border-gray-300 rounded shadow-sm">
        <option value="">-- Seleccionar rol --</option>
        @foreach($roles as $rol)
        <option value="{{ $rol->name }}" {{ old('role', optional($usuario)->roles->first()->name ?? '') === $rol->name ?
            'selected' : '' }}>
            {{ ucfirst($rol->name) }}
        </option>
        @endforeach
    </select>
    @error('role')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
