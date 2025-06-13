<div x-data="{ open: false, sidebarOpen: true }" class="relative flex min-h-screen">
    {{-- Sidebar --}}
    <div :class="sidebarOpen ? 'w-64' : 'w-24'"
        class="h-auto flex flex-col transition-all duration-1000 bg-[#00304D] text-white flex-shrink-0 overflow-y-auto">

        <div class="flex items-center justify-between px-4 py-3">
            {{-- Logo + botón de colapsar --}}
            <div class="flex items-center shrink-0" :class="!sidebarOpen && 'justify-center w-full'">
                <a href="{{ route('dashboard') }}">
                    <x-application-mark class="block w-auto h-9" />
                </a>
            </div>
            <button @click="sidebarOpen = !sidebarOpen"
                class="-ml-2 text-[var(--color-text)] rounded hover:bg-[var(--color-sidebarhoverbtn)] transition-transform duration-700 ease-in-out hover:translate-x-1"
                :class="!sidebarOpen && 'rotate-180 mx-auto'">
                <svg class="w-4 h-5 transition-transform" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 px-6 pt-4 space-y-2">
            {{-- Inicio --}}
            {{-- Cultivos --}}
            {{-- Boletines --}}
            <div class="px-2 space-y-2">
                {{-- Inicio --}}
                <div
                    :class="sidebarOpen
                        ?
                        'flex pl-2 py-2 ml-[20px] transition rounded-xl  hover:bg-[var(--color-sidebarhoverbtn)]' :
                        'flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]'">
                    <x-responsive-nav-link href="{{ route('dashboard') }}"
                        class="flex items-center transition-all duration-300 ease-in-out" :active="request()->routeIs('dashboard')">

                        <img src="{{ asset('images/casa.svg') }}" class="w-4 h-4" alt="Inicio">

                        <span x-show="sidebarOpen" x-transition
                            class="ml-2 text-sm font-medium text-[var(--color-text)] whitespace-nowrap">
                            {{ __('Inicio') }}
                        </span>
                    </x-responsive-nav-link>
                </div>

                {{-- Cultivos --}}
                {{-- <div
                    :class="sidebarOpen
                        ?
                        'flex pl-2 py-2 ml-[20px] transition rounded-xl  hover:bg-[var(--color-sidebarhoverbtn)]' :
                        'flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]'">

                    <x-responsive-nav-link href="{{ route('productos.index') }}"
                        class="flex items-center transition-all duration-300 ease-in-out" :active="request()->routeIs('cultivos')">

                        <img src="{{ asset('images/plant.svg') }}" class="w-4 h-4" alt="Cultivos">

                        <span x-show="sidebarOpen" x-transition
                            class="ml-2 text-sm font-medium text-[var(--color-text)] whitespace-nowrap">
                            {{ __('Cultivos') }}
                        </span>
                    </x-responsive-nav-link>
                </div> --}}

                <div class="relative">
                    <div :class="sidebarOpen
                        ?
                        'flex pl-2 py-2 ml-[20px] transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer ' :
                        'flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer '"
                        @click.prevent="$refs.submenuCultivos.classList.toggle('hidden')">

                        <img src="{{ asset('images/plant.svg') }}" class="w-4 h-4" alt="cultivos">

                        <span x-show="sidebarOpen" x-transition
                            class="ml-2 text-sm font-medium text-[var(--color-text)] whitespace-nowrap">
                            {{ __('Cultivos') }}
                        </span>
                    </div>

                    {{-- Este es el contenedor del submenú que se va a desplegar debajo --}}
                    <div id="submenu-cultivos" x-ref="submenuCultivos"
                        class="hidden space-y-0 transition-all duration-200 ease-in-out space-y-0 {{-- border-b-2 rounded-b-xl border-x-2 --}}"
                        :class="sidebarOpen ? 'w-full pl-2' : 'w-9 -mx-1 flex flex-col items-center'"
                        style="border-color: background-color: var(--color-sidebarhoverbtn);">

                        {{-- Contenido del submenú --}}
                        <div :class="sidebarOpen ? 'w-full' : 'flex justify-center'">
                            <x-responsive-nav-link href="{{ route('productos.index') }}"
                                class="flex items-center px-2 py-2 ml-[20px] text-sm border-2 border-transparent rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] transition-all duration-1000">
                                <img src="{{ asset('images/flechita.svg') }}" alt="Cultivos"
                                    :class="sidebarOpen ? 'w-4 h-4 ml-[20px]' : 'w-4 h-4'" />
                                <span x-show="sidebarOpen"
                                    class="text-xs font-medium text-[var(--color-text)] whitespace-nowrap ml-[16px]">{{ __('Lista de Productos') }}</span>
                            </x-responsive-nav-link>
                        </div>

                        <hr class="border-gray-600 my-0 w-1/2 mx-auto">

                        <div :class="sidebarOpen ? 'w-full' : 'flex justify-center'"> {{-- Opción: Cultivos --}}
                            <x-responsive-nav-link href="{{ route('noticias.noticias.index') }}"
                                class="flex items-center px-2 py-2 ml-[20px] text-sm border-2 border-transparent rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] transition-all duration-1000">
                                <img src="{{ asset('images/flechita.svg') }}" alt="Cultivos"
                                    :class="sidebarOpen ? 'w-4 h-4 ml-[20px]' : 'w-4 h-4'" />
                                <span x-show="sidebarOpen"
                                    class="text-xs font-medium text-[var(--color-text)] whitespace-nowrap ml-[16px]">{{ __('Noticias') }}</span>
                            </x-responsive-nav-link>
                        </div>
                    </div>
                </div>

                {{-- Boletines --}}
                <div
                    :class="sidebarOpen
                        ?
                        'flex pl-2 py-2 ml-[20px] transition rounded-xl  hover:bg-[var(--color-sidebarhoverbtn)]' :
                        'flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]'">

                    <x-responsive-nav-link href="{{ route('boletines.index') }}"
                        class="flex items-center transition-all duration-300 ease-in-out" :active="request()->routeIs('boletines')">

                        <img src="{{ asset('images/form.svg') }}" class="w-4 h-4" alt="Boletines">

                        <span x-show="sidebarOpen" x-transition
                            class="ml-2 text-sm font-medium text-[var(--color-text)] whitespace-nowrap">
                            {{ __('Boletines') }}
                        </span>
                    </x-responsive-nav-link>
                </div>
            </div>
        </nav>

        <nav class="flex-1 px-6 pt-4 space-y-2">
            {{-- Gestión de Usuarios --}}
            {{-- Accesibilidad --}}
            {{-- Centro de Ayuda --}}
            {{-- Cerrar Sesión --}}
            <div class="px-2 space-y-2">
                <div x-show="sidebarOpen" x-transition class="px-6 py-2 text-xs text-[var(--color-ajustes)]">
                    {{ __('Ajustes') }}
                </div>

                {{-- Gestión de Usuarios --}}
                <div class="relative">
                    <div :class="sidebarOpen
                        ?
                        'flex pl-2 py-2 ml-[20px] transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer ' :
                        'flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer '"
                        @click.prevent="$refs.submenuGestionUsuarios.classList.toggle('hidden')">

                        <img src="{{ asset('images/Icon.svg') }}" class="w-4 h-4" alt="Usuarios">

                        <span x-show="sidebarOpen" x-transition
                            class="ml-2 text-sm font-medium text-[var(--color-text)] whitespace-nowrap">
                            {{ __('Gestión de Usuarios') }}
                        </span>
                    </div>

                    {{-- Este es el contenedor del submenú que se va a desplegar debajo --}}
                    <div id="submenu-gestion-usuarios" x-ref="submenuGestionUsuarios"
                        class="hidden space-y-0 transition-all duration-200 ease-in-out space-y-0 {{-- border-b-2 rounded-b-xl border-x-2 --}}"
                        :class="sidebarOpen ? 'w-full pl-2' : 'w-9 -mx-1 flex flex-col items-center'"
                        style="border-color: background-color: var(--color-sidebarhoverbtn);">

                        {{-- Contenido del submenú --}}
                        <div :class="sidebarOpen ? 'w-full' : 'flex justify-center'">
                            <x-responsive-nav-link href="{{ route('usuarios.index') }}"
                                class="flex items-center px-2 py-2 ml-[20px] text-sm border-2 border-transparent rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] transition-all duration-1000">
                                <img src="{{ asset('images/flechita.svg') }}" alt="Cultivos"
                                    :class="sidebarOpen ? 'w-4 h-4 ml-[20px]' : 'w-4 h-4'" />
                                <span x-show="sidebarOpen"
                                    class="text-xs font-medium text-[var(--color-text)] whitespace-nowrap ml-[16px]">{{ __('Lista de Usuarios') }}</span>
                            </x-responsive-nav-link>
                        </div>

                        <hr class="border-gray-600 my-0 w-1/2 mx-auto">

                        <div :class="sidebarOpen ? 'w-full' : 'flex justify-center'"> {{-- Opción: Cultivos --}}
                            <x-responsive-nav-link href="{{ route('usuarios.create') }}"
                                class="flex items-center px-2 py-2 ml-[20px] text-sm border-2 border-transparent rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] transition-all duration-1000">
                                <img src="{{ asset('images/flechita.svg') }}" alt="Cultivos"
                                    :class="sidebarOpen ? 'w-4 h-4 ml-[20px]' : 'w-4 h-4'" />
                                <span x-show="sidebarOpen"
                                    class="text-xs font-medium text-[var(--color-text)] whitespace-nowrap ml-[16px]">{{ __('Crear Usuarios') }}</span>
                            </x-responsive-nav-link>
                        </div>

                        {{-- <div :class="sidebarOpen ? 'w-full' : 'flex justify-center'"> 
                            <x-responsive-nav-link href="{{ route('usuarios.index')}}"
                                class="flex items-center px-2 py-2 ml-[20px] text-sm border-2 border-transparent rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] transition-all duration-1000">
                                <img src="{{ asset('images/flechita.svg') }}" alt="Boletines"
                                    :class="sidebarOpen ? 'w-4 h-4 ml-[20px]' : 'w-4 h-4'" />
                                <span x-show="sidebarOpen"
                                    class="text-xs font-medium text-[var(--color-text)] whitespace-nowrap ml-[16px]">{{ __('Roles y Permisos') }}</span>
                            </x-responsive-nav-link>
                        </div> --}}
                    </div>
                </div>

                {{-- Accesibilidad --}}
                <div
                    :class="sidebarOpen
                        ?
                        'flex pl-2 py-2 ml-[20px] transition rounded-xl  hover:bg-[var(--color-sidebarhoverbtn)]' :
                        'flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]'">

                    <x-responsive-nav-link href="{{ route('accesibilidad.index') }}"
                        class="flex items-center transition-all duration-300 ease-in-out" :active="request()->routeIs('accesibilidad')">

                        <img src="{{ asset('images/accesi.svg') }}" class="w-4 h-4" alt="Usuarios">

                        <span x-show="sidebarOpen" x-transition
                            class="ml-2 text-sm font-medium text-[var(--color-text)] whitespace-nowrap">
                            {{ __('Accesibilidad') }}
                        </span>
                    </x-responsive-nav-link>
                </div>

                {{-- Centro de Ayuda --}}
                <div
                    :class="sidebarOpen
                        ?
                        'flex pl-2 py-2 ml-[20px] transition rounded-xl  hover:bg-[var(--color-sidebarhoverbtn)]' :
                        'flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]'">

                    <x-responsive-nav-link href="{{ route('centroAyuda.index') }}"
                        class="flex items-center transition-all duration-300 ease-in-out" :active="request()->routeIs('centroAyuda')">

                        <img src="{{ asset('images/preg.svg') }}" class="w-4 h-4" alt="Usuarios">

                        <span x-show="sidebarOpen" x-transition
                            class="ml-2 text-sm font-medium text-[var(--color-text)] whitespace-nowrap">
                            {{ __('Centro de Ayuda') }}
                        </span>
                    </x-responsive-nav-link>
                </div>

                {{-- Cerrar Sesión --}}
                <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                    @csrf
                    <div
                        :class="sidebarOpen
                            ?
                            'flex items-center pl-2 py-2 ml-[20px] transition rounded-xl  hover:bg-[var(--color-sidebarhoverbtn)]' :
                            'flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]'">

                        <x-responsive-nav-link href="{{ route('logout') }}"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="flex items-center">

                            <img src="{{ asset('images/off.svg') }}" class="w-4 h-4" alt="Cerrar Sesión">

                            <span x-show="sidebarOpen" x-transition
                                class="ml-2 text-sm font-medium text-[var(--color-text)] whitespace-nowrap">
                                {{ __('Cerrar Sesión') }}
                            </span>
                        </x-responsive-nav-link>
                    </div>
                </form>
            </div>
        </nav>

        <div class="px-6 py-16">
            {{-- Perfil --}}
            <x-responsive-nav-link href="{{ route('profile.show') }}" class="rounded-3xl"
                x-bind:class="sidebarOpen ? 'px-3 py-6' : 'flex justify-center p-0'">

                <div class="flex items-center w-full rounded-lg"
                    x-bind:class="sidebarOpen
                        ?
                        'bg-[var(--color-profile)]  hover:bg-[var(--color-sidebarhoverbtn)] px-3 py-2' :
                        'justify-center px-0'">

                    <!-- Imagen de perfil -->
                    <img class="object-cover rounded-md size-10" src="{{ Auth::user()->profile_photo_url }}"
                        alt="{{ Auth::user()->name }}" />

                    <!-- Nombre visible solo cuando el sidebar está abierto -->
                    <div class="flex flex-col ml-3" x-show="sidebarOpen">
                        <span class="text-base font-bold text-gray-800">
                            {{ Auth::user()->name }}
                        </span>
                        <span class="text-sm text-gray-600">
                            {{ Auth::user()->getRoleNames()->first() ?? 'Usuario' }}
                        </span>
                    </div>
                </div>
            </x-responsive-nav-link>
        </div>
    </div>

    {{-- Contenido principal --}}
    <div :class="sidebarOpen ? 'pl-1' : 'pl-1'"
        class="flex-1 w-full h-screen overflow-y-auto transition-all duration-300">
        <main class="p-4">
            @yield('content')
        </main>
        <!-- Responsive Navigation Menu -->
        <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
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
                            <img class="object-cover rounded-full size-10"
                                src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                        </div>
                    @endif

                    <div>
                        <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <!-- Account Management -->
                    <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                        <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
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
                            <x-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')">
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
