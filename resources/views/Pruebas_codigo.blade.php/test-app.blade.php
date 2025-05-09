<body x-data="{ open: false, sidebarOpen: true }" class="flex flex-col h-screen overflow-hidden font-sans antialiased">

    <!-- Cinta GOV.CO -->
    <div class="p-1 bg-blue-500">
        <img src="https://zajuna.sena.edu.co/img/logos/gov-logo.svg" alt="Logo GOV.CO" width="100px">
    </div>

    <x-banner />

    <div class="flex flex-1 overflow-hidden">

        <!-- Sidebar dinámico según el rol -->
        @role('administrador')
            <x-sidebar-admin />
        @elserole('operador')
            <x-sidebar-operador />
        @endrole

        <!-- Contenido principal -->
        <div class="flex flex-col flex-1 overflow-hidden bg-gray-2">
            @if (isset($header))
            <header class="px-4 py-6 bg-white shadow">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endif

            <main class="flex-1 h-full p-6 overflow-y-auto" :class="sidebarOpen ? 'pl-64' : 'pl-16'">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Overlay para móviles -->
    <div x-show="open" @click="open = false" class="fixed inset-0 z-40 bg-black bg-opacity-30 sm:hidden"
        x-transition.opacity></div>

    @stack('modals')
    @livewireScripts
    @yield('scripts')

    <script type="module" src="{{ asset('js/accesibilidad.js') }}"></script>
</body>
