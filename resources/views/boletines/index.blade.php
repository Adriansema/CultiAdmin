<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Lista de Boletines</h2>
    </x-slot>

    <div class="py-6 mx-auto max-w-7xl">
        <a href="{{ route('boletines.create') }}" class="inline-block px-4 py-2 mb-4 text-white bg-blue-600 rounded hover:bg-blue-700">
            + Nuevo Boletín
        </a>

        <x-success />

        <div class="p-6 bg-white rounded shadow">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2">Asunto</th>
                        <th class="px-4 py-2">Contenido</th>
                        <th class="px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boletines as $boletin)
                        <tr>
                            <td class="px-4 py-2 border">{{ $boletin->asunto }}</td>
                            <td class="px-4 py-2 border">{{ Str::limit($boletin->contenido, 50) }}</td>
                            <td class="px-4 py-2 space-x-2 border">
                                <a href="{{ route('boletines.show', $boletin) }}" class="text-blue-600 hover:underline">Ver</a>
                                <a href="{{ route('boletines.edit', $boletin) }}" class="text-yellow-600 hover:underline">Editar</a>
                                <form action="{{ route('boletines.destroy', $boletin) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar este boletín?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-4 text-center">No hay boletines aún.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
