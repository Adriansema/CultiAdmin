@extends('layouts.guest')
{{-- todo: El usuario ve el formulario de iniciar sesion, luego Envía el formulario a la ruta POST /login --}}
{{-- ! GET|HEAD  login ..... login › Laravel\Fortify › AuthenticatedSessionController@create --}}
{{-- ! POST      login .... login.store › Laravel\Fortify › AuthenticatedSessionController@store --}}
{{-- ! POST      logout .... logout › Laravel\Fortify › AuthenticatedSessionController@destroy  --}}
{{-- ? Login = Autenticación: Es el evento inicial donde se confirma tu identidad. --}}
{{-- * Sesión = Estado después de la Autenticación: Es el mecanismo que mantiene tu estado de "autenticado" a lo largo 
* de tu interacción con la aplicación, evitando que tengas que loguearte en cada clic o navegación de página. * --}}

@section('content')
    {{-- Fondo de logos superpuestos --}}
    <div class="absolute inset-0 z-0">
        {{-- Logo Cultiva centrado arriba --}}
        {{-- CAMBIOS AQUÍ: Eliminado translate-y-1/2, ajustado top-10, y el tamaño del logo --}}
        <div class="absolute transform -translate-x-1/2 top-60 left-1/2">
            <img src="{{ asset('images/cultivasena.svg') }}" alt="Logo Cultiva" class="w-auto h-20 sm:h-24 opacity-90">
            {{-- h-24 o h-32 (96px o 128px) para controlar la altura y que el ancho se ajuste proporcionalmente --}}
            {{-- w-auto para mantener la proporción, eliminado w-1/2 max-w-xl para evitar problemas de centrado en este contexto --}}
        </div>
    </div>

    {{-- Formulario de inicio de sesión --}}
    <div class="relative z-20 flex flex-col items-center justify-center min-h-screen p-4">
        <form method="POST" action="{{ route('login') }}" class="login-form w-full max-w-md mt-16 sm:mt-24">
            @csrf
            <div class="mb-6" x-data="{ email: '', emailExists: null, debounceTimeout: null }">
                <label for="email" class="block mb-1 text-sm font-bold text-gray-700">Correo electrónico</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                        <img src="{{ asset('images/user.svg') }}" alt="persona" class="w-4 h-4">
                    </span>
                    <input id="email" type="email" name="email" placeholder="ingrese su correo electronico" required
                        autofocus x-model="email" {{-- Enlaza el valor del input a la variable 'email' de Alpine --}}
                        @input.debounce.500ms="
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(() => {
                    if (email.length > 0) { // Solo si hay algo escrito
                        fetch('/check-email', { // Llama a tu endpoint de Laravel
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]').content // Para Laravel
                            },
                            body: JSON.stringify({ email: email })
                        })
                        .then(response => response.json())
                        .then(data => {
                            emailExists = data.exists; // Actualiza la variable de Alpine con el resultado del backend
                        })
                        .catch(error => {
                            console.error('Error checking email:', error);
                            emailExists = null; // O manejar el error como prefieras
                        });
                    } else {
                        emailExists = null; // Resetea si el campo está vacío
                    }
                }, 500); // Debounce de 500ms
            "
                        class="w-full px-3 py-2 pl-10 pr-10 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" />
                    {{-- Importante: añadido 'pr-10' para dejar espacio al icono de validación --}}

                    {{-- Icono de validación (éxito o error) --}}
                    <template x-if="emailExists !== null"> {{-- Muestra el icono solo si ya se ha validado --}}
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <img :src="emailExists ? '{{ asset('images/bien.svg') }}' : '{{ asset('images/mal.svg') }}'"
                                :alt="emailExists ? 'Correo existe' : 'Correo no existe'" class="w-5 h-5"
                                :class="{ 'text-green-500': emailExists, 'text-red-500': !emailExists }" />
                        </span>
                    </template>
                </div>
                {{-- Mensaje de error para el campo de correo --}}
                @error('email')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            {{-- Contraseña --}
            <div class="mb-6" x-data="{ showPassword: false }">
                <label for="password" class="block mb-1 text-sm font-bold text-gray-700">Contraseña</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                        <img src="{{ asset('images/candado.svg') }}" alt="candado" class="w-4 h-4">
                    </span>

                    <input id="password" :type="showPassword ? 'text' : 'password'" name="password"
                        placeholder="ingrese su contraseña" required
                        class="w-full px-3 py-2 pl-10 pr-10 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" />
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 cursor-pointer"
                        @click="showPassword = !showPassword">
                        <img :src="showPassword ? '{{ asset('images/ojo-open.svg') }}' : '{{ asset('images/ojo-close.svg') }}'"
                            alt="Mostrar/Ocultar contraseña" class="w-5 h-5 opacity-50">
                    </span>
                    {{-- Mensaje de error para el campo de contraseña 
                    @error('password')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div> --}}

            {{-- Contraseña --}}
            <div class="mb-6" x-data="{ showPassword: false }">
                <label for="password" class="block mb-1 text-sm font-bold text-gray-700">Contraseña</label>

                {{-- Este es el div que envolverá todo el campo de contraseña, iconos y error --}}
                <div class="relative">
                    {{-- Icono de Candado --}}
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                        <img src="{{ asset('images/candado.svg') }}" alt="candado" class="w-4 h-4">
                    </span>

                    {{-- Campo de Contraseña --}}
                    <input id="password" :type="showPassword ? 'text' : 'password'" name="password"
                        placeholder="ingrese su contraseña" required
                        class="w-full px-3 py-2 pl-10 pr-10 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" />

                    {{-- Icono de Ojo (Mostrar/Ocultar Contraseña) --}}
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 cursor-pointer"
                        @click="showPassword = !showPassword">
                        <img :src="showPassword ? '{{ asset('images/ojo-open.svg') }}' : '{{ asset('images/ojo-close.svg') }}'"
                            alt="Mostrar/Ocultar contraseña" class="w-5 h-5 opacity-50">
                    </span>

                    {{-- Mensaje de error para el campo de contraseña --}}
                    {{-- ¡AHORA DENTRO DEL MISMO CONTENEDOR RELATIVO! --}}
                    @error('password')
                        <span class="text-red-500 text-xs block mt-1">{{ $message }}</span>
                        {{-- Añadí 'block' para asegurar que el span ocupe su propia línea y el 'mt-1' funcione bien --}}
                    @enderror
                </div>
            </div>

            {{-- Recuérdame y olvido --}}
            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center text-sm font-bold text-gray-600">
                    <input type="checkbox" name="remember" class="mr-2">
                    Recuérdame en este dispositivo
                </label>
                <a href="{{ route('password.request') }}" class="text-sm text-purple-600 font-bold hover:underline">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>

            {{-- Botón de ingreso --}}
            <button type="submit"
                class="w-full px-4 py-2 font-semibold text-white transition duration-150 bg-green-600 rounded-full hover:bg-green-700">
                Iniciar Sesión
            </button>
        </form>
    </div>

    <div class="absolute transform -translate-x-1/2 bottom-44 left-1/2">
        <img src="{{ asset('images/sena-logo.svg') }}" alt="Logo SENA" class="h-14 w-auto opacity-90">
    </div>

    {{-- Modal usuario inactivo --}}
    @if (session('inactivo'))
        <div id="inactivoModal" x-data="{ show: true }" {{-- x-show="show" x-init="setTimeout(() => { show = false; }, 5000)" --}} {{-- Se quita la funcion de que se cierre el modal automaticamente por 5 sg --}} {{-- Solo cierra el modal, no redirige si ya estás en login --}}
            class="fixed inset-0 flex items-center justify-center z-50 bg-gray-900 bg-opacity-50">
            <div class="bg-white rounded-3xl shadow-md p-6 max-w-md text-center">
                {{-- Icono --}}
                <img src="{{ asset('images/warning.svg') }}" alt="Icono de advertencia" class="mx-auto h-36 w-56 mb-4">
                <h2 class="text-2xl font-bold text-red-600 mb-4">Cuenta Desactivada</h2>
                <p class="text-gray-700 text-sm">
                    Si crees que esto es un error, contacta a
                    {{-- CAMBIO AQUÍ: apunto a la ruta de PQR --}}
                    <a href="{{ route('pqrs.create') }}" class="underline text-blue-600">
                        nuestro soporte de PQR
                    </a>
                    {{-- O si quieres mantener el email visible pero con un enlace: --}}
                    {{-- <a href="{{ route('pqrs.create') }}" class="underline text-blue-600">
                    soporteayuda2025@gmail.com
                </a> --}}
                    .
                </p>

                <button @click="show = false"
                    class="mt-6 px-4 py-2 bg-[var(--color-iconos4)] text-white rounded hover:bg-green-600">
                    Cerrar
                </button>
            </div>
        </div>
    @endif
@endsection
