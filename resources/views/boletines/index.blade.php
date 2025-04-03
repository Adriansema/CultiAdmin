@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Lista de Boletines</h1>

    <a href="{{ route('boletines.create') }}" class="mb-3 btn btn-primary">Agregar Boletín</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Asunto</th>
                <th>Contenido</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($boletines as $boletin)
                <tr>
                    <td>{{ $boletin->id }}</td>
                    <td>{{ $boletin->asunto }}</td>
                    <td>{{ $boletin->contenido }}</td>
                    <td>
                        <a href="{{ route('boletines.edit', $boletin->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('boletines.destroy', $boletin->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
