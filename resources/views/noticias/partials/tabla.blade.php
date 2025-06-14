<div class="overflow-x-auto rounded-2xl">
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-[var(--color-tabla)]">
            <tr>
                <th class="px-4 py-2">Creador</th>
                <th class="px-4 py-2">Autor</th>
                <th class="px-4 py-2">Tipo</th>
                <th class="px-4 py-2">Titulo</th>
                <th class="px-4 py-2">Clase</th>
                <th class="px-4 py-2">Pág.</th>
                <th class="px-4 py-2">Estado</th>
                <th class="px-4 py-2">Acciones</th>
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
                    <tr class="bg-white hover:bg-gray-200">
                        <td class="px-6 py-4 text-sm">
                            {{ $noticia->user ? $noticia->user->name : 'Desconocido' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ $noticia->autor ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ $noticia->tipo }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ Str::limit($noticia->titulo, 30) ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ $noticia->clase ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ $noticia->numero_pagina }}
                        </td>
                        <td class="px-4 py-2">
                            <span
                                class="inline-block px-3 py-1 text-sm font-semibold text-white rounded
                                {{ $noticia->estado === 'aprobado' ? 'bg-green-600' : ($noticia->estado === 'pendiente' ? 'bg-yellow-500' : 'bg-red-600') }}">
                                {{ ucfirst($noticia->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('noticias.noticias.show', $noticia->id_noticias) }}"
                                    class="text-blue-600 hover:text-blue-900">Ver</a>
                                <a href="{{ route('noticias.noticias.edit', $noticia->id_noticias) }}"
                                    class="text-yellow-600 hover:text-yellow-900">Editar</a>
                                <form action="{{ route('noticias.noticias.destroy', $noticia->id_noticias) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta noticia?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                </form>
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
