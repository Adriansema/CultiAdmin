@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Lista de Comentarios</h1>

    <a href="{{ route('comentarios.create') }}" class="mb-3 btn btn-primary">Agregar Comentario</a>

    <form method="GET" action="{{ route('comentarios.filtrar', date('m')) }}" class="mb-3">
        <label for="mes">Filtrar por Mes:</label>
        <select name="mes" id="mes" class="form-select" onchange="this.form.submit()">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                    {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                </option>
            @endfor
        </select>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Contenido</th>
                <th>Fecha de Creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($comentarios as $comentario)
                <tr>
                    <td>{{ $comentario->id }}</td>
                    <td>{{ $comentario->contenido }}</td>
                    <td>{{ $comentario->created_at }}</td>
                    <td>
                        <form action="{{ route('comentarios.destroy', $comentario->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este comentario?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
