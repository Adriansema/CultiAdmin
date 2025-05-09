<table class="min-w-full overflow-hidden bg-white rounded-md shadow">
    <thead>
        <tr>
            <th class="px-4 py-2 text-left">Asunto</th>
            <th class="px-4 py-2 text-left">Estado</th>
            <th class="px-4 py-2 text-left">Fecha</th>
        </tr>
    </thead>
    <tbody>
        @forelse($boletines as $boletin)
            <tr>
                <td class="px-4 py-2">{{ $boletin->asunto }}</td>
                <td class="px-4 py-2 capitalize">{{ $boletin->estado }}</td>
                <td class="px-4 py-2">{{ $boletin->created_at->format('d/m/Y') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="py-4 text-center text-gray-500">No hay boletines encontrados.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-4">
    {{ $boletines->links() }}
</div>
