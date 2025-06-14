<div class="overflow-x-auto rounded-2xl">
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-[var(--color-tabla)]">
            <tr>
                <th class="px-4 py-2 text-left whitespace-nowrap">Contenido</th>
                <th class="px-4 py-2 text-left whitespace-nowrap">Fechas</th>
                <th class="px-4 py-2 text-left whitespace-nowrap">Estados</th>
                <th class="px-4 py-2 text-left whitespace-nowrap">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @if ($boletines->total() === 0)
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
                @forelse ($boletines as $boletin)
                    @include('boletines.partials.boletin_row', ['boletin' => $boletin])
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            No hay boletines registrados aún.
                        </td>
                    </tr>
                @endforelse
            @endif
        </tbody>
    </table>
</div>
