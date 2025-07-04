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
