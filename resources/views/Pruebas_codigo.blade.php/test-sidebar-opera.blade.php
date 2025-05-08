<div x-data="{ open: false, sidebarOpen: true }" class="relative flex min-h-screen">
    {{-- Sidebar --}}
    <div :class="sidebarOpen ? 'w-64' : 'w-24'"
        class="h-auto flex flex-col transition-all duration-1000 bg-[#00304D] text-white flex-shrink-0 overflow-y-auto">

        <div class="flex items-center justify-between px-4 py-3">
            {{-- Logo + botón de colapsar --}}
        </div>

        <nav class="flex-1 px-6 pt-4 space-y-2">
            {{-- Inicio --}}
            {{-- Validar Productos --}}
            {{-- Historial --}}
        </nav>

        <nav class="flex-1 px-6 pt-4 space-y-2">
            {{-- Accesibilidad --}}
            {{-- Centro de Ayuda --}}
            {{-- Cerrar Sesión --}}
        </nav>

        <div class="px-6 py-16">
            {{-- Perfil --}}
        </div>
    </div>

    {{-- Overlay móvil --}}
    <div x-show="open" @click="open = false" class="fixed inset-0 z-40 bg-black bg-opacity-30 sm:hidden"
        x-transition.opacity></div>

    {{-- Contenido principal --}}
    <div :class="sidebarOpen ? 'pl-1' : 'pl-1'"
        class="flex-1 w-full h-screen overflow-y-auto transition-all duration-300">
        <main class="p-4">
            @yield('content')
        </main>
    </div>
</div>

<!--
manteniendo toda la lógica de colapsar el sidebar (x-data, sidebarOpen, etc.)
 dentro de cada uno, de modo que puedan operar de forma independiente y seguir
 funcionando igual de bien.
-->
