<div class="overflow-x-auto rounded-2xl">
    <table class="min-w-full text-md text-left">
        <thead class="bg-[var(--color-tabla)]">
            <tr>
                <th class="px-6 py-3 font-bold text-left text-gray-600">
                    <div class="flex items-center justify-between">
                        <span>Nombre</span>
                        <div class="flex flex-col ml-2">
                            <button type="button" class="sort-icon-btn group whitespace-nowrap" data-sort-field="nombre"
                                data-sort-direction="asc">
                                <img src="{{ asset('images/asce.svg') }}"
                                    class="w-5 h-5 relative inset-0 block normal-icon" alt="Icono ascendente">
                                <img src="{{ asset('images/asce-hover.svg') }}"
                                    class="relative inset-0 hidden w-6 h-6 hover-icon" alt="Icono ascendente hover">
                            </button>
                            <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                data-sort-field="nombre" data-sort-direction="desc">
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
                        <span>Descripción</span>
                        <div class="flex flex-col ml-2">
                            <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                data-sort-field="descripcion" data-sort-direction="asc">
                                <img src="{{ asset('images/asce.svg') }}"
                                    class="w-5 h-5 relative inset-0 block normal-icon" alt="Icono ascendente">
                                <img src="{{ asset('images/asce-hover.svg') }}"
                                    class="relative inset-0 hidden w-6 h-6 hover-icon" alt="Icono ascendente hover">
                            </button>
                            <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                data-sort-field="descripcion" data-sort-direction="desc">
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
                        <span>Precio alto</span>
                        <div class="flex flex-col ml-2">
                            {{-- Botón para ordenar Precio Alto Ascendente --}}
                            <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                data-sort-field="precio_mas_alto" data-sort-direction="asc">
                                <img src="{{ asset('images/asce.svg') }}"
                                    class="w-5 h-5 relative inset-0 block normal-icon" alt="Icono ascendente">
                                <img src="{{ asset('images/asce-hover.svg') }}"
                                    class="relative inset-0 hidden w-6 h-6 hover-icon" alt="Icono ascendente hover">
                            </button>
                            {{-- Botón para ordenar Precio Alto Descendente --}}
                            <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                data-sort-field="precio_mas_alto" data-sort-direction="desc">
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
                        <span>Precio bajo</span>
                        <div class="flex flex-col ml-2">
                            {{-- Botón para ordenar Precio Bajo Ascendente --}}
                            <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                data-sort-field="precio_mas_bajo" data-sort-direction="asc">
                                <img src="{{ asset('images/asce.svg') }}"
                                    class="w-5 h-5 relative inset-0 block normal-icon" alt="Icono ascendente">
                                <img src="{{ asset('images/asce-hover.svg') }}"
                                    class="relative inset-0 hidden w-6 h-6 hover-icon" alt="Icono ascendente hover">
                            </button>
                            {{-- Botón para ordenar Precio Bajo Descendente --}}
                            <button type="button" class="sort-icon-btn group whitespace-nowrap"
                                data-sort-field="precio_mas_bajo" data-sort-direction="desc">
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
        <tbody id="boletines-table-body">
            @forelse ($boletines as $boletin)
                @include('boletines.partials.boletin_row', ['boletin' => $boletin])
            @empty
                <tr id="no-boletines-row">
                    {{-- Asegúrate de ajustar este 'colspan' al número total de columnas de tu tabla --}}
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                        @if (request()->has('q') && !empty(request()->get('q')))
                            No se encontraron boletines que coincidan con
                            <b>{{ htmlspecialchars(request()->get('q')) }}</b>.
                        @else
                            No hay boletines registrados.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
