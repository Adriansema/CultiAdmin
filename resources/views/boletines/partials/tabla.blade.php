{{-- resources/views/boletines/partials/tabla.blade.php --}}

<div class="overflow-x-auto bg-white rounded-xl shadow-sm mb-6">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Contenido
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Fecha
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Estado
                </th>
                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Acciones
                </th>
            </tr>
        </thead>
        <tbody id="boletines-table-body" class="bg-white divide-y divide-gray-200">
            @forelse ($boletines as $boletin)
                @include('boletines.partials.boletin_row', ['boletin' => $boletin])
            @empty
                <tr id="no-boletines-row"> {{-- ID para poder remover esta fila dinámicamente --}}
                    <td colspan="4" class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        No hay boletines para mostrar.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación --}}
<div class="mt-4">
    {{ $boletines->links() }}
</div>
