<table class="min-w-full bg-white shadow rounded-md overflow-hidden">
    <thead>
        <tr>
            <th class="px-4 py-2 text-left">Nombre</th>
            <th class="px-4 py-2 text-left">Estado</th>
            <th class="px-4 py-2 text-left">Fecha</th>
        </tr>
    </thead>
    <tbody>
        @forelse($productos as $producto)
            <tr>
                <td class="px-4 py-2">{{ $producto->nombre }}</td>
                <td class="px-4 py-2 capitalize">{{ $producto->estado }}</td>
                <td class="px-4 py-2">{{ $producto->created_at->format('d/m/Y') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center py-4 text-gray-500">No hay productos encontrados.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-4">
    {{ $productos->links() }}
</div>
