{{-- ESTRUCTURA N°1 (ERA Actual Y YA NO ESTA EN USO[08/05/2025][13:39 PM])--}}
<div x-data="{ open: false, sidebarOpen: true }" class="relative flex min-h-screen">

    {{-- ESTRUCTURA N°1 ( ERA ACTUAL, Y YA NO ESTA EN USO[08/05/2025][13:39 PM])
    El problema:

    Estás usando: <nav class="flex-1 px-6 pt-4 space-y-2 mt-60">

        Pero flex-1 dentro de un sidebar flex-col distribuye el
        espacio vertical disponible entre los elementos que lo tienen.
        Si ambos <nav> tienen flex-1, se reparten el alto... y no puedes
            "empujar" al segundo más abajo solo con mt-60.

            La solución
            segundo <nav> quede pegado al fondo del sidebar, lo ideal es
                convertir el contenedor principal del sidebar en un flex
                flex-col justify-between, así: El EJEMPLO SE VERÁ EN LA
                {{-- ESTRUCTURA N°2 ( En Proceso de Prueba ) --}}

                <!-- Sidebar -->
                <div :class="sidebarOpen ? 'w-64' : 'w-24'"
                    class="h-auto flex flex-col transition-all duration-300 bg-[#00304D] text-white flex-shrink-0 overflow-y-auto">

                    <div class="flex items-center justify-between px-4 py-4">
                        <!-- Logo + botón de colapsar -->...
                    </div>

                    @role('administrador')
                    <div class="px-8 space-y-0.5 ">
                        {{-- Botón principal --}} ...
                    </div>

                    <nav class="flex-1 px-2 pt-4 space-y-2 ">
                        {{-- Inicio --}} {{-- Cultivos --}} {{-- Boletines --}}...
                    </nav>

                    <nav class="flex-1 px-6 pt-4 space-y-2 mt-60">
                        {{-- Gestión de Usuarios --}}{{-- Accesibilidad --}}
                        {{-- Centro de Ayuda --}}{{-- Cerrar Sesión --}}
                    </nav>

                    <div class="px-6 py-16">
                        {{-- Perfil --}}...
                    </div>
                </div>
                @endrole

                <div x-show="open" @click="open = false" class="fixed inset-0 z-40 bg-black bg-opacity-30 sm:hidden"
                    x-transition.opacity>
                    <!-- Overlay para móviles -->
                </div>

</div>

{{-- ESTRUCTURA N°2 --}}
<div x-data="{ open: false, sidebarOpen: true }" class="relative flex min-h-screen">

    {{-- ESTRUCTURA N°2 ( En Proceso de Prueba )
    ¿Qué logras con esto?
    El sidebar es un flex-col justify-between, lo que
    distribuye el espacio entre el bloque superior y el inferior.

    El segundo <nav> y el perfil quedan abajo sin
        necesidad de márgenes artificiales (mt-60 desaparece).

        La estructura es más limpia, clara y escalable.--}}


        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'w-64' : 'w-24'"
            class="h-auto flex flex-col justify-between transition-all duration-300 bg-[#00304D] text-white flex-shrink-0 overflow-y-auto">

            <!-- Top (Logo + Botón + Primer nav) -->
            <div>
                <div class="flex items-center justify-between px-4 py-4">
                    <!-- Logo + botón de colapsar -->
                </div>

                @role('administrador')
                <div class="px-8 space-y-0.5">
                    {{-- Botón principal --}}
                </div>

                <nav class="px-2 pt-4 space-y-2">
                    {{-- Inicio --}} {{-- Cultivos --}} {{-- Boletines --}}
                </nav>
            </div>

            <!-- Bottom (Segundo nav + Perfil) -->
            <div>
                <nav class="px-6 pt-4 space-y-2">
                    {{-- Gestión de Usuarios --}} {{-- Accesibilidad --}}
                    {{-- Centro de Ayuda --}} {{-- Cerrar Sesión --}}
                </nav>

                <div class="px-6 py-16">
                    {{-- Perfil --}}
                </div>
            </div>
        </div>

        <div x-show="open" @click="open = false" class="fixed inset-0 z-40 bg-black bg-opacity-30 sm:hidden"
            x-transition.opacity>
            <!-- Overlay para móviles -->
        </div>
</div>

{{-- ESTRUCTURA N°3 --}}
<div x-data="{ open: false, sidebarOpen: true }" class="relative flex min-h-screen">

    {{-- ESTRUCTURA N°3( En Proceso de Prueba )
    Puntos importantes:
    El sidebar debe tener relative → esto da contexto al
    absolute del bloque inferior.

    El contenedor del bloque fijo tiene absolute bottom-0 w-full
    para quedar pegado al fondo y ocupar todo el ancho.

    Agrega pb-6 si quieres que tenga un pequeño “respiro”
    antes del borde.

    Resultado:
    El segundo <nav> + perfil quedan anclados abajo incluso
        si el contenido de arriba crece y el sidebar entra en scroll. Nunca se moverán.--}}

        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'w-64' : 'w-24'"
            class="relative h-auto flex flex-col transition-all duration-300 bg-[#00304D] text-white flex-shrink-0 overflow-y-auto">

            <!-- Top (Logo + Botón + Primer nav) -->
            <div class="px-4 py-4">
                <!-- Logo + botón -->
            </div>

            @role('administrador')
            <div class="px-8 space-y-0.5">
                {{-- Botón principal --}}
            </div>

            <nav class="px-2 pt-4 space-y-2">
                {{-- Inicio --}} {{-- Cultivos --}} {{-- Boletines --}}
            </nav>

            <!-- Bloque Fijo Abajo -->
            <div class="absolute bottom-0 w-full px-6 pb-6">
                <nav class="pt-4 space-y-2">
                    {{-- Gestión de Usuarios --}} {{-- Accesibilidad --}}
                    {{-- Centro de Ayuda --}} {{-- Cerrar Sesión --}}
                </nav>

                <div class="pt-6">
                    {{-- Perfil --}}
                </div>
            </div>

        </div>


        <div x-show="open" @click="open = false" class="fixed inset-0 z-40 bg-black bg-opacity-30 sm:hidden"
            x-transition.opacity>
            <!-- Overlay para móviles -->
        </div>
</div>

{{-- ESTRUCTURA N°4 (Actual, YA SE ESTA USANDO[08/05/2025][15:48 PM])--}}
<div x-data="{ open: false, sidebarOpen: true }" class="relative flex min-h-screen">
    {{-- Sidebar --}}
    <div :class="sidebarOpen ? 'w-64' : 'w-24'" {{-- Empieza div  --}}
        class="h-auto flex flex-col transition-all duration-1000 bg-[#00304D] text-white flex-shrink-0 overflow-y-auto">

        <div class="flex items-center justify-between px-4 py-3">
            {{-- Logo + botón de colapsar --}}...
        </div>

        @role('administrador')
        <div class="px-8 space-y-0.5 ">
            {{-- Botón principal --}} ...
        </div>

        <nav class="flex-1 px-6 pt-4 space-y-2">
            {{-- Inicio --}}
            {{-- Cultivos --}}
            {{-- Boletines --}}...
        </nav>

        <nav class="flex-1 px-6 pt-4 space-y-2">
            {{-- Gestión de Usuarios --}}
            {{-- Accesibilidad --}}
            {{-- Centro de Ayuda --}}
            {{-- Cerrar Sesión --}}
        </nav>

        <div class="px-6 py-16">
            {{-- Perfil --}}...
        </div>
        @endrole

        @role('operador')
        <nav class="flex-1 px-6 pt-4 space-y-2">
            {{-- Inicio --}}
            {{-- Validar Productos --}}
            {{-- Historial --}}...
        </nav>

        <nav class="flex-1 px-6 pt-4 space-y-2">
            {{-- Accesibilidad --}}
            {{-- Centro de Ayuda --}}
            {{-- Cerrar Sesión --}}
        </nav>

        <div class="px-6 py-16">
            {{-- Perfil --}}...
        </div>
    </div> {{-- termina div --}}
        @endrole

    {{-- Overlay para móviles --}}
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
