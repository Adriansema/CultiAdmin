<div class="overflow-x-auto rounded-2xl">
    <table class="min-w-full text-md text-left">
        <thead class="bg-[var(--color-tabla)]">
            <tr>
                <th class="px-4 py-2 font-bold text-left text-gray-600">
                    <div class="flex items-center">
                        <div class="flex flex-wrap items-center justify-between flex-grow">
                            <span class="whitespace-nowrap flex-shrink-0">Tipo</span>
                            <div class="flex flex-col ml-2 flex-shrink-0">
                                <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                    data-sort-field="tipo" data-sort-direction="asc">
                                    <img src="{{ asset('images/asce.svg') }}"
                                        class="w-4 h-4 relative inset-0 block normal-icon" alt="Icono ascendente">
                                    <img src="{{ asset('images/asce-hover.svg') }}"
                                        class="relative inset-0 hidden w-4 h-4 hover-icon" alt="Icono ascendente hover">
                                </button>
                                <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                    data-sort-field="tipo" data-sort-direction="desc">
                                    <img src="{{ asset('images/desce.svg') }}"
                                        class="w-4 h-4 relative inset-0 block normal-icon" alt="Icono descendente">
                                    <img src="{{ asset('images/desce-hover.svg') }}"
                                        class="relative inset-0 hidden w-4 h-4 hover-icon"
                                        alt="Icono descendente hover">
                                </button>
                            </div>
                        </div>
                    </div>
                </th>
                <th class="px-4 py-2 font-bold text-left text-gray-600">
                    <div class="flex items-center">
                        <div class="flex flex-wrap items-center justify-between flex-grow">
                            <span class="whitespace-nowrap flex-shrink-0">Creador</span>
                            <div class="flex flex-col ml-2 flex-shrink-0">
                                <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                    data-sort-field="creador_name" data-sort-direction="asc">
                                    <img src="{{ asset('images/asce.svg') }}"
                                        class="w-4 h-4 relative inset-0 block normal-icon" alt="Icono ascendente">
                                    <img src="{{ asset('images/asce-hover.svg') }}"
                                        class="relative inset-0 hidden w-4 h-4 hover-icon" alt="Icono ascendente hover">
                                </button>
                                <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                    data-sort-field="creador" data-sort-direction="desc">
                                    <img src="{{ asset('images/desce.svg') }}"
                                        class="w-4 h-4 relative inset-0 block normal-icon" alt="Icono descendente">
                                    <img src="{{ asset('images/desce-hover.svg') }}"
                                        class="relative inset-0 hidden w-4 h-4 hover-icon"
                                        alt="Icono descendente hover">
                                </button>
                            </div>
                        </div>
                    </div>
                </th>
                <th class="px-4 py-2 font-bold text-left text-gray-600">
                    <div class="flex items-center">
                        <div class="flex flex-wrap items-center justify-between flex-grow">
                            <span class="whitespace-nowrap flex-shrink-0">Autor</span>
                            <div class="flex flex-col ml-2 flex-shrink-0">
                                <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                    data-sort-field="autor" data-sort-direction="asc">
                                    <img src="{{ asset('images/asce.svg') }}"
                                        class="w-4 h-4 relative inset-0 block normal-icon" alt="Icono ascendente">
                                    <img src="{{ asset('images/asce-hover.svg') }}"
                                        class="relative inset-0 hidden w-4 h-4 hover-icon" alt="Icono ascendente hover">
                                </button>
                                <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                    data-sort-field="autor" data-sort-direction="desc">
                                    <img src="{{ asset('images/desce.svg') }}"
                                        class="w-4 h-4 relative inset-0 block normal-icon" alt="Icono descendente">
                                    <img src="{{ asset('images/desce-hover.svg') }}"
                                        class="relative inset-0 hidden w-4 h-4 hover-icon"
                                        alt="Icono descendente hover">
                                </button>
                            </div>
                        </div>
                    </div>
                </th>
                <th class="px-4 py-2 font-bold text-left text-gray-600">
                    <div class="flex items-center">
                        <div class="flex flex-wrap items-center justify-between flex-grow">
                            <span class="whitespace-nowrap flex-shrink-0">Título</span>
                            <div class="flex flex-col ml-2 flex-shrink-0">
                                <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                    data-sort-field="titulo" data-sort-direction="asc">
                                    <img src="{{ asset('images/asce.svg') }}"
                                        class="w-4 h-4 relative inset-0 block normal-icon" alt="Icono ascendente">
                                    <img src="{{ asset('images/asce-hover.svg') }}"
                                        class="relative inset-0 hidden w-4 h-4 hover-icon" alt="Icono ascendente hover">
                                </button>
                                <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                    data-sort-field="titulo" data-sort-direction="desc">
                                    <img src="{{ asset('images/desce.svg') }}"
                                        class="w-4 h-4 relative inset-0 block normal-icon" alt="Icono descendente">
                                    <img src="{{ asset('images/desce-hover.svg') }}"
                                        class="relative inset-0 hidden w-4 h-4 hover-icon"
                                        alt="Icono descendente hover">
                                </button>
                            </div>
                        </div>
                    </div>
                </th>
                <th class="px-4 py-2 font-bold text-left text-gray-600">
                    <div class="flex items-center">
                        <div class="flex flex-wrap items-center justify-between flex-grow">
                            <span class="whitespace-nowrap flex-shrink-0">Fecha</span>
                            <div class="flex flex-col ml-2 flex-shrink-0">
                                <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                    data-sort-field="created_at" data-sort-direction="asc">
                                    <img src="{{ asset('images/asce.svg') }}"
                                        class="w-4 h-4 relative inset-0 block normal-icon" alt="Icono ascendente">
                                    <img src="{{ asset('images/asce-hover.svg') }}"
                                        class="relative inset-0 hidden w-4 h-4 hover-icon"
                                        alt="Icono ascendente hover">
                                </button>
                                <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                    data-sort-field="created_at" data-sort-direction="desc">
                                    <img src="{{ asset('images/desce.svg') }}"
                                        class="w-4 h-4 relative inset-0 block normal-icon" alt="Icono descendente">
                                    <img src="{{ asset('images/desce-hover.svg') }}"
                                        class="relative inset-0 hidden w-4 h-4 hover-icon"
                                        alt="Icono descendente hover">
                                </button>
                            </div>
                        </div>
                    </div>
                </th>
                <th class="px-4 py-2 font-bold text-left text-gray-600">
                    <div class="flex items-center">
                        <div class="flex flex-wrap items-center justify-between flex-grow">
                            <span class="whitespace-nowrap flex-shrink-0">Estado</span>
                            <div class="flex flex-col ml-2 flex-shrink-0">
                                <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                    data-sort-field="estado" data-sort-direction="asc">
                                    <img src="{{ asset('images/asce.svg') }}"
                                        class="w-4 h-4 relative inset-0 block normal-icon" alt="Icono ascendente">
                                    <img src="{{ asset('images/asce-hover.svg') }}"
                                        class="relative inset-0 hidden w-4 h-4 hover-icon"
                                        alt="Icono ascendente hover">
                                </button>
                                <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                    data-sort-field="estado" data-sort-direction="desc">
                                    <img src="{{ asset('images/desce.svg') }}"
                                        class="w-4 h-4 relative inset-0 block normal-icon" alt="Icono descendente">
                                    <img src="{{ asset('images/desce-hover.svg') }}"
                                        class="relative inset-0 hidden w-4 h-4 hover-icon"
                                        alt="Icono descendente hover">
                                </button>
                            </div>
                        </div>
                    </div>
                </th>
                <th class="px-4 py-2 font-bold text-left text-gray-600">Acciones</th>
            </tr>
        </thead>

        <tbody>
            @if ($noticias->total() === 0)
                <tr>
                    {{-- Ajustado el colspan a 9 para cubrir todas las columnas --}}
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                        @if (request()->has('q') && !empty(request()->get('q')))
                            No se encontraron noticias que coincidan con
                            <b>{{ htmlspecialchars(request()->get('q')) }}</b>.
                        @else
                            No hay noticias registradas.
                        @endif
                    </td>
                </tr>
            @else
                {{-- Si hay noticias, las iteramos --}}
                @forelse($noticias as $noticia)
                    <tr class="bg-white hover:bg-gray-200">
                        <td class="px-6 py-4">
                            {{ $noticia->tipo }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $noticia->user ? $noticia->user->name : 'Desconocido' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $noticia->autor ?? 'N/A' }}
                        </td>

                        <td class="px-6 py-4">
                            {{ Str::limit($noticia->titulo, 30) ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $noticia->created_at->locale('es')->translatedFormat('d \d\e F \d\e\l Y h:i a') }}
                            <span class="block text-xs text-gray-500">
                                ({{ $noticia->created_at->diffForHumans() }})
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-block px-3 py-2 text-md font-semibold text-white rounded-xl
                                {{ $noticia->estado === 'aprobado' ? 'bg-green-600' : ($noticia->estado === 'pendiente' ? 'bg-yellow-500' : 'bg-red-600') }}">
                                {{ ucfirst($noticia->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                @can('crear noticia')
                                    <a href="{{ route('noticias.show', $noticia->id_noticias) }}"
                                        class="px-3 py-2 text-center text-white bg-green-600 rounded-xl hover:bg-green-700">
                                        Ver
                                    </a>
                                @endcan

                                @can('editar noticia')
                                    <a href="{{ route('noticias.edit', $noticia->id_noticias) }}"
                                        class="px-3 py-2 text-center text-white bg-yellow-600 rounded-xl hover:bg-yellow-700">
                                        Editar
                                    </a>
                                @endcan

                                @can('eliminar noticia')
                                    <button type="button"
                                        onclick="mostrarModal('noticia', '{{ $noticia->id_noticias }}')"
                                        class="px-3 py-2 text-center text-white bg-red-600 rounded-xl hover:bg-red-700">
                                        Eliminar
                                    </button>
                                @endcan

                                @can('validar noticia')
                                    <button type="button"
                                        onclick="mostrarModal('validar-noticias', '{{ $noticia->id_noticias }}')"
                                        class="px-3 py-2 text-center text-white bg-blue-600 rounded-xl hover:bg-blue-700">
                                        Validar
                                    </button>
                                @endcan

                                @can('validar noticia')
                                    <button type="button"
                                        onclick="mostrarModal('rechazar-noticias', '{{ $noticia->id_noticias }}')"
                                        class="px-3 py-2 text-center text-white bg-orange-600 rounded-xl hover:bg-orange-700">
                                        Rechazar
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                            No se encontraron noticias para mostrar.
                        </td>
                    </tr>
                @endforelse
            @endif
        </tbody>
    </table>
</div>
