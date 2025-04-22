@extends('layouts.login-layout')

@section('content')
    {{-- Fondo de logos superpuestos --}}
    <div class="absolute inset-0 z-0">
        {{-- Logo Cultiva centrado arriba --}}
        <div class="absolute transform -translate-x-1/2 top-8 left-1/2">
            <img src="{{ asset('images/cultiva-logo.png') }}" alt="Logo Cultiva" class="w-1/2 max-w-lg opacity-90">
        </div>

        {{-- Logo SENA centrado abajo --}}
        <div class="absolute transform -translate-x-1/2 bottom-1 left-1/2">
            <img src="{{ asset('images/sena-logo.svg') }}" alt="Logo SENA" class="w-9 opacity-90">
        </div>
    </div>

    {{-- Formulario de inicio de sesión --}}
    <form method="POST" action="{{ route('login') }}"
          class="relative z-10 max-w-md px-8 py-10 mx-auto bg-white shadow-xl bg-opacity-90 rounded-xl backdrop-blur-sm">
        @csrf

        {{-- Correo electrónico --}}
        <div class="mb-6">
            <label for="email" class="block mb-1 text-sm font-medium text-gray-700">Correo electrónico</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                    <i class="fas fa-user"></i>
                </span>
                <input id="email" type="email" name="email" required autofocus
                       class="w-full px-3 py-2 pl-10 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" />
            </div>
        </div>

        {{-- Contraseña --}}
        <div class="mb-6">
            <label for="password" class="block mb-1 text-sm font-medium text-gray-700">Contraseña</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                    <i class="fas fa-lock"></i>
                </span>
                <input id="password" type="password" name="password" required
                       class="w-full px-3 py-2 pl-10 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" />
            </div>
        </div>

        {{-- Recuérdame y olvido --}}
        <div class="flex items-center justify-between mb-6">
            <label class="flex items-center text-sm text-gray-600">
                <input type="checkbox" name="remember" class="mr-2">
                Recuérdame en este dispositivo
            </label>
            <a href="{{ route('password.request') }}" class="text-sm text-purple-600 hover:underline">
                ¿Olvidaste tu contraseña?
            </a>
        </div>

        {{-- Botón de ingreso --}}
        <button type="submit"
                class="w-full px-4 py-2 font-semibold text-white transition duration-150 bg-green-600 rounded-lg hover:bg-green-700">
            Iniciar Sesión
        </button>
    </form>

    <div class="mt-8 text-sm text-center text-gray-600">
        © {{ date('Y') }} Cultiva SENA. Todos los derechos reservados.
    </div>
@endsection
