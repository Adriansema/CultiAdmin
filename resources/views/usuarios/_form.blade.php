@csrf
{{-- ? Se encarga de crear un nuevo usuario con rol, email, name y password 
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
@endif --}}

{{-- Modal de Éxito --}}
@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false; }, 2000)" {{-- Se cierra automáticamente después de 2 segundos --}} x-transition
        class="fixed inset-0 flex items-center justify-center z-50 bg-gray-900 bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm text-center relative">
            {{-- Botón de cerrar (la "X") --}}
            <button @click="show = false"
                class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl font-bold leading-none focus:outline-none">
                &times;
            </button>

            {{-- Icono de Éxito --}}
            <img src="{{ asset('images/check.svg') }}" alt="Icono de éxito" class="mx-auto h-24 w-24 mb-4">
            <h2 class="text-2xl font-bold text-green-600 mb-4">¡Éxito!</h2>
            <p class="text-gray-700 text-base">
                {{ session('success') }}
            </p>
        </div>
    </div>
@endif

<div class="mb-6" x-data="{ name: '' }">
    <label for="name" class="block mb-1 text-sm font-bold text-gray-700">Nombre:</label>
    <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
            <img src="{{ asset('images/user.svg') }}" alt="persona" class="w-4 h-4">
        </span>
        <input id="name" type="name" name="name" placeholder="ingrese su nombre"
            value="{{ old('name', $usuario->name ?? '') }}" required
            class="w-full px-3 py-2 pl-10 pr-10 text-sm border border-gray-300 rounded-full focus:outline-none 
            focus:ring-2 focus:ring-green-500 focus:border-transparent" />

        {{-- Mensaje de éxito --}}
        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif
    </div>
</div>

<div class="mb-6" x-data="{ email: '' }">
    <label for="email" class="block mb-1 text-sm font-bold text-gray-700">Correo electrónico:</label>
    <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
            <img src="{{ asset('images/email.svg') }}" alt="persona" class="w-4 h-4">
        </span>
        <input id="email" type="email" name="email" placeholder="ingrese su correo electronico" required
            class="w-full px-3 py-2 pl-10 pr-10 text-sm border border-gray-300 rounded-full focus:outline-none 
            focus:ring-2 focus:ring-green-500 focus:border-transparent" />

        {{-- Mensaje de éxito --}}
        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif
    </div>
</div>

@if (Route::currentRouteName() === 'usuarios.create')
    <div class="mb-6" x-data="{ showPassword: false }">
        <label for="password" class="block mb-1 text-sm font-bold text-gray-700">Contraseña:</label>

        {{-- Este es el div que envolverá todo el campo de contraseña, iconos y error --}}
        <div class="relative">
            {{-- Icono de Candado --}}
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                <img src="{{ asset('images/candado.svg') }}" alt="candado" class="w-4 h-4">
            </span>

            {{-- Campo de Contraseña --}}
            <input id="password" :type="showPassword ? 'text' : 'password'" name="password"
                placeholder="ingrese su contraseña" required
                class="w-full px-3 py-2 pl-10 pr-10 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-2 
                focus:ring-green-500 focus:border-transparent" />

            {{-- Icono de Ojo (Mostrar/Ocultar Contraseña) --}}
            <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 cursor-pointer"
                @click="showPassword = !showPassword">
                <img :src="showPassword ? '{{ asset('images/ojo-open.svg') }}' : '{{ asset('images/ojo-close.svg') }}'"
                    alt="Mostrar/Ocultar contraseña" class="w-5 h-5 opacity-50">
            </span>
        </div>
        {{-- Mensaje de error para el campo de contraseña --}}
        @error('password')
            <span class="text-red-500 text-xs block mt-1">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-6" x-data="{ showConfirmPassword: false }"> {{-- Usa una nueva variable para este campo --}}
        <label for="password_confirmation" class="block mb-1 text-sm font-bold text-gray-700">Confirmar
            Contraseña:</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                <img src="{{ asset('images/candado.svg') }}" alt="candado" class="w-4 h-4">
            </span>
            <input id="password_confirmation" :type="showConfirmPassword ? 'text' : 'password'"
                name="password_confirmation" {{-- Importante: name="password_confirmation" --}} placeholder="confirme su contraseña" required
                class="w-full px-3 py-2 pl-10 pr-10 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-2 
            focus:ring-green-500 focus:border-transparent" />

            <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 cursor-pointer"
                @click="showConfirmPassword = !showConfirmPassword"> {{-- Usa la nueva variable aquí --}}
                <img :src="showConfirmPassword ? '{{ asset('images/ojo-open.svg') }}' : '{{ asset('images/ojo-close.svg') }}'"
                    {{-- Y aquí --}} alt="Mostrar/Ocultar contraseña" class="w-5 h-5 opacity-50">
            </span>
        </div>

        @error('password_confirmation')
            <span class="text-red-500 text-xs block mt-1">{{ $message }}</span>
        @enderror
    </div>
@endif
