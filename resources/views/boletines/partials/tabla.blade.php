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
                {{-- Esta fila es para cuando NO hay boletines (ni cargando ni nada) --}}
                <tr id="no-boletines-message-row" style="display: none;"> {{-- Inicialmente oculto --}}
                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No hay boletines para mostrar.</td>
                </tr>
            @endforelse
            {{-- Fila para el spinner de carga (inicialmente oculta) --}}
            <tr id="loading-spinner-row" style="display: none;">
                <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $boletines->links() }}
</div>