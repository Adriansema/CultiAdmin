@extends('layouts.app') {{-- Asume que tienes un layout base --}}

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Listado de Noticias</h1>

    <div class="mb-4">
        <a href="{{ route('noticias.noticias.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Crear Nueva Noticia
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($noticias->isEmpty())
        <p class="text-gray-600">No hay noticias registradas.</p>
    @else
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ID Noticia
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Creador
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Autor Acreditado
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Tipo
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Título
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Clase
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Pág.
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($noticias as $noticia)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $noticia->id_noticias }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $noticia->user ? $noticia->user->name : 'Desconocido' }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $noticia->autor ?? 'N/A' }} {{-- ¡MOSTRANDO EL AUTOR! --}}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $noticia->tipo }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ Str::limit($noticia->titulo, 30) ?? 'N/A' }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $noticia->clase ?? 'N/A' }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $noticia->numero_pagina }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <span class="relative inline-block px-3 py-1 font-semibold leading-tight {{ $noticia->estado == 'aprobada' ? 'text-green-900' : ($noticia->estado == 'rechazada' ? 'text-red-900' : 'text-gray-900') }}">
                                <span aria-hidden="true" class="absolute inset-0 opacity-50 rounded-full {{ $noticia->estado == 'aprobada' ? 'bg-green-200' : ($noticia->estado == 'rechazada' ? 'bg-red-200' : 'bg-gray-200') }}"></span>
                                <span class="relative">{{ $noticia->estado }}</span>
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('noticias.noticias.show', $noticia->id_noticias) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                                <a href="{{ route('noticias.noticias.edit', $noticia->id_noticias) }}" class="text-yellow-600 hover:text-yellow-900">Editar</a>
                                <form action="{{ route('noticias.noticias.destroy', $noticia->id_noticias) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta noticia?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection