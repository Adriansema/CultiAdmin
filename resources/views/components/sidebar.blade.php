<div x-data="{ open: false, sidebarOpen: true }" class="relative flex min-h-screen">
    <!-- Sidebar -->
    <div :class="sidebarOpen ? 'w-64' : 'w-24'"
        class="h-screen flex flex-col transition-all duration-300 bg-[#00304D] text-white flex-shrink-0 overflow-y-auto">

        <!-- Logo + botón de colapsar -->
        <div class="flex items-center justify-between px-4 py-4">
            <div class="flex items-center shrink-0" :class="!sidebarOpen && 'justify-center w-full'">
                <a href="{{ route('dashboard') }}">
                    <x-application-mark class="block w-auto h-9" />
                </a>
            </div>
            <button @click="sidebarOpen = !sidebarOpen"
                class="p-0 ml-0 text-white rounded hover:bg-[var(--color-sidebarhoverbtn)]"
                :class="!sidebarOpen && 'rotate-180 mx-auto'">
                <svg class="w-4 h-5 transition-transform" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
        </div>

        @role('administrador')
        <!-- Enlaces del sidebar -->
        <nav class="flex-1 px-2 pt-4 space-y-2 ">
            <div class="space-y-0">

                {{-- Botón principal --}}
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

                {{-- Submenú --}}
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

            <div
                :class="sidebarOpen ? 'flex items-center px-6 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]' : 'flex justify-center px-5 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]'">
                <x-responsive-nav-link href="{{ route('dashboard') }}">
                    <img src="{{ asset('images/casa.svg') }}" class="w-5 h-5" alt="Inicio">
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium transition-all">
                        {{ __('Inicio') }}
                    </span>
                </x-responsive-nav-link>
            </div>

            <div class="space-y-1">

                {{-- Botón principal Cultivos --}}
                <div :class="sidebarOpen ? 'flex items-center px-6 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer' : 'flex justify-center px-2 py-2 transition rounded-lg hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'"
                    @click.prevent="$el.nextElementSibling.classList.toggle('hidden')">

                    <img src="{{ asset('images/plant.svg') }}" alt="Cultivos" class="w-5 h-5" />

                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium text-white transition-all">
                        {{ __('Cultivos') }}
                    </span>
                </div>
            </div>


            <div class="space-y-1">

                {{-- Botón principal Boletines --}}
                <div :class="sidebarOpen ? 'flex items-center px-6 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer' : 'flex justify-center px-2 py-2 transition rounded-lg hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'"
                    @click.prevent="$el.nextElementSibling.classList.toggle('hidden')">

                    <img src="{{ asset('images/files.svg') }}" alt="Boletines" class="w-5 h-5" />

                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium text-white transition-all">
                        {{ __('Boletines') }}
                    </span>
                </div>
            </div>


            <div class="flex-1 space-y-2">

                {{-- Gestión de Usuarios --}}
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

                {{-- Accesibilidad --}}
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

                {{-- Centro de Ayuda --}}
                <div
                    :class="sidebarOpen ? 'flex items-center px-6 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]' : 'flex justify-center px-2 py-2 transition rounded-lg hover:bg-[var(--color-sidebarhoverbtn)]'">
                    <x-responsive-nav-link href="{{ route('centroAyuda.index') }}">
                        <img src="{{ asset('images/preg.svg') }}" class="w-5 h-5" alt="Centro de Ayuda">
                        <span x-show="sidebarOpen" class="ml-3 text-sm font-medium text-white transition-all">
                            {{ __('Centro de Ayuda') }}
                        </span>
                    </x-responsive-nav-link>
                </div>

                {{-- Cerrar Sesión --}}
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
        </nav>

        <!-- Perfil -->
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
    <div
        :class="sidebarOpen ? 'flex items-center px-6 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]' : 'flex justify-center px-5 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]'">
        <x-responsive-nav-link href="{{ route('dashboard') }}">
            <img src="{{ asset('images/casa.svg') }}" class="w-5 h-5" alt="Inicio">
            <span x-show="sidebarOpen" class="ml-3 text-sm font-medium transition-all">
                {{ __('Inicio') }}
            </span>
        </x-responsive-nav-link>
    </div>

    <div class="flex-1 space-y-2">
        {{-- Cerrar Sesión --}}
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

    <!-- Perfil -->
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
    @endrole

    <!-- Overlay para móviles -->
    <div x-show="open" @click="open = false" class="fixed inset-0 z-40 bg-black bg-opacity-30 sm:hidden"
        x-transition.opacity></div>

    <!-- Contenido principal -->
    <div :class="sidebarOpen ? 'pl-1' : 'pl-1'"
        class="flex-1 w-full h-screen overflow-y-auto transition-all duration-300">
        <main class="p-4">
            @yield('content')
        </main>

        <!-- Responsive Navigation Menu -->
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="flex items-center px-4">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3">
                        <img class="object-cover rounded-full size-10" src="{{ Auth::user()->profile_photo_url }}"
                            alt="{{ Auth::user()->name }}" />
                    </div>
                    @endif

                    <div>
                        <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <!-- Account Management -->
                    <x-responsive-nav-link href="{{ route('profile.show') }}"
                        :active="request()->routeIs('profile.show')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}"
                        :active="request()->routeIs('api-tokens.index')">
                        {{ __('API Tokens') }}
                    </x-responsive-nav-link>
                    @endif

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf

                        <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>

                    <!-- Team Management -->
                    @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="border-t border-gray-200"></div>

                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Team') }}
                    </div>

                    <!-- Team Settings -->
                    <x-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}"
                        :active="request()->routeIs('teams.show')">
                        {{ __('Team Settings') }}
                    </x-responsive-nav-link>

                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                    <x-responsive-nav-link href="{{ route('teams.create') }}"
                        :active="request()->routeIs('teams.create')">
                        {{ __('Create New Team') }}
                    </x-responsive-nav-link>
                    @endcan

                    <!-- Team Switcher -->
                    @if (Auth::user()->allTeams()->count() > 1)
                    <div class="border-t border-gray-200"></div>

                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Switch Teams') }}
                    </div>

                    @foreach (Auth::user()->allTeams() as $team)
                    <x-switchable-team :team="$team" component="responsive-nav-link" />
                    @endforeach
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
