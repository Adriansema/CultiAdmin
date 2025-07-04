<div class="overflow-x-auto rounded-xl-2xl w-full">
    <table class="min-w-full text-md text-left">
        <thead class="bg-[var(--color-tabla)]">
            <tr>
                <th class="px-6 py-3 font-bold text-left text-gray-600">Creador</th>
                <th class="px-6 py-3 font-bold text-left text-gray-600">Autor</th>
                <th class="px-6 py-3 font-bold text-left text-gray-600">Tipo</th>
                <th class="px-6 py-3 font-bold text-left text-gray-600">Titulo</th>
                <th class="px-6 py-3 font-bold text-left text-gray-600">Clase</th>
                <th class="px-6 py-3 font-bold text-left text-gray-600">Pág.</th>
                <th class="px-6 py-3 font-bold text-left text-gray-600">Estado</th>
                <th class="px-6 py-3 font-bold text-left text-gray-600">Acciones</th>
            </tr>
        </thead>

        <tbody>
            @if ($noticias->total() === 0)
                <tr>
                    {{-- Ajustado el colspan a 9 para cubrir todas las columnas --}}
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                        @if (request()->has('q') && !empty(request()->get('q')))
                            No se encontraron noticias que coincidan con
                            "{{ htmlspecialchars(request()->get('q')) }}".
                        @else
                            No hay noticias registradas.
                        @endif
                    </td>
                </tr>
            @else
                {{-- Si hay noticias, las iteramos --}}
                @forelse($noticias as $noticia)
                    <tr class="bg-white hover:bg-gray-300">
                        <td class="px-4 py-2 text-sm">
                            {{ $noticia->user ? $noticia->user->name : 'Desconocido' }}
                        </td>
                        <td class="px-4 py-2 text-sm">
                            {{ $noticia->autor ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-2 text-sm">
                            {{ $noticia->tipo }}
                        </td>
                        <td class="px-4 py-2 text-sm">
                            {{ Str::limit($noticia->titulo, 30) ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-2 text-sm">
                            {{ $noticia->clase ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-2 text-sm">
                            {{ $noticia->numero_pagina }}
                        </td>
                        <td class="px-4 py-2">
                            <span
                                class="inline-block px-3 py-2 text-md font-semibold text-white rounded-xl
                                {{ $noticia->estado === 'aprobado' ? 'bg-green-600' : ($noticia->estado === 'pendiente' ? 'bg-yellow-500' : 'bg-red-600') }}">
                                {{ ucfirst($noticia->estado) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-sm">
                            <div class="flex items-center space-x-2">
                                @can('crear noticia')
                                    <a href="{{ route('noticias.show', $noticia->id_noticias) }}"
                                        class="px-3 py-2 text-sm text-center text-white bg-green-600 rounded-xl hover:bg-green-700">
                                        Ver
                                    </a>
                                @endcan

                                @can('editar noticia')
                                    <a href="{{ route('noticias.edit', $noticia->id_noticias) }}"
                                        class="px-3 py-2 text-sm text-center text-white bg-yellow-600 rounded-xl hover:bg-yellow-700">
                                        Editar
                                    </a>
                                @endcan

                                @can('eliminar noticia')
                                    <button type="button" onclick="mostrarModal('noticia', '{{ $noticia->id_noticias }}')"
                                        class="px-3 py-2 text-sm text-center text-white bg-red-600 rounded-xl hover:bg-red-700">
                                        Eliminar
                                    </button>
                                @endcan

                                @can('validar noticia')
                                    <button type="button"
                                        onclick="mostrarModal('validar-noticia', '{{ $noticia->id_noticias }}')"
                                        class="px-3 py-2 text-sm text-center text-white bg-blue-600 rounded-xl hover:bg-blue-700">
                                        Validar
                                    </button>
                                    @include('pendientes.partials.modal-noticia-validar')
                                @endcan

                                @can('validar noticia')
                                    <button type="button"
                                        onclick="mostrarModal('rechazar-noticia', '{{ $noticia->id_noticias }}')"
                                        class="px-3 py-2 text-sm text-center text-white bg-orange-600 rounded-xl hover:bg-orange-700">
                                        Rechazar
                                    </button>
                                    @include('pendientes.partials.modal-noticia-rechazar')
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
