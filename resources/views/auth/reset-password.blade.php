@extends('layouts.guest')

@section('content')
    <div class="absolute inset-0 z-0">
        <div class="absolute transform -translate-x-1/2 top-60 left-1/2">
            <img src="{{ asset('images/cultivasena.svg') }}" alt="Logo Cultiva" class="w-auto h-24 sm:h-24 opacity-90">
        </div>
    </div>

    <div class="relative z-20 flex flex-col items-center justify-center min-h-screen p-4">

        @if ($errors->any())
            <div class="mb-4 text-sm text-red-600">
                <div class="font-medium text-red-600">
                    {{ __('¡Ups! Hubo algunos problemas con tu envío.') }}
                </div>

                <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="w-full max-w-md mt-16 login-form sm:mt-24">
            @csrf

            {{-- El token se obtiene directamente de la URL a través de $request->route('token') --}}
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="block">
                <label for="email" class="block font-medium text-sm text-gray-700">
                    {{ __('Email') }}
                </label>
                <input id="email"
                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus
                    autocomplete="username" />
            </div>

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
                        <img :src="showConfirmPassword ? '{{ asset('images/ojo-open.svg') }}' :
                            '{{ asset('images/ojo-close.svg') }}'"
                            {{-- Y aquí --}} alt="Mostrar/Ocultar contraseña" class="w-5 h-5 opacity-50">
                    </span>
                </div>

                <div class="flex items-center justify-end mt-4">
                    {{-- Reemplaza <x-button> con <button> y sus clases --}}
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Restablecer Contraseña') }}
                    </button>
                </div>
        </form>
    </div>

    {{-- Logo SENA en la parte inferior --}}
    <div class="absolute transform -translate-x-1/2 bottom-44 left-1/2">
        <img src="{{ asset('images/sena-logo.svg') }}" alt="Logo SENA" class="w-auto h-20 opacity-90">
    </div>

@endsection
