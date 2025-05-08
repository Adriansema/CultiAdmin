@role('administrador')
<nav class="flex flex-col flex-1 px-4 py-0.5 space-y-4 overflow-y-auto">
    <div class="py-1 px-7">
        <div class="flex-1 space-y-2">
            <div class="space-y-1">
                <!-- + Nueva Entrada -->
                <div :class="sidebarOpen
                        ? 'flex items-center px-4 py-2 transition bg-[#39A900] border-transparent rounded-full border-x-2 hover:bg-[#61BA33] cursor-pointer'
                        : 'flex items-center justify-center w-10 h-10 transition bg-[#39A900] rounded-full hover:bg-[#61BA33] cursor-pointer mx-auto'"
                    @click.prevent="$el.nextElementSibling.classList.toggle('hidden')">
                    <!-- Ícono -->
                    <img src="{{ asset('images/signo.svg') }}" alt="Nueva Entrada" class="w-3 h-4" />
                    <!-- Texto visible solo si el sidebar está abierto -->
                    <span x-show="sidebarOpen" class="font-medium text-white tex-sm ml-9">
                        {{ __('Nueva Entrada') }}
                    </span>
                </div>

                    <div id="submenu-nueva-entrada" class="hidden pl-0 mt-0 space-y-0 border-b-2 rounded-b-3xl border-x-2"
                        style="border-color: #39A900;">
                        <!-- Submenu con borde inferior y laterales -->
                        <div id="submenu-nueva-entrada"
                        class="hidden mt-8 space-y-0 transition-all duration-200 ease-in-out border-b-2 border-x-2 rounded-b-3xl"
                        :class="sidebarOpen ? 'w-full px-2' : 'w-10 mx-auto flex flex-col items-center'"
                        style="border-color: #39A900;">

                        {{-- Opción: Cultivos --}}
                        <div :class="sidebarOpen ? '' : 'flex justify-center'">
                            <x-responsive-nav-link href="{{ route('dashboard') }}"
                                class="flex items-center px-3 py-2 text-sm text-gray-400 border-2 border-transparent rounded-full hover:bg-[#39A900] hover:text-white transition-all duration-75">
                                <img src="{{ asset('images/hoja.svg') }}" alt="Cultivos" class="w-4 h-4 mr-2" />
                                <span x-show="sidebarOpen">{{ __('Wiki de Cultivos') }}</span>
                            </x-responsive-nav-link>
                        </div>

                        {{-- Opción: Boletines --}}
                        <div :class="sidebarOpen ? '' : 'flex justify-center'">
                            <x-responsive-nav-link href="{{ route('dashboard') }}"
                                class="flex items-center px-3 py-2 text-sm text-gray-400 border-2 border-transparent rounded-full hover:bg-[#39A900] hover:text-white transition-all duration-300">
                                <img src="{{ asset('images/form.svg') }}" alt="Boletines" class="w-4 h-4 mr-2" />
                                <span x-show="sidebarOpen">{{ __('Boletines') }}</span>
                            </x-responsive-nav-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-8 px-7">
        <div class="flex-1 space-y-3">
            <div class="space-y-3">
                <!-- Panel_Icono de casa -->
                <div
                :class="sidebarOpen ? 'flex items-center px-6 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]' : 'flex justify-center px-5 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]'">
                <x-responsive-nav-link href="{{ route('dashboard') }}">
                    <img src="{{ asset('images/casa.svg') }}" class="w-5 h-5" alt="Inicio">
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium transition-all">
                        {{ __('Inicio') }}
                    </span>
                </x-responsive-nav-link>
            </div>
            </div>

            <div class="space-y-1">
                <!-- Boletines y Cultivos -->
                 {{-- Botón principal Cultivos --}}
                 <div :class="sidebarOpen ? 'flex items-center px-6 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer' : 'flex justify-center px-2 py-2 transition rounded-lg hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'"
                 @click.prevent="$el.nextElementSibling.classList.toggle('hidden')">

                 <img src="{{ asset('images/plant.svg') }}" alt="Cultivos" class="w-5 h-5" />

                 <span x-show="sidebarOpen" class="ml-3 text-sm font-medium text-white transition-all">
                     {{ __('Cultivos') }}
                 </span>
             </div>

             {{-- Botón principal Boletines --}}
             <div :class="sidebarOpen ? 'flex items-center px-6 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer' : 'flex justify-center px-2 py-2 transition rounded-lg hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'"
             @click.prevent="$el.nextElementSibling.classList.toggle('hidden')">

             <img src="{{ asset('images/files.svg') }}" alt="Boletines" class="w-5 h-5" />

             <span x-show="sidebarOpen" class="ml-3 text-sm font-medium text-white transition-all">
                 {{ __('Boletines') }}
             </span>
         </div>
            </div>
        </div>
    </div>

    <div class="px-5 py-3">
        <div class="block px-4 py-2 text-xs text-gray-400">
            <!-- Ajuste/Configuracion -->
            {{ __('Ajustes') }}
        </div>

        <div class="flex-1 space-y-2.5">
            <!-- Botón de Gestion de Usuario -->
            <div
                    :class="sidebarOpen ? 'flex items-center px-6 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]' : 'flex justify-center px-2 py-2 transition rounded-lg hover:bg-[var(--color-sidebarhoverbtn)]'">
                    <x-responsive-nav-link href="{{ route('usuarios.index') }}"
                        :active="request()->routeIs('usuarios.*')">
                        <img src="{{ asset('images/add.svg') }}" class="w-5 h-5" alt="Usuarios">
                        <span x-show="sidebarOpen" class="ml-3 text-sm font-medium text-white transition-all">
                            {{ __('Gestión de Usuarios') }}
                        </span>
                    </x-responsive-nav-link>
                </div>
            <!-- Botón de Accesibilidad -->
            <div
            :class="sidebarOpen ? 'flex items-center px-6 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]' : 'flex justify-center px-2 py-2 transition rounded-lg hover:bg-[var(--color-sidebarhoverbtn)]'">
            <x-responsive-nav-link href="{{ route('accesibilidad.index') }}"
                :active="request()->routeIs('accesibilidad')">
                <img src="{{ asset('images/accesi.svg') }}" class="w-5 h-5" alt="Accesibilidad">
                <span x-show="sidebarOpen" class="ml-3 text-sm font-medium text-white transition-all">
                    {{ __('Accesibilidad') }}
                </span>
            </x-responsive-nav-link>
        </div>
            <!-- Boton de Centro de Ayuda -->
            <div
                    :class="sidebarOpen ? 'flex items-center px-6 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]' : 'flex justify-center px-2 py-2 transition rounded-lg hover:bg-[var(--color-sidebarhoverbtn)]'">
                    <x-responsive-nav-link href="{{ route('centroAyuda.index') }}">
                        <img src="{{ asset('images/preg.svg') }}" class="w-5 h-5" alt="Centro de Ayuda">
                        <span x-show="sidebarOpen" class="ml-3 text-sm font-medium text-white transition-all">
                            {{ __('Centro de Ayuda') }}
                        </span>
                    </x-responsive-nav-link>
                </div>
            <!-- Botón de cerrar sesión -->
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <div
                    :class="sidebarOpen ? 'flex items-center px-6 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]' : 'flex justify-center px-2 py-2 transition rounded-lg hover:bg-[var(--color-sidebarhoverbtn)]'">
                    <x-responsive-nav-link href="{{ route('logout') }}"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        <img src="{{ asset('images/off.svg') }}" class="w-5 h-5" alt="Cerrar Sesión">
                        <span x-show="sidebarOpen" class="ml-3 text-sm font-medium text-white transition-all">
                            {{ __('Cerrar Sesión') }}
                        </span>
                    </x-responsive-nav-link>
                </div>
            </form>
        </div>
    </div>
