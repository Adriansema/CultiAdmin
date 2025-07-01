<div class="mb-6 overflow-x-auto bg-white shadow-sm rounded-xl">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                {{-- Nuevo orden de encabezados --}}
                <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                    Nombre
                </th>
                <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                    Descripción
                </th>
                <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                    Fecha
                </th>
                <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                    Precio Alto
                </th>
                <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                    Precio Bajo
                </th>
                <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                    Estado
                </th>
                <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                    Acciones
                </th>
            </tr>
        </thead>
        <tbody id="boletines-table-body" class="bg-white divide-y divide-gray-200">
            @forelse ($boletines as $boletin)
                @include('boletines.partials.boletin_row', ['boletin' => $boletin])
            @empty
                <tr id="no-boletines-row">
                    <td colspan="7" class="px-4 py-4 text-sm text-center text-gray-500 whitespace-nowrap"> {{-- Ajustado colspan a 7 --}}
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
