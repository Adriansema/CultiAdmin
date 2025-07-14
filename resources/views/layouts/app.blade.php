<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Cultiva Sena') }}</title>
    <link rel="icon" href="{{ asset('images/Favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="flex flex-col h-screen font-sans antialiased"
      x-data="{ sidebarOpen: window.innerWidth >= 768 }" {{-- sidebarOpen: true para desktop por defecto --}}
      x-init="() => {
          // Si es un dispositivo móvil al cargar, el sidebar debe estar cerrado por defecto
          if (window.innerWidth < 768) {
              sidebarOpen = false;
          }

          $watch('sidebarOpen', value => {
              if (window.innerWidth < 768) { // Solo en móviles
                  if (value) {
                      document.body.classList.add('overflow-hidden-mobile');
                  } else {
                      document.body.classList.remove('overflow-hidden-mobile');
                  }
              } else { // En desktop, asegurar que no haya overflow-hidden
                  document.body.classList.remove('overflow-hidden-mobile');
              }
          });

          // Ajusta el sidebar al cambiar el tamaño de la ventana
          window.addEventListener('resize', () => {
              if (window.innerWidth >= 768) {
                  sidebarOpen = true; // En desktop, el sidebar siempre está abierto
                  document.body.classList.remove('overflow-hidden-mobile');
              } else {
                  sidebarOpen = false; // En móvil, cerrar al redimensionar
              }
          });
      }">

    {{-- Barra superior de GOV.CO --}}
    <div class="flex items-center justify-between p-1 pr-4 bg-blue-500">
        <img src="https://zajuna.sena.edu.co/img/logos/gov-logo.svg" alt="Logo GOV.CO" width="100px">
    </div>

    {{-- Header/Navbar superior para móviles (con botón de hamburguesa) --}}
    <header class="flex items-center justify-between p-4 bg-white shadow md:hidden">
        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        
    </header>

    <x-banner />

    {{-- Contenedor principal de layout: Sidebar + Contenido --}}
    <div class="flex flex-1 overflow-hidden"> {{-- Flex para Sidebar y Main Content --}}

        {{-- Componente Sidebar --}}
        {{-- Aquí el sidebar se comportará como un flex item en desktop y fixed en mobile --}}
        <x-sidebar />

        {{-- Contenido Principal --}}
        <div class="flex flex-col flex-1 w-full overflow-hidden bg-white" {{-- Añadimos bg-white aquí --}}
             :class="{
                'md:ml-0': sidebarOpen && window.innerWidth >= 768,       {{-- No hay ml porque el sidebar es parte del flex flow en desktop --}}
                'md:ml-0': !sidebarOpen && window.innerWidth >= 768,      {{-- No hay ml porque el sidebar colapsado también es parte del flow --}}
                'ml-0': window.innerWidth < 768,                          {{-- En móvil, sin ml, el sidebar se superpone --}}
             }">
             {{-- Los márgenes automáticos serán manejados por el ancho del sidebar mismo --}}

            @if (isset($header))
                <header class="px-4 py-6 bg-white shadow">
                    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif
            <main class="flex-1 h-full overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('modals')
    @yield('scripts')
    @livewireScripts

    @include('partials.global-message-modal')

    @if (session('success_message'))
        <div id="flash-success-message-data" data-message="{{ session('success_message') }}" class="hidden"></div>
    @endif

    @if (session('error_message'))
        <div id="flash-error-message-data" data-message="{{ session('error_message') }}" class="hidden"></div>
    @endif

    <script type="module" src="{{ asset('js/accesibilidad.js') }}"></script>
</body>

</html>
