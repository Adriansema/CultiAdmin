@extends('layouts.guest')

@section('title', 'Acceso No Autorizado') {{-- Opcional: define el título de la página --}}

@section('content')
    <div class="flex items-center justify-center min-h-screen">

        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full text-center">

            <svg class="mx-auto h-32 w-24 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>

            <h1 class="mt-4 text-2xl font-bold text-gray-800">Acceso Pendiente de Configuración</h1>

            <p class="mt-2 text-gray-600">
                Has iniciado sesión correctamente, pero tu cuenta aún no tiene un rol asignado o está pendiente de
                configuración por un administrador.
                Por favor, contacta al soporte o a tu administrador para obtener más información.
            </p>

            <p class="mt-2 text-gray-700 sm:text-base">
                Si crees que esto es un error, contácta a
                <a href="{{ route('pqrs.create') }}" class="text-blue-600 underline">
                    nuestro soporte de PQRs
                </a>
            </p>

            <div class="mt-6">
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="inline-block bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    Cerrar Sesión
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>
@endsection
