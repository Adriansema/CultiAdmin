<div class="overflow-x-auto w-full rounded-xl">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-[var(--color-tabla)]">
            <tr>
                <th class="px-4 py-3">Nombre</th>
                <th class="px-4 py-3">Descripción</th>
                <th class="px-4 py-3">Fecha</th>
                <th class="px-4 py-3">Precio Alto</th>
                <th class="px-4 py-3">Precio Bajo</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3se">Acciones</th>
            </tr>
        </thead>
        <tbody id="boletines-table-body">
            @forelse ($boletines as $boletin)
                @include('boletines.partials.boletin_row', ['boletin' => $boletin])
            @empty
                <tr id="no-boletines-row">
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                        No hay boletines para mostrar.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $boletines->links() }}
</div>
