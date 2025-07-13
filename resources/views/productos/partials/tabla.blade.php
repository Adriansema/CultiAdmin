<div class="overflow-x-auto rounded-2xl">
    <table class="min-w-full text-md text-left">
        <thead class="bg-[var(--color-tabla)]">
            <tr>
                <th class="px-6 py-3 font-bold text-left text-gray-600">
                    <div class="flex items-center justify-between">
                        <span>Creador</span>
                        <div class="flex flex-col ml-2">
                            <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                data-sort-field="creador_name" data-sort-direction="asc">
                                <img src="{{ asset('images/asce.svg') }}"
                                    class="w-5 h-5 relative inset-0 block normal-icon" alt="Icono ascendente">
                                <img src="{{ asset('images/asce-hover.svg') }}"
                                    class="relative inset-0 hidden w-6 h-6 hover-icon" alt="Icono ascendente hover">
                            </button>
                            <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                data-sort-field="creador" data-sort-direction="desc">
                                <img src="{{ asset('images/desce.svg') }}"
                                    class="w-5 h-5 relative inset-0 block normal-icon" alt="Icono descendente">
                                <img src="{{ asset('images/desce-hover.svg') }}"
                                    class="relative inset-0 hidden w-6 h-6 hover-icon" alt="Icono descendente hover">
                            </button>
                        </div>
                    </div>
                </th>
                <th class="px-6 py-3 font-bold text-left text-gray-600">
                    <div class="flex items-center justify-between">
                        <span>Tipo</span>
                        <div class="flex flex-col ml-2">
                            <button type="button" class="sort-icon-btn group whitespace-nowrap" data-sort-field="tipo"
                                data-sort-direction="asc">
                                <img src="{{ asset('images/asce.svg') }}"
                                    class="w-5 h-5 relative inset-0 block normal-icon" alt="Icono ascendente">
                                <img src="{{ asset('images/asce-hover.svg') }}"
                                    class="relative inset-0 hidden w-6 h-6 hover-icon" alt="Icono ascendente hover">
                            </button>
                            <button type="button" class="sort-icon-btn group whitespace-nowrap" data-sort-field="tipo"
                                data-sort-direction="desc">
                                <img src="{{ asset('images/desce.svg') }}"
                                    class="w-5 h-5 relative inset-0 block normal-icon" alt="Icono descendente">
                                <img src="{{ asset('images/desce-hover.svg') }}"
                                    class="relative inset-0 hidden w-6 h-6 hover-icon" alt="Icono descendente hover">
                            </button>
                        </div>
                    </div>
                </th>
                <th class="px-6 py-3 font-bold text-left text-gray-600">
                    <div class="flex items-center justify-between">
                        <span>Fecha</span>
                        <div class="flex flex-col ml-2">
                            <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                data-sort-field="created_at" data-sort-direction="asc">
                                <img src="{{ asset('images/asce.svg') }}"
                                    class="w-5 h-5 relative inset-0 block normal-icon" alt="Icono ascendente">
                                <img src="{{ asset('images/asce-hover.svg') }}"
                                    class="relative inset-0 hidden w-6 h-6 hover-icon" alt="Icono ascendente hover">
                            </button>
                            <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                data-sort-field="created_at" data-sort-direction="desc">
                                <img src="{{ asset('images/desce.svg') }}"
                                    class="w-5 h-5 relative inset-0 block normal-icon" alt="Icono descendente">
                                <img src="{{ asset('images/desce-hover.svg') }}"
                                    class="relative inset-0 hidden w-6 h-6 hover-icon" alt="Icono descendente hover">
                            </button>
                        </div>
                    </div>
                </th>
                <th class="px-6 py-3 font-bold text-left text-gray-600">
                    <div class="flex items-center justify-between">
                        <span>Estado</span>
                        <div class="flex flex-col ml-2">
                            <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                data-sort-field="estado" data-sort-direction="asc">
                                <img src="{{ asset('images/asce.svg') }}"
                                    class="w-5 h-5 relative inset-0 block normal-icon" alt="Icono ascendente">
                                <img src="{{ asset('images/asce-hover.svg') }}"
                                    class="relative inset-0 hidden w-6 h-6 hover-icon" alt="Icono ascendente hover">
                            </button>
                            <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                data-sort-field="estado" data-sort-direction="desc">
                                <img src="{{ asset('images/desce.svg') }}"
                                    class="w-5 h-5 relative inset-0 block normal-icon" alt="Icono descendente">
                                <img src="{{ asset('images/desce-hover.svg') }}"
                                    class="relative inset-0 hidden w-6 h-6 hover-icon" alt="Icono descendente hover">
                            </button>
                        </div>
                    </div>
                </th>
                <th class="px-6 py-3 font-bold text-left text-gray-600">Acciones</th>
            </tr>
        </thead>

        <tbody>
            @if ($productos->total() === 0)
                <tr>
                    {{-- Ajustado el colspan a 9 para cubrir todas las columnas --}}
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                        @if (request()->has('q') && !empty(request()->get('q')))
                            No se encontraron productos que coincidan con
                            <b>{{ htmlspecialchars(request()->get('q')) }}</b>.
                        @else
                            No hay productos registrados.
                        @endif
                    </td>
                </tr>
            @else
                @forelse($productos as $producto)
                    <tr class="bg-white hover:bg-gray-200">
                        <td class="px-6 py-4">
                            {{ $producto->user ? $producto->user->name : 'Desconocido' }}
                        </td>
                        <td class="px-6 py-4">
                            <span>{{ $producto->tipo }}</span>
                        </td>

                        <td class="px-6 py-4">
                            {{ $producto->created_at->locale('es')->translatedFormat('d \d\e F \d\e\l Y h:i a') }}
                            <span class="block text-xs text-gray-500">
                                ({{ $producto->created_at->diffForHumans() }})
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <span
                                class="inline-block px-3 py-2 text-md font-semibold text-white rounded-xl
                                {{ $producto->estado === 'aprobado' ? 'bg-green-600' : ($producto->estado === 'pendiente' ? 'bg-yellow-500' : 'bg-red-600') }}">
                                {{ ucfirst($producto->estado) }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2 ">
                                @can('crear producto')
                                    <a href="{{ route('productos.show', $producto) }}"
                                        class="px-3 py-2 text-center text-white bg-green-600 rounded-xl hover:bg-green-700">
                                        Ver
                                    </a>
                                @endcan

                                @can('editar producto')
                                    <a href="{{ route('productos.edit', $producto) }}"
                                        class="px-3 py-2 text-center text-white bg-yellow-600 rounded-xl hover:bg-yellow-700">
                                        Editar
                                    </a>
                                @endcan

                                @can('eliminar producto')
                                    <button type="button" onclick="mostrarModal('producto', '{{ $producto->id }}')"
                                        class=" px-3 py-2 text-center text-white bg-red-600 rounded-xl hover:bg-red-700">
                                        Eliminar
                                    </button>
                                @endcan

                                @can('validar producto')
                                    <button type="button"
                                        onclick="mostrarModal('validar-producto', '{{ $producto->id }}')"
                                        class="px-3 py-2 text-center text-white bg-blue-600 rounded-xl hover:bg-blue-700">
                                        Validar
                                    </button>
                                @endcan

                                @can('validar producto')
                                    <button type="button"
                                        onclick="mostrarModal('rechazar-producto', '{{ $producto->id }}')"
                                        class="px-3 py-2 text-center text-white bg-orange-600 rounded-xl hover:bg-orange-700">
                                        Rechazar
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay productos registrados.
                        </td>
                    </tr>
                @endforelse
            @endif
        </tbody>
    </table>
</div>
