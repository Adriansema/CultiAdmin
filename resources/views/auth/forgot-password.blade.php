@extends('layouts.guest')

@section('content')
    <div class="absolute inset-0 z-0">
        <div class="absolute transform -translate-x-1/2 top-60 left-1/2">
            <img src="{{ asset('images/cultivasena.svg') }}" alt="Logo Cultiva" class="w-auto h-24 sm:h-24 opacity-90">
        </div>
    </div>

    <div class="relative z-20 flex flex-col items-center justify-center min-h-screen p-4">
        <form method="POST" action="{{ route('password.email') }}" class="w-full max-w-md mt-16 recuperar-form sm:mt-24">
            @csrf

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-4 relative">
                <label for="email" class="block mb-2 text-md font-bold text-gray-700">
                    {{ __('Correo electrónico') }}
                </label>

                <div class="absolute inset-y-0 left-0 flex items-center pl-3" style="top: 2rem;">
                    <img src="{{ asset('images/email.svg') }}" alt="email" class="w-5 h-4 text-gray-500">
                </div>

                <input id="email"
                    class="w-full px-3 py-2 pl-10 pr-10 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    type="email" name="email" placeholder="ingrese su correo electrónico" value="{{ old('email') }}"
                    required autofocus autocomplete="username" />
            </div>

            <div class="flex items-center justify-between">
                {{-- Boton para volver al inicio de sesion (primero para que aparezca a la izquierda) --}}
                <a href="{{ route('login') }}"
                    class="bg-[var(--color-Gestion)] hover:bg-gray-300 text-gray-800 text-md font-bold py-2 px-4 rounded-full text-center
                             focus:outline-none focus:shadow-outline">
                    {{ __('Volver') }}
                </a>

                <button type="submit"
                    class="bg-[var(--color-textmarca)] hover:bg-[var(--color-texthovermarca)] text-white text-md font-bold py-2 px-4 rounded-full text-center
                    focus:outline-none focus:shadow-outline"">
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
    </div>

    <div class="absolute transform -translate-x-1/2 bottom-44 left-1/2">
        <img src="{{ asset('images/sena-logo.svg') }}" alt="Logo SENA" class="w-auto h-20 opacity-90">
    </div>
@endsection
