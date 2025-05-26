<!-- Contenedor/tabla del listado de boletines -->
<div class="overflow-x-auto bg-white rounded shadow">
    <table class="min-w-full text-sm table-auto">
        <thead class="text-gray-700 bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left whitespace-nowrap">ID</th>
                <th class="px-4 py-2 text-left whitespace-nowrap">Contenido</th>
                <th class="px-4 py-2 text-left whitespace-nowrap">Fechas</th>
                <th class="px-4 py-2 text-left whitespace-nowrap">Estados</th>
                <th class="px-4 py-2 text-left whitespace-nowrap">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($boletines as $boletin)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2 align-top whitespace-nowrap">{{ $loop->iteration }}</td>

                    <td class="max-w-xs px-4 py-2 text-gray-600 break-words whitespace-normal align-top">
                        {{ Str::limit($boletin->contenido, 60) }}
                    </td>

                    <td class="max-w-xs px-4 py-2 text-gray-600 break-words whitespace-normal align-top">
                        {{ $boletin->created_at->locale('es')->translatedFormat('d \d\e F \d\e\l Y h:i a') }}
                        <span class="block text-xs text-gray-500">
                            ({{ $boletin->created_at->diffForHumans() }})
                        </span>
                    </td>

                    <td class="px-4 py-2">
                        <span
                            class="inline-block px-3 py-1 text-sm font-semibold text-white rounded
                            {{ $boletin->estado === 'aprobado' ? 'bg-green-600' : ($boletin->estado === 'pendiente' ? 'bg-yellow-500' : 'bg-red-600') }}">
                            {{ ucfirst($boletin->estado) }}
                        </span>
                    </td>

                    <td class="flex flex-col px-4 py-2 space-y-1 align-top md:space-y-0 md:space-x-2 md:flex-row">
                        <button type="button" onclick="mostrarModal('ver', '{{ $boletin->id }}')" 
                            class="px-3 py-1 text-sm text-center text-white bg-green-600 rounded hover:bg-green-700">
                            Ver
                        </button>
                        @include('boletines.partials.modal-views')

                        <button type="button" onclick="mostrarModal('editar', '{{ $boletin->id }}')" 
                            class="px-3 py-1 text-sm text-center text-white bg-yellow-600 rounded hover:bg-yellow-700">
                            Editar
                        </button>
                        @include('boletines.partials.modal-edit')
            
                        <!-- Botón que abre el modal -->
                        <button type="button" onclick="mostrarModal('boletin', '{{ $boletin->id }}')"
                            class="w-20 px-1 py-1 text-sm text-center text-white bg-red-600 rounded hover:bg-red-700">
                            Eliminar
                        </button>
                        @include('boletines.partials.modal-delete')
                    </td>
                </tr>
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
