{{-- @if ($paginator->hasPages())
    <div class="flex flex-col items-center justify-between px-4 py-3 bg-white sm:flex-row sm:px-6 rounded-b-xl">

        {{-- Sección izquierda: Selector "Elementos por página" y conteo de ítems --
        <div class="flex items-center mb-4 space-x-4 sm:mb-0">
            {{-- Selector "Elementos por página" --
            <div class="flex items-center space-x-2">
                <label for="per_page" class="text-sm text-gray-700 whitespace-nowrap">Elementos por página</label>
                <select id="per_page" name="per_page"
                        class="block w-20 py-1 text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        onchange="window.location.href = this.value;">
                    {{-- Las opciones deben ser las mismas que definiste en el controlador --
                    @foreach ([5, 10, 25, 50, 100] as $option)
                        <option value="{{ $paginator->url(1) }}&per_page={{ $option }}&q={{ request()->input('q') }}"
                                @if ($paginator->perPage() == $option) selected @endif>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Información de "Mostrando X a Y de Z resultados" --
            <p class="text-sm text-gray-700 whitespace-nowrap">
                Mostrando
                <span class="font-medium">{{ $paginator->firstItem() }}</span>
                a
                <span class="font-medium">{{ $paginator->lastItem() }}</span>
                de
                <span class="font-medium">{{ $paginator->total() }}</span>
                resultados
            </p>
        </div>

        {{-- Sección central: Enlaces de paginación --
        <div class="flex justify-center flex-1 sm:justify-start">
            <div>
                <nav class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    {{-- Enlace a la página anterior --
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="@lang('pagination.previous')">
                            <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-l-md" aria-hidden="true">
                                {{-- Icono de Página Anterior (ejemplo con SVG) --
                                <img src="{{ asset('images/retro(2).svg') }}" class="w-5 h-5" alt="Anterior">
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}&per_page={{ $paginator->perPage() }}&q={{ request()->input('q') }}" rel="prev" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-blue-500 focus:border-blue-500" aria-label="@lang('pagination.previous')">
                            {{-- Icono de Página Anterior (ejemplo con SVG) --
                            <img src="{{ asset('images/retro(1).svg') }}" class="w-5 h-5" alt="Anterior">
                        </a>
                    @endif

                    {{-- Elementos de Paginación (números de página y puntos suspensivos) -
                    @foreach ($elements as $element)
                        {{-- Separador "..." --
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 cursor-default">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Arreglo de Enlaces (números de página) --
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        {{-- Estilo para la página activa (ejemplo usando una variable CSS que podrías tener) --
                                        <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium bg-[var(--color-Gestion)] text-white border border-[var(--color-Gestion)] cursor-default z-10 rounded-md">
                                            {{ $page }}
                                        </span>
                                    </span>
                                @else
                                    <a href="{{ $url }}&per_page={{ $paginator->perPage() }}&q={{ request()->input('q') }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Enlace a la página siguiente --
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}&per_page={{ $paginator->perPage() }}&q={{ request()->input('q') }}" rel="next" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-blue-500 focus:border-blue-500" aria-label="@lang('pagination.next')">
                            {{-- Icono de Página Siguiente (ejemplo con SVG) -
                            <img src="{{ asset('images/sgts(2).svg') }}" class="w-5 h-5" alt="Siguiente">
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="@lang('pagination.next')">
                            <span class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-r-md" aria-hidden="true">
                                {{-- Icono de Página Siguiente (ejemplo con SVG) --
                                <img src="{{ asset('images/sgts(1).svg') }}" class="w-5 h-5" alt="Siguiente">
                            </span>
                        </span>
                    @endif
                </nav>
            </div>
        </div>

        {{-- Sección derecha: Conteo de páginas "X de Y Páginas" (solo en pantallas grandes)
        <div class="hidden mt-4 sm:flex sm:flex-1 sm:items-center sm:justify-end sm:mt-0">
            <p class="text-sm text-gray-700 whitespace-nowrap">
                <span class="font-medium">{{ $paginator->currentPage() }}</span>
                de
                <span class="font-medium">{{ $paginator->lastPage() }}</span>
                Páginas
            </p>
        </div>
    </div>
@endif
 --}}

{{--  @if ($paginator->hasPages()) --}}
{{-- Contenedor principal: Usamos justify-between en sm para alinear izquierda, centro y derecha --
    <div class="flex flex-col items-center justify-center px-4 py-3 bg-blue-200 sm:flex-row sm:justify-between sm:px-6 rounded-b-xl">

        {{-- Sección izquierda: Selector "Elementos por página" --}}
{{-- Añadimos un margen inferior en móvil para separar del bloque de paginación. --
        <div class="flex items-center mb-3 space-x-4 sm:mb-0">
            <div class="flex items-center space-x-2">
                <label for="per_page" class="text-sm text-gray-700 whitespace-nowrap">Elementos por página</label>
                <select id="per_page" name="per_page"
                        class="block py-1 text-sm border-gray-300 w-18 rounded-xl focus:ring-blue-500 focus:border-blue-500 "
                        onchange="window.location.href = this.value;">
                    @foreach ([5, 10, 25, 50, 100] as $option)
                        <option value="{{ $paginator->url(1) }}&per_page={{ $option }}&q={{ request()->input('q') }}"
                                @if ($paginator->perPage() == $option) selected @endif>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Sección central: Enlaces de paginación --}}
{{-- flex-1 permite que crezca y ocupe el espacio disponible entre los lados. --}}
{{-- justify-center para centrar los enlaces dentro de esta sección. --}}
{{-- overflow-x-auto para un scroll horizontal SÓLO DENTRO de esta sección si los números desbordan. --
        <div class="flex justify-center flex-1 overflow-x-auto">
            <nav class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                {{-- Enlace a la página anterior --
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="@lang('pagination.previous')">
                        <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-l-md" aria-hidden="true">
                            <img src="{{ asset('images/retro(1).svg') }}" class="w-5 h-5" alt="Anterior">
                        </span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}&per_page={{ $paginator->perPage() }}&q={{ request()->input('q') }}" rel="prev" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-blue-500 focus:border-blue-500" aria-label="@lang('pagination.previous')">
                        <img src="{{ asset('images/retro(2).svg') }}" class="w-5 h-5" alt="Anterior">
                    </a>
                @endif

                {{-- Elementos de Paginación (números de página y puntos suspensivos) --}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}
                    @if (is_string($element))
                        <span aria-disabled="true">
                            <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 cursor-default">{{ $element }}</span>
                        </span>
                    @endif

                    {{-- Array Of Links --}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page">
                                    {{-- Padding reducido a px-3 --}
                                    <span class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium bg-[var(--color-Gestion)] text-white border border-[var(--color-Gestion)] cursor-default z-10 rounded-md">
                                        {{ $page }}
                                    </span>
                                </span>
                            @else
                                {{-- Padding reducido a px-3 --}
                                <a href="{{ $url }}&per_page={{ $paginator->perPage() }}&q={{ request()->input('q') }}" class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}&per_page={{ $paginator->perPage() }}&q={{ request()->input('q') }}" rel="next" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-blue-500 focus:border-blue-500" aria-label="@lang('pagination.next')">
                        <img src="{{ asset('images/sgts(1).svg') }}" class="w-5 h-5" alt="Siguiente">
                    </a>
                @else
                    <span aria-disabled="true" aria-label="@lang('pagination.next')">
                        <span class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-r-md" aria-hidden="true">
                            <img src="{{ asset('images/sgts(2).svg') }}" class="w-5 h-5" alt="Siguiente">
                        </span>
                    </span>
                @endif
            </nav>
        </div>

        {{-- Sección derecha: Conteo de páginas "X de Y Páginas" --}}
{{-- Se muestra solo en pantallas sm (small) y superiores. En móviles se oculta. --}
        <div class="flex items-center mt-2 sm:justify-end sm:mt-0 sm:flex">
            <p class="text-sm text-gray-700 whitespace-nowrap">
                {{-- Usamos currentPage() y lastPage() para el formato "1 de 13 Páginas" --}}
{{-- Si quisieras "1 - 10 de 13 Páginas" basado en ítems, sería:
                <span class="font-medium">{{ $paginator->firstItem() }}</span>
                -
                <span class="font-medium">{{ $paginator->lastItem() }}</span>
                de
                <span class="font-medium">{{ $paginator->total() }}</span>
                resultados
                pero esto es para ítems, no páginas completas.
                Para el formato que sugiere la imagen "1 - 10 de 13 Páginas",
                una interpretación común para "1-10" en el contexto de "Páginas" sería
                la página actual (1) y el número de elementos por página (10).
                Podríamos usar: --}
                <span class="font-medium">{{ $paginator->currentPage() }}</span>
                -
                <span class="text-sm font-medium">{{ $paginator->perPage() }}</span>
                de
                <span class="text-sm font-medium">{{ $paginator->lastPage() }}</span>
                Páginas
            </p>
        </div>
    </div>
@endif --}}

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        <div class="flex items-center justify-between sm:hidden">
            {{-- Mobile Pagination (Puedes dejarlo o quitarlo si no lo necesitas en móvil) --}}
            <a href="{{ $paginator->previousPageUrl() }}"
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                {!! __('pagination.previous') !!}
            </a>

            <a href="{{ $paginator->nextPageUrl() }}"
                class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                {!! __('pagination.next') !!}
            </a>
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between px-4"> {{-- Contenedor principal --}}
            {{-- Selector de "Elementos por página" --}}
            <div class="flex items-center mr-10">
                <label for="perPage" class="text-sm font-medium text-gray-700 mr-2">Elementos por página</label>
                <div class="relative custom-select-wrapper"> {{-- Agregamos un contenedor para posicionar la flecha --}}
                    <select name="perPage" id="perPage" onchange="window.location.href = this.value"
                        class="py-1 px-2 pl-2 border border-gray-300 bg-gray-200 rounded-lg shadow-sm sm:text-sm custom-select">
                        @foreach ([5, 10, 25, 50, 100] as $option)
                            <option value="{{ $paginator->url(1) }}&per_page={{ $option }}"
                                {{ $paginator->perPage() == $option ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Aquí va tu icono de flecha, posicionado absolutamente --}}
                    <img src="{{ asset('images/cam.svg') }}" class="custom-select-arrow w-5 h-4"
                        alt="Flecha desplegable">
                </div>
            </div>

            <div class="flex items-center">
                {{-- Páginas previas (<< <) --}}
                @if ($paginator->onFirstPage())
                    <span
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 border border-gray-300 rounded-lg leading-5 cursor-default">
                        <img src="{{ asset('images/retro(2).svg') }}" class="w-5 h-5" alt="Anterior">
                    </span>
                    <span
                        class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 border border-gray-300 rounded-lg leading-5 cursor-default">
                        <img src="{{ asset('images/retro(1).svg') }}" class="w-5 h-5" alt="Anterior">
                    </span>
                @else
                    <a href="{{ $paginator->url(1) }}&per_page={{ $paginator->perPage() }}" rel="first"
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg leading-5 hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-gray-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                        <img src="{{ asset('images/retro(2).svg') }}" class="w-5 h-5" alt="Anterior">
                    </a>
                    <a href="{{ $paginator->previousPageUrl() }}&per_page={{ $paginator->perPage() }}" rel="prev"
                        class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-700 border border-gray-300 rounded-lg leading-5 hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-gray-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                        <img src="{{ asset('images/retro(1).svg') }}" class="w-5 h-5" alt="Anterior">
                    </a>
                @endif

                {{-- Enlaces numéricos --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span
                            class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 border border-gray-300 leading-5 cursor-default">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page"
                                    class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-white bg-[var(--color-pag)] border border-[var(--color-pag)] leading-5 rounded-lg mx-1">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}&per_page={{ $paginator->perPage() }}"
                                    class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 border border-gray-300 leading-5 rounded-lg hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-gray-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 mx-1">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Páginas siguientes (> >>) --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}&per_page={{ $paginator->perPage() }}" rel="next"
                        class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-700 border border-gray-300 leading-5 hover:text-gray-500 rounded-lg focus:outline-none focus:ring ring-gray-300 focus:border-gray-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                        <img src="{{ asset('images/sgts(1).svg') }}" class="w-5 h-5" alt="Anterior">
                    </a>
                    <a href="{{ $paginator->url($paginator->lastPage()) }}&per_page={{ $paginator->perPage() }}"
                        rel="last"
                        class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-700 border border-gray-300 rounded-lg leading-5 hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-gray-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                        <img src="{{ asset('images/sgts(2).svg') }}" class="w-5 h-5" alt="Anterior">
                    </a>
                @else
                    <span
                        class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 border border-gray-300 rounded-lg leading-5 cursor-default">
                        <img src="{{ asset('images/sgts(1).svg') }}" class="w-5 h-5" alt="Anterior">
                    </span>
                    <span
                        class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 border border-gray-300 rounded-lg leading-5 cursor-default">
                        <img src="{{ asset('images/sgts(2).svg') }}" class="w-5 h-5" alt="Anterior">
                    </span>
                @endif
            </div>

            {{-- Texto de paginación (1 - 10 de 13 Páginas) --}}
            <div class="ml-4 text-sm text-gray-700 leading-5">
                <span class="font-medium">{{ $paginator->firstItem() }}</span> - <span
                    class="font-medium">{{ $paginator->lastItem() }}</span> de <span
                    class="font-medium">{{ $paginator->total() }}</span> Páginas
                {{-- <span class="ml-2">(Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }})</span> --}}
            </div>
        </div>
    </nav>
@endif
