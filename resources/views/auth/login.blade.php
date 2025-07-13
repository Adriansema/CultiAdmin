@extends('layouts.guest')

@section('content')
    {{-- Fondo con logos: Cultiva Sena y SENA --}}
    <div class="absolute inset-0 z-0 overflow-hidden"> {{-- Añadido overflow-hidden para evitar scroll si los logos se salen --}}
        {{-- Logo Cultiva Sena (arriba y centrado) --}}
        <div class="absolute transform -translate-x-1/2 top-16 sm:top-24 md:top-32 left-1/2"> {{-- Ajuste de top para diferentes pantallas --}}
            <img src="{{ asset('images/cultivasena.svg') }}" alt="Logo Cultiva" class="w-auto h-20 sm:h-24 md:h-28 opacity-90"> {{-- Ajuste de altura para diferentes pantallas --}}
        </div>
        {{-- Logo SENA (abajo y centrado) --}}
        <div class="absolute transform -translate-x-1/2 bottom-16 sm:bottom-24 md:bottom-32 left-1/2"> {{-- Ajuste de bottom para diferentes pantallas --}}
            <img src="{{ asset('images/sena-logo.svg') }}" alt="Logo SENA" class="w-auto h-16 sm:h-20 md:h-24 opacity-90"> {{-- Ajuste de altura para diferentes pantallas --}}
        </div>
    </div>

    {{-- Formulario de inicio de sesion --}}
    <div class="relative z-20 flex flex-col items-center justify-center min-h-screen p-4 sm:p-6 md:p-8"> {{-- Padding responsivo --}}
        <form method="POST" action="{{ route('login') }}" class="w-full max-w-sm mt-16 sm:max-w-md login-form"> {{-- Ancho máximo responsivo, mt ajustado --}}
            @csrf

            {{-- Campo de Correo Electronico --}}
            <div class="mb-6" x-data="{ email: '{{ old(Laravel\Fortify\Fortify::username()) }}', emailExists: null, debounceTimeout: null }">
                <label for="email" class="block mb-1 text-sm font-bold text-gray-700">Correo electrónico</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                        <img src="{{ asset('images/user.svg') }}" alt="persona" class="w-4 h-4">
                    </span>
                    <input id="email" type="email" name="email" placeholder="ingrese su correo electrónico" required
                        autofocus x-model="email"
                        @input.debounce.500ms="
                            clearTimeout(debounceTimeout);
                            debounceTimeout = setTimeout(() => {
                                if (email.length > 0) {
                                    fetch('./check-email', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]').content
                                        },
                                        body: JSON.stringify({ email: email })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        emailExists = data.exists;
                                    })
                                    .catch(error => {
                                        console.error('Error checking email:', error);
                                        emailExists = null;
                                    });
                                } else {
                                    emailExists = null;
                                }
                            }, 500);
                        "
                        class="w-full px-3 py-2 pl-10 pr-10 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" />

                    {{-- Icono de validacion (exito o error) --}}
                    <template x-if="emailExists !== null">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <img :src="emailExists ? '{{ asset('images/bien.svg') }}' : '{{ asset('images/mal.svg') }}'"
                                :alt="emailExists ? 'Correo existe' : 'Correo no existe'" class="w-5 h-5"
                                :class="{ 'text-green-500': emailExists, 'text-red-500': !emailExists }" />
                        </span>
                    </template>
                </div>
                @if ($errors->has(Laravel\Fortify\Fortify::username()))
                    <div class="mb-4 text-sm text-red-500">
                        {{ $errors->first(Laravel\Fortify\Fortify::username()) }}
                    </div>
                @endif
                {{-- Mensaje de exito (ej. despues de restablecer contrasena) --}}
                @if (session('status'))
                    <div class="mb-4 text-sm font-medium text-green-600">
                        {{ session('status') }}
                    </div>
                @endif
            </div>

            {{-- Contrasena --}}
            <div class="mb-6" x-data="{ showPassword: false }">
                <label for="password" class="block mb-1 text-sm font-bold text-gray-700">Contraseña</label>

                {{-- Este es el div que envolvera todo el campo de contrasena, iconos y error --}}
                <div class="relative">
                    {{-- Icono de Candado --}}
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                        <img src="{{ asset('images/candado.svg') }}" alt="candado" class="w-4 h-4">
                    </span>

                    {{-- Campo de Contrasena --}}
                    <input id="password" :type="showPassword ? 'text' : 'password'" name="password"
                        placeholder="ingrese su contraseña" required
                        class="w-full px-3 py-2 pl-10 pr-10 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"/>

                    {{-- Icono de Ojo (Mostrar/Ocultar Contrasena) --}}
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 cursor-pointer"
                        @click="showPassword = !showPassword">
                        <img :src="showPassword ? '{{ asset('images/ojo-open.svg') }}' : '{{ asset('images/ojo-close.svg') }}'"
                            alt="Mostrar/Ocultar contrasena" class="w-5 h-5 opacity-50">
                    </span>
                </div>
                {{-- Mensaje de error para el campo de contrasena --}}
                @error('password')
                    <span class="block mt-1 text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            {{-- Recuerdame y olvido --}}
            <div class="flex flex-col items-center justify-between mb-6 space-y-2 sm:flex-row sm:space-y-0"> {{-- Se apilan en móvil, en fila en sm+ --}}
                <label class="flex items-center text-sm font-bold text-gray-600">
                    <input type="checkbox" name="remember"
                        class="w-5 h-5 mr-2 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    Recuérdame en este dispositivo
                </label>
                <a href="{{ route('password.request') }}" class="text-sm font-bold text-purple-600 hover:underline">
                    ¿Olvidaste tu contrasena?
                </a>
            </div>

            {{-- Boton de ingreso --}}
            <button type="submit"
                class="w-full px-4 py-3 text-lg font-semibold text-white transition duration-150 bg-green-600 rounded-full hover:bg-green-700"> {{-- Aumentado padding y tamaño de texto --}}
                Iniciar sesión
            </button>
        </form>
    </div>

    {{-- Modal usuario inactivo --}}
    @if (session('inactivo'))
        <div id="inactivoModal" x-data="{ show: true }" x-show="show"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900 bg-opacity-50"> {{-- Añadido p-4 para padding en móvil --}}
            <div class="w-full max-w-md p-6 text-center bg-white shadow-md rounded-3xl"> {{-- w-full para móvil --}}
                {{-- Icono --}}
                <img src="{{ asset('images/warning.svg') }}" alt="Icono de advertencia" class="w-48 h-32 mx-auto mb-4 sm:w-56 sm:h-36"> {{-- Ajuste de tamaño de imagen --}}
                <h2 class="mb-4 text-xl font-bold text-red-600 sm:text-2xl">Cuenta desactivada</h2> {{-- Título responsivo --}}
                <p class="text-sm text-gray-700 sm:text-base"> {{-- Texto responsivo --}}
                    Si crees que esto es un error, contácta a
                    <a href="{{ route('pqrs.create') }}" class="text-blue-600 underline">
                        nuestro soporte de PQRs
                    </a>
                </p>

                <button @click="show = false"
                    class="mt-6 px-6 py-3 bg-[var(--color-iconos4)] text-white rounded-full hover:bg-green-600 text-lg"> {{-- Aumentado padding y tamaño de texto --}}
                    Cerrar
                </button>
            </div>
        </div>
    @endif
@endsection
