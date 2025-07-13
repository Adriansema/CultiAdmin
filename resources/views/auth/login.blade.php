@extends('layouts.guest')

@section('content')
    <div class="absolute inset-0 z-0">
        <div class="absolute transform -translate-x-1/2 top-60 left-1/2">
            <img src="{{ asset('images/cultivasena.svg') }}" alt="Logo Cultiva" class="w-auto h-24 sm:h-24 opacity-90">
        </div>
    </div>

    {{-- Formulario de inicio de sesion --}}
    <div class="relative z-20 flex flex-col items-center justify-center min-h-screen p-4">
        <form method="POST" action="{{ route('login') }}" class="w-full max-w-md mt-16 login-form sm:mt-24">
            @csrf

            {{-- Campo de Correo Electronico --}}
            <div class="mb-6">
                <label for="email" class="block mb-1 text-sm font-bold text-gray-700">Correo electrónico</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                        <img src="{{ asset('images/user.svg') }}" alt="persona" class="w-4 h-4">
                    </span>
                    <input id="email" type="email" name="email" placeholder="ingrese su correo electrónico" required
                        autofocus value="{{ old(Laravel\Fortify\Fortify::username()) }}"
                        class="w-full px-3 py-2 pl-10 pr-10 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" />

                    {{-- Contenedor para el icono de validación JS --}}
                    <span id="email-icon-container" class="absolute inset-y-0 right-0 flex items-center pr-3">
                        {{-- Los iconos se inyectarán aquí por JavaScript --}}
                    </span>
                </div>
                {{-- Div para mostrar errores de validación JS/Backend --}}
                <div id="email-error-message" class="mb-4 text-sm text-red-500" style="display:none;"></div>

                {{-- Errores de Laravel (ocultos por JS si el JS ya los maneja) --}}
                @if ($errors->has(Laravel\Fortify\Fortify::username()))
                    <div id="laravel-email-error" class="mb-4 text-sm text-red-500">
                        {{ $errors->first(Laravel\Fortify\Fortify::username()) }}
                    </div>
                @endif
                {{-- Mensaje de exito (ej. despues de restablecer contrasena) --}}
                @if (session('status'))
                    <div id="session-status-message" class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif
            </div>

            {{-- Contraseña --}}
            <div class="mb-6">
                <label for="password" class="block mb-1 text-sm font-bold text-gray-700">Contraseña</label>

                <div class="relative">
                    {{-- Icono de Candado --}}
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                        <img src="{{ asset('images/candado.svg') }}" alt="candado" class="w-4 h-4">
                    </span>

                    {{-- Campo de Contraseña --}}
                    <input id="password" type="password" name="password" placeholder="ingrese su contraseña" required
                        class="w-full px-3 py-2 pl-10 pr-10 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" />

                    {{-- Icono de Ojo (Mostrar/Ocultar Contraseña) --}}
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 cursor-pointer">
                        <img id="password-toggle-icon" src="{{ asset('images/ojo-close.svg') }}"
                            alt="Mostrar/Ocultar contraseña" class="w-5 h-5 opacity-50">
                    </span>
                </div>
                {{-- Div para mostrar errores de validación JS --}}
                <div id="password-error-message" class="text-red-500 text-sm block mt-1" style="display:none;"></div>

                {{-- Errores de Laravel para la contraseña (ocultos por JS si el JS ya los maneja) --}}
                @error('password')
                    <span id="laravel-password-error" class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                @enderror
            </div>

            {{-- Recuerdame y olvido --}}
            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center text-sm font-bold text-gray-600">
                    <input type="checkbox" name="remember"
                        class="mr-2 h-5 w-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    Recuérdame en este dispositivo
                </label>
                <a href="{{ route('password.request') }}" class="text-sm font-bold text-purple-600 hover:underline">
                    ¿Olvidaste tu contrasena?
                </a>
            </div>

            {{-- Boton de ingreso --}}
            <button type="submit"
                class="w-full px-4 py-2 font-semibold text-white transition duration-150 bg-green-600 rounded-full hover:bg-green-700">
                Iniciar sesión
            </button>
        </form>
    </div>

    <div class="absolute transform -translate-x-1/2 bottom-44 left-1/2">
        <img src="{{ asset('images/sena-logo.svg') }}" alt="Logo SENA" class="w-auto h-20 opacity-90">
    </div>

    {{-- Modal usuario inactivo --}}
    @if (session('inactivo'))
        <div id="inactivoModal" x-data="{ show: true }" x-show="show"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
            <div class="max-w-md p-6 text-center bg-white shadow-md rounded-3xl">
                {{-- Icono --}}
                <img src="{{ asset('images/warning.svg') }}" alt="Icono de advertencia" class="w-56 mx-auto mb-4 h-36">
                <h2 class="mb-4 text-2xl font-bold text-red-600">Cuenta desactivada</h2>
                <p class="text-sm text-gray-700">
                    Si crees que esto es un error, contácta a
                    <a href="{{ route('pqrs.create') }}" class="text-blue-600 underline">
                        nuestro soporte de PQRs
                    </a>
                </p>

                <button @click="show = false"
                    class="mt-6 px-4 py-2 bg-[var(--color-iconos4)] text-white rounded hover:bg-green-600">
                    Cerrar
                </button>
            </div>
        </div>
    @endif
@endsection