</nav>

<div class="py-2">
    {{-- Foto de perfil y nombre --}}
    <div class="px-2 py-12">
        <x-responsive-nav-link href="{{ route('profile.show') }}"
            class="transition-all duration-200 rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]"
            x-bind:class="sidebarOpen ? 'px-3 py-2' : 'flex justify-center p-2'">

            <div class="flex items-center w-full rounded-2xl"
                x-bind:class="sidebarOpen ? 'bg-white/50 px-3 py-2' : 'justify-center px-0'">

                <!-- Imagen de perfil -->
                <img class="object-cover rounded-full size-10" src="{{ Auth::user()->profile_photo_url }}"
                    alt="{{ Auth::user()->name }}" />

                <!-- Nombre visible solo cuando el sidebar está abierto -->
                <div class="flex flex-col ml-3" x-show="sidebarOpen">
                    <span class="text-base font-bold text-white">
                        {{ Auth::user()->name }}
                    </span>
                </div>
            </div>
        </x-responsive-nav-link>
    </div>
</div>
@endrole

@role('operador')
<nav class="flex flex-col flex-1 px-4 py-0.5 space-y-4 overflow-y-auto">
    <div class="px-4 py-3">
        {{-- Inicio --}}
        <div
        :class="sidebarOpen ? 'flex items-center px-6 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]' : 'flex justify-center px-5 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]'">
        <x-responsive-nav-link href="{{ route('dashboard') }}">
            <img src="{{ asset('images/casa.svg') }}" class="w-5 h-5" alt="Inicio">
            <span x-show="sidebarOpen" class="ml-3 text-sm font-medium transition-all">
                {{ __('Inicio') }}
            </span>
        </x-responsive-nav-link>
    </div>
        {{-- Validar Productos --}}
        {{-- Historial --}}
    </div>

    <div class="px-4 py-3">
        {{-- Cerrar Sesion --}}
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <div
                :class="sidebarOpen ? 'flex items-center px-6 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]' : 'flex justify-center px-2 py-2 transition rounded-lg hover:bg-[var(--color-sidebarhoverbtn)]'">
                <x-responsive-nav-link href="{{ route('logout') }}"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                    <img src="{{ asset('images/off.svg') }}" class="w-5 h-5" alt="Cerrar Sesión">
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium text-white transition-all">
                        {{ __('Cerrar Sesión') }}
                    </span>
                </x-responsive-nav-link>
            </div>
        </form>
    </div>

    <div class="px-4 py-3">
        {{-- Foto de perfil y nombre --}}
        <div class="px-2 py-12">
            <x-responsive-nav-link href="{{ route('profile.show') }}"
                class="transition-all duration-200 rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]"
                x-bind:class="sidebarOpen ? 'px-3 py-2' : 'flex justify-center p-2'">

                <div class="flex items-center w-full rounded-2xl"
                    x-bind:class="sidebarOpen ? 'bg-white/50 px-3 py-2' : 'justify-center px-0'">

                    <!-- Imagen de perfil -->
                    <img class="object-cover rounded-full size-10" src="{{ Auth::user()->profile_photo_url }}"
                        alt="{{ Auth::user()->name }}" />

                    <!-- Nombre visible solo cuando el sidebar está abierto -->
                    <div class="flex flex-col ml-3" x-show="sidebarOpen">
                        <span class="text-base font-bold text-white">
                            {{ Auth::user()->name }}
                        </span>
                    </div>
                </div>
            </x-responsive-nav-link>
        </div>
    </div>
</nav>
@endrole
