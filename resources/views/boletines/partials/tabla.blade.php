<div class="overflow-x-auto bg-white rounded shadow">
    <table class="min-w-full text-sm table-auto">
        <thead class="text-gray-700 bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left whitespace-nowrap">Contenido</th>
                <th class="px-4 py-2 text-left whitespace-nowrap">Fechas</th>
                <th class="px-4 py-2 text-left whitespace-nowrap">Estados</th>
                <th class="px-4 py-2 text-left whitespace-nowrap">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($boletines as $boletin)
                @include('boletines.partials.boletin_row', ['boletin' => $boletin])
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                        No hay boletines registrados aún.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
