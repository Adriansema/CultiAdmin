<div class="overflow-x-auto w-full rounded-xl">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-[var(--color-tabla)]">
            <tr>
                <th class="px-6 py-3 font-bold text-left text-gray-600">Nombre</th>
                <th class="px-6 py-3 font-bold text-left text-gray-600">Descripción</th>
                <th class="px-6 py-3 font-bold text-left text-gray-600">Fecha</th>
                <th class="px-6 py-3 font-bold text-left text-gray-600">Precio Alto</th>
                <th class="px-6 py-3 font-bold text-left text-gray-600">Precio Bajo</th>
                <th class="px-6 py-3 font-bold text-left text-gray-600">Estado</th>
                <th class="px-6 py-3 font-bold text-left text-gray-600">Acciones</th>
            </tr>
        </thead>
        <tbody id="boletines-table-body">
            @forelse ($boletines as $boletin)
                @include('boletines.partials.boletin_row', ['boletin' => $boletin])
            @empty
                <tr id="no-boletines-row">
                    <td colspan="9" class="px-4 py-4 text-sm text-center text-gray-500 whitespace-nowrap"> {{-- Ajustado colspan a 7 --}}
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