@extends('layouts.guest')

@section('content')
    <div class="absolute inset-0 z-0">
        <div class="absolute transform -translate-x-1/2 top-60 left-1/2">
            <img src="{{ asset('images/cultivasena.svg') }}" alt="Logo Cultiva" class="w-auto h-24 sm:h-24 opacity-90">
        </div>
    </div>

    <form method="POST" action="{{ route('password.email') }}" class="mt-16 recuperar-form sm:mt-24 relative z-20">
        @csrf

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <div class="block">
            <label for="email" class="block font-medium text-sm text-gray-700">
                {{ __('Email') }}
            </label>
            <input id="email"
                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                type="email" name="email" placeholder="email" value="{{ old('email') }}" required autofocus
                autocomplete="username" />
        </div>

        <div class="flex items-center justify-end mt-4">

        </div>
        <div class="flex items-center justify-between">
            {{-- Botón para volver al inicio de sesión (primero para que aparezca a la izquierda) --}}
            <a href="{{ route('login') }}"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 text-xs font-semibold py-2 px-4 rounded-lg text-center
                             focus:outline-none focus:shadow-outline">
                Volver
            </a>

            <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Restablecer contraseña') }}
            </button>
        </div>
        @if ($errors->any())
            <div class="mb-4">
                <div class="font-medium text-red-600">
                    {{ __('¡Ups! Hubo algunos problemas con tu envío') }}
                </div>

                <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </form>


    <div class="absolute transform -translate-x-1/2 bottom-44 left-1/2">
        <img src="{{ asset('images/sena-logo.svg') }}" alt="Logo SENA" class="w-auto h-20 opacity-90">
    </div>
@endsection
