@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Lista de Información</h1>

    <a href="{{ route('informaciones.create') }}" class="mb-3 btn btn-primary">Agregar Información</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($informaciones as $info)
                <tr>
                    <td>{{ $info->id }}</td>
                    <td>{{ $info->titulo }}</td>
                    <td>{{ $info->descripcion }}</td>
                    <td>
                        <a href="{{ route('informaciones.edit', $info->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('informaciones.destroy', $info->id) }}" method="POST" style="display:inline;">
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
