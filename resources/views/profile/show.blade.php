@extends('layouts.app')

@section('title', 'Perfil del usuario') {{-- Añadido el título para la página de perfil --}}

@section('content')
    {{-- Contenedor principal de la página de perfil --}}
    <div class="w-full px-4 py-6 md:px-8 lg:px-12"> {{-- Ajuste de padding para pantallas pequeñas y grandes --}}
        <div class="flex items-center mb-6 space-x-4"> {{-- Margen inferior para separar del contenido --}}
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono de retroceso"> {{-- Ajusta el alt si es necesario --}}
            <h1 class="text-xl font-bold sm:text-2xl lg:text-3xl whitespace-nowrap">Perfil del usuario</h1> {{-- Título responsivo --}}
        </div>

        <div class="py-4 mx-auto max-w-7xl sm:px-6 lg:px-8"> {{-- Contenedor principal del contenido del perfil, con padding --}}
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                <div class="mb-10"> {{-- Margen inferior para separar secciones --}}
                    @livewire('profile.update-profile-information-form')
                </div>
                <x-section-border />
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="mt-10 mb-10 sm:mt-0"> {{-- Margen superior e inferior para separar secciones --}}
                    @livewire('profile.update-password-form')
                </div>
                <x-section-border />
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="mt-10 mb-10 sm:mt-0"> {{-- Margen superior e inferior para separar secciones --}}
                    @livewire('profile.two-factor-authentication-form')
                </div>
                <x-section-border />
            @endif

            {{-- Si tienes esta sección, puedes descomentarla y aplicarle clases si es necesario --}}
            {{-- <div class="mt-10 mb-10 sm:mt-0">
                @livewire('profile.logout-other-browser-sessions-form')
            </div> --}}

            {{-- Si tienes una sección de eliminación de cuenta, también iría aquí --}}
            {{-- @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <x-section-border />
                <div class="mt-10 mb-10 sm:mt-0">
                    @livewire('profile.delete-user-form')
                </div>
            @endif --}}
        </div>
    </div>
@endsection
