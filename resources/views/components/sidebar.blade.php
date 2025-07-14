<aside
    x-show="sidebarOpen || window.innerWidth >= 768"
    class="h-full flex flex-col transition-all duration-300 ease-in-out bg-[#00304D] text-white flex-shrink-0 overflow-y-auto overflow-x-hidden" {{-- Mantenemos overflow-x-hidden aquí --}}
    :class="{
        'md:w-72': sidebarOpen,
        'md:w-28': !sidebarOpen,
        'md:static md:translate-x-0': true,

        'fixed inset-y-0 left-0 z-40': window.innerWidth < 768,
        'translate-x-0': sidebarOpen && window.innerWidth < 768,
        '-translate-x-full': !sidebarOpen && window.innerWidth < 768,

        'w-72': sidebarOpen && window.innerWidth < 768
    }"
    @click.away="if(window.innerWidth < 768 && sidebarOpen) sidebarOpen = false"

    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
>
    {{-- TODO EL CONTENIDO INTERNO DEL SIDebar se mantiene igual --}}

    <div class="flex items-center justify-between px-4 py-3">
        {{-- Logo + boton de colapsar --}}
        <div class="flex items-center shrink-0" :class="!sidebarOpen && 'justify-center w-full'">
            <a href="{{ route('dashboard') }}">
                <x-application-mark class="block w-auto h-9" />
            </a>
        </div>
        {{-- Botón para colapsar en desktop --}}
        <button @click="sidebarOpen = !sidebarOpen"
                class="-ml-2 text-[var(--color-text)] rounded hover:bg-[var(--color-sidebarhoverbtn)] transition-transform duration-700 ease-in-out hover:translate-x-1
                       hidden md:block"
                :class="{
                    'rotate-180 mx-auto': !sidebarOpen
                }">
                <svg class="w-4 h-5 transition-transform" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
    </div>

    <nav class="flex-1 px-6 pt-4 space-y-2">
        <div class="px-2 space-y-2">
            {{-- Inicio --}}
            <a href="{{ route('dashboard') }}"
                :class="sidebarOpen
                    ?
                    '{{ request()->routeIs('dashboard') ? 'bg-white' : '' }} flex pl-2 py-2 ml-[20px] transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer' :
                    '{{ request()->routeIs('dashboard') ? 'bg-white' : '' }} flex justify-center px-4 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'">
                <div class="flex items-center w-full transition-all duration-300 ease-in-out">
                    <img src="{{ asset(request()->routeIs('dashboard') ? 'images/casaColor.svg' : 'images/casa.svg') }}"
                        class="w-4 h-4" alt="Inicio">
                    <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="ml-2 text-sm font-medium whitespace-nowrap {{ request()->routeIs('dashboard') ? 'text-[var(--color-textmarca)]' : 'text-[var(--color-text)]' }}">
                        {{ __('Inicio') }}
                    </span>
                </div>
            </a>

            {{-- Cultivos --}}
            @canany(['crear producto'])
                <a href="{{ route('productos.index') }}"
                    :class="sidebarOpen
                        ?
                        '{{ request()->routeIs('productos.index') ? 'bg-white' : '' }} flex pl-2 py-2 ml-[20px] transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer' :
                        '{{ request()->routeIs('productos.index') ? 'bg-white' : '' }} flex justify-center px-4 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'">
                    <div class="flex items-center w-full transition-all duration-300 ease-in-out">
                        <img src="{{ asset(request()->routeIs('productos.index') ? 'images/plantColor.svg' : 'images/plant.svg') }}"
                            class="w-4 h-4" alt="Cultivos">
                        <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="ml-2 text-sm font-medium whitespace-nowrap {{ request()->routeIs('productos.index') ? 'text-[var(--color-textmarca)]' : 'text-[var(--color-text)]' }}">
                            {{ __('Cultivos') }}
                        </span>
                    </div>
                </a>
            @endcanany

            {{-- Noticias --}}
            @canany(['crear noticia'])
                <a href="{{ route('noticias.index') }}"
                    :class="sidebarOpen
                        ?
                        '{{ request()->routeIs('noticias.index') ? 'bg-white' : '' }} flex pl-2 py-2 ml-[20px] transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer' :
                        '{{ request()->routeIs('noticias.index') ? 'bg-white' : '' }} flex justify-center px-4 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'">
                    <div class="flex items-center w-full transition-all duration-300 ease-in-out">
                        <img src="{{ asset(request()->routeIs('noticias.index') ? 'images/noticiasColor.svg' : 'images/noticias.svg') }}"
                            class="w-4 h-4" alt="Noticias">
                        <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="ml-2 text-sm font-medium whitespace-nowrap {{ request()->routeIs('noticias.index') ? 'text-[var(--color-textmarca)]' : 'text-[var(--color-text)]' }}">
                            {{ __('Noticias') }}
                        </span>
                    </div>
                </a>
            @endcanany

            {{-- Boletines --}}
            @canany(['crear boletin'])
                <a href="{{ route('boletines.index') }}"
                    :class="sidebarOpen
                        ?
                        '{{ request()->routeIs('boletines.index') ? 'bg-white' : '' }} flex pl-2 py-2 ml-[20px] transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer' :
                        '{{ request()->routeIs('boletines.index') ? 'bg-white' : '' }} flex justify-center px-4 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'">
                    <div class="flex items-center w-full transition-all duration-300 ease-in-out">
                        <img src="{{ asset(request()->routeIs('boletines.index') ? 'images/formColor.svg' : 'images/form.svg') }}"
                            class="w-4 h-4" alt="Boletines">
                        <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="ml-2 text-sm font-medium whitespace-nowrap {{ request()->routeIs('boletines.index') ? 'text-[var(--color-textmarca)]' : 'text-[var(--color-text)]' }}">
                            {{ __('Boletines') }}
                        </span>
                    </div>
                </a>
            @endcanany
        </div>
    </nav>

    <nav class="flex-1 px-6 pt-4 mt-40 space-y-2">
        <div class="px-2 space-y-2">
            {{-- Separar la navegacion principal de los ajustes --}}
            <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="px-7 py-2 text-sm text-[var(--color-ajustes)] mt-8">
                {{ __('Ajustes') }}
            </div>

            {{-- Gestion de Usuarios (el div con x-data que contiene el boton y el menu) --}}
            <div x-data="{ userMenuOpen: false }" class="space-x-2 relative"> {{-- Este div es el contenedor relative --}}
                @canany(['crear usuario'])
                    <a href="#" @click.prevent="userMenuOpen = !userMenuOpen" x-ref="userMenuButton"
                        :class="sidebarOpen
                            ?
                            '{{ request()->routeIs('usuarios.index') ? 'bg-white' : '' }} flex pl-2 py-2 ml-[20px] transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] text-white cursor-pointer' :
                            '{{ request()->routeIs('usuarios.index') ? 'bg-white' : '' }} flex justify-center px-4 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'">
                        <div class="flex items-center w-full transition-all duration-300 ease-in-out">
                            <img src="{{ asset(request()->routeIs('usuarios.index') ? 'images/IconColor.svg' : 'images/Icon.svg') }}"
                                class="w-4 h-4" alt="Usuarios">

                            {{-- El texto solo se muestra si el sidebar está abierto --}}
                            <span x-show="sidebarOpen" x-transition
                                class="ml-2 text-sm font-medium whitespace-nowrap {{ request()->routeIs('usuarios.index') ? 'text-[var(--color-textmarca)]' : 'text-[var(--color-text)]' }}">
                                {{ __('Gestion de usuarios') }}
                            </span>

                            {{-- Icono de flecha (solo se muestra si el sidebar está abierto) --}}
                            <img src="{{ asset(request()->routeIs('usuarios.index') ? 'images/menu.svg' : 'images/menu-hov.svg') }}"
                            class="w-4 h-4 ml-3"
                            alt="icono de abrir-menu" x-show="sidebarOpen" :class="userMenuOpen ? '-rotate-90' : ''">
                        </div>
                    </a>
                @endcanany

                {{-- Menu desplegable --}}
                <div x-show="userMenuOpen" @click.away="userMenuOpen = false"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="absolute z-50 w-auto py-2 bg-white shadow-2xl rounded-xl whitespace-nowrap border border-gray-200"
                    x-bind:style="!sidebarOpen ?
                        `top: ${$refs.userMenuButton.getBoundingClientRect().top}px;
                         left: ${$refs.userMenuButton.getBoundingClientRect().right + 8}px;` :
                        ''
                    "
                    :class="{
                        'top-full right-0 mt-2': sidebarOpen, // Posición cuando el sidebar está abierto
                    }"
                    style="transform-origin: top left;"
                >
                    <a href="{{ route('usuarios.index') }}"
                        class="block px-4 py-2 text-sm text-gray-700 rounded-xl hover:bg-gray-200">
                        <ul class="flex items-center">
                            <li class="mr-2">
                                <img src="{{ asset('images/list.svg') }}" class="w-3 h-5" alt="icono de carga masiva">
                            </li>
                            <li>{{ __('Lista de usuarios') }}</li>
                        </ul>
                    </a>

                    @if (Auth::user()->hasAnyRole(['SuperAdmin', 'Administrador']))
                        <button type="button"
                            class="block w-full px-4 py-2 text-sm text-left text-gray-700 rounded-xl hover:bg-gray-200"
                            @click="userMenuOpen = false; document.getElementById('create-user-button').click()">
                            <ul class="flex items-center">
                                <li class="mr-2">
                                    <img src="{{ asset('images/new.svg') }}" class="w-3 h-4"
                                        alt="icono de carga masiva">
                                </li>
                                <li>{{ __('Nuevo usuario') }}</li>
                            </ul>
                        </button>
                    @endif

                    <button type="button"
                        class="block w-full px-4 py-2 text-sm text-left text-gray-700 rounded-xl hover:bg-gray-200"
                        @click="userMenuOpen = false; document.getElementById('importCsvButton').click()">
                        <ul class="flex items-center">
                            <li class="mr-2">
                                <img src="{{ asset('images/carga.svg') }}" class="w-4 h-5" alt="icono de carga masiva">
                            </li>
                            <li>{{ __('Carga masiva de usuario') }}</li>
                        </ul>
                    </button>
                </div>
            </div>

            {{-- Accesibilidad --}}
            <a href="{{ route('accesibilidad.index') }}"
                :class="sidebarOpen
                    ?
                    '{{ request()->routeIs('accesibilidad.index') ? 'bg-white' : '' }} flex pl-2 py-2 ml-[20px] transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer' :
                    '{{ request()->routeIs('accesibilidad.index') ? 'bg-white' : '' }} flex justify-center px-4 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'">
                <div class="flex items-center w-full transition-all duration-300 ease-in-out">
                    <img src="{{ asset(request()->routeIs('accesibilidad.index') ? 'images/accesiColor.svg' : 'images/accesi.svg') }}"
                        class="w-4 h-4" alt="Accesibilidad">
                    <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="ml-2 text-sm font-medium whitespace-nowrap {{ request()->routeIs('accesibilidad.index') ? 'text-[var(--color-textmarca)]' : 'text-[var(--color-text)]' }}">
                        {{ __('Accesibilidad') }}
                    </span>
                </div>
            </a>

            {{-- Centro de Ayuda --}}
            <a href="{{ route('centroAyuda.index') }}"
                :class="sidebarOpen
                    ?
                    '{{ request()->routeIs('centroAyuda.index') ? 'bg-white' : '' }} flex pl-2 py-2 ml-[20px] transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer' :
                    '{{ request()->routeIs('centroAyuda.index') ? 'bg-white' : '' }} flex justify-center px-4 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'">
                <div class="flex items-center w-full transition-all duration-300 ease-in-out">
                    <img src="{{ asset(request()->routeIs('centroAyuda.index') ? 'images/pregColors.svg' : 'images/preg.svg') }}"
                        class="w-4 h-4" alt="Centro de Ayuda">
                    <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="ml-2 text-sm font-medium whitespace-nowrap {{ request()->routeIs('centroAyuda.index') ? 'text-[var(--color-textmarca)]' : 'text-[var(--color-text)]' }}">
                        {{ __('Centro de ayuda') }}
                    </span>
                </div>
            </a>

            {{-- Cerrar Sesion --}}
            <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                @csrf
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                    :class="sidebarOpen
                        ?
                        'flex pl-2 py-2 ml-[20px] transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer' :
                        'flex justify-center px-4 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'">
                    <div class="flex items-center w-full transition-all duration-300 ease-in-out">
                        <img src="{{ asset('images/off.svg') }}" class="w-4 h-4" alt="Cerrar Sesion">
                        <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="ml-2 text-sm font-medium text-[var(--color-text)] whitespace-nowrap">
                            {{ __('Cerrar sesion') }}
                        </span>
                    </div>
                </a>
            </form>
        </div>
    </nav>

    <div class="px-5">
        {{-- Perfil (Generalmente visible para todos) --}}
        <x-responsive-nav-link href="{{ route('profile.show') }}"
            class="rounded-3xl relative top-[-30px]"
            x-bind:class="sidebarOpen ? 'px-3 py-6' : 'flex justify-center p-0'">

            <div class="flex items-center w-full rounded-lg"
                x-bind:class="sidebarOpen
                    ?
                    'bg-[var(--color-profile)]  hover:bg-[var(--color-sidebarhoverbtn)] px-3 py-2' :
                    'justify-center px-0'">

                <img class="object-cover rounded-md size-10" src="{{ Auth::user()->profile_photo_url }}"
                    alt="{{ Auth::user()->name }}" />

                <div class="flex flex-col ml-3" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <span class="text-base font-bold text-gray-800">
                        {{ Auth::user()->name }}
                    </span>
                    <span class="text-sm text-gray-600">
                        {{-- Muestra el primer rol del usuario o 'Usuario' por defecto --}}
                        {{ Auth::user()->getRoleNames()->first() ?? 'Usuario' }}
                    </span>
                </div>
            </div>
        </x-responsive-nav-link>
    </div>
</aside>