{{-- Este div contiene solo el HTML del sidebar --}}
{{-- Ajustes clave: `fixed inset-y-0 left-0 z-40` para posicionamiento móvil. --}}
{{-- `md:relative md:translate-x-0 md:w-64` para desktop (siempre visible y ancho completo). --}}
{{-- `transform transition-transform duration-300 ease-in-out` para animaciones. --}}
{{-- `x-bind:class` para controlar `translate-x-full` en móviles y el ancho en desktop. --}}
<div x-bind:class="{
        'w-64': sidebarOpen && window.innerWidth >= 768,       {{-- Ancho completo en desktop si está abierto --}}
        'w-28': !sidebarOpen && window.innerWidth >= 768,      {{-- Ancho colapsado en desktop si está cerrado --}}
        'w-64 -translate-x-full': !sidebarOpen && window.innerWidth < 768, {{-- Oculto en móvil si está cerrado --}}
        'w-64 translate-x-0': sidebarOpen && window.innerWidth < 768      {{-- Visible en móvil si está abierto --}}
    }"
    class="h-full flex flex-col transition-all duration-300 ease-in-out bg-[#00304D] text-white flex-shrink-0 overflow-y-auto
           fixed inset-y-0 left-0 z-40 md:relative md:translate-x-0"> {{-- Eliminado 'overflow-x-hidden' --}}

    <div class="flex items-center justify-between px-4 py-3">
        {{-- Logo + boton de colapsar --}}
        <div class="flex items-center shrink-0" :class="!sidebarOpen && 'justify-center w-full'">
            <a href="{{ route('dashboard') }}">
                <x-application-mark class="block w-auto h-9" />
            </a>
        </div>
        {{-- Botón para colapsar en desktop, o cerrar en móvil --}}
        <button @click="sidebarOpen = !sidebarOpen"
                class="-ml-2 text-[var(--color-text)] rounded hover:bg-[var(--color-sidebarhoverbtn)] transition-transform duration-700 ease-in-out hover:translate-x-1"
                :class="{
                    'rotate-180 mx-auto': !sidebarOpen && window.innerWidth >= 768, {{-- Rotar solo en desktop colapsado --}}
                    'md:block': window.innerWidth >= 768, {{-- Visible en md+ --}}
                    'hidden': window.innerWidth < 768 {{-- Oculto en móviles --}}
                }">
                <svg class="w-4 h-5 transition-transform" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            {{-- Botón de CERRAR sidebar para pantallas móviles --}}
            <button @click="sidebarOpen = false"
                    class="ml-auto text-white focus:outline-none md:hidden"> {{-- Solo visible en móviles --}}
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
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

    <nav class="flex-1 px-6 pt-4 space-y-2 mt-40">
        <div class="px-2 space-y-2">
            {{-- Separar la navegacion principal de los ajustes --}}
            <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="px-7 py-2 text-sm text-[var(--color-ajustes)] mt-8">
                {{ __('Ajustes') }}
            </div>

            {{-- Gestion de Usuarios (el div con x-data que contiene el boton y el menu) --}}
            {{-- Este div ya no necesita ser 'relative' para el menu desplegable si usamos 'fixed' --}}
            <div x-data="{ userMenuOpen: false }" class="px-2 space-y-2">
                @canany(['crear usuario'])
                    <a href="#" @click.prevent="userMenuOpen = !userMenuOpen" x-ref="userMenuButton"
                        {{-- Anadir una referencia para Alpine.js --}}
                        :class="sidebarOpen
                            ?
                            '{{ request()->routeIs('usuarios.index') ? 'bg-white' : '' }} flex pl-2 py-2 ml-[20px] transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer' :
                            '{{ request()->routeIs('usuarios.index') ? 'bg-white' : '' }} flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'">
                        <div class="flex items-center w-full transition-all duration-300 ease-in-out">
                            <img src="{{ asset(request()->routeIs('usuarios.index') ? 'images/IconColor.svg' : 'images/Icon.svg') }}"
                                class="w-4 h-4" alt="Usuarios">
                            <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="ml-2 text-sm font-medium whitespace-nowrap {{ request()->routeIs('usuarios.index') ? 'text-[var(--color-textmarca)]' : 'text-[var(--color-text)]' }}">
                                {{ __('Gestion de usuarios') }}
                            </span>
                            {{-- Icono de flecha para indicar que es un menu desplegable --}}
                            <img src="{{ asset('images/abrir-menu-2.svg') }}" class="w-3 h-4 ml-2"
                                alt="icono de abrir-menu" x-show="sidebarOpen" :class="userMenuOpen ? '-rotate-90' : ''">
                        </div>
                    </a>
                @endcanany

                {{-- Menu desplegable --}}
                <div x-show="userMenuOpen" @click.away="userMenuOpen = false"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    x-init="$watch('userMenuOpen', (value) => {
                        if (value) { // Se simplifica la lógica, ya no es necesario separar por sidebarOpen
                            $nextTick(() => {
                                const buttonRect = $refs.userMenuButton.getBoundingClientRect();
                                // Calcular la posición en relación con la ventana (viewport)
                                $el.style.top = `${buttonRect.top}px`; // Alinear con la parte superior del botón
                                // Ajustar 'left' según el estado de sidebarOpen para que el menú salga del sidebar
                                if (window.innerWidth >= 768) { // Desktop
                                    if (sidebarOpen) { // Sidebar expandido
                                        $el.style.left = `${buttonRect.right + 10}px`; // Un poco a la derecha del botón
                                    } else { // Sidebar colapsado
                                        $el.style.left = `${buttonRect.left + 50}px`; // Más a la derecha para que no se oculte detrás del contenido
                                    }
                                } else { // Mobile
                                    // Para móvil, el sidebar es un overlay. El menú debería aparecer a la derecha del botón dentro del sidebar.
                                    // Opcional: Centrarlo o darle un ancho completo dentro del overlay.
                                    // Si quieres que salga del sidebar en móvil, el 'left' es relativo al viewport.
                                    // Para que se vea dentro del sidebar, necesitaría ser `position: absolute` dentro de un contenedor `relative` dentro del sidebar.
                                    // Por ahora, lo haremos que se muestre a la derecha, asumiendo que el sidebar es un overlay amplio.
                                    $el.style.left = `${buttonRect.right + 10}px`;
                                }
                                $el.style.minWidth = `200px`; // Ancho mínimo para que quepa el texto. Ajusta si es necesario.
                                $el.style.position = 'fixed'; // Asegurarse de que sea fixed
                                $el.style.zIndex = '50'; // Asegurarse de que esté por encima de todo
                            });
                        }
                    });" class="fixed z-50 w-auto bg-white rounded-xl shadow-2xl py-2">

                    <a href="{{ route('usuarios.index') }}"
                        class="block px-4 py-2 text-sm rounded-xl text-gray-700 hover:bg-gray-200">
                        <ul class="flex items-center">
                            <li class="mr-2">
                                <img src="{{ asset('images/list.svg') }}" class="w-3 h-5" alt="icono de carga masiva">
                            </li>
                            <li>{{ __('Lista de usuarios') }}</li>
                        </ul>
                    </a>

                    @if (Auth::user()->hasAnyRole(['SuperAdmin', 'Administrador']))
                        <button type="button"
                            class="block w-full rounded-xl text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-200"
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
                        class="block w-full rounded-xl text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-200"
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
</div>