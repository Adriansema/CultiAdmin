@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Agregar Comentario</h1>

    <form action="{{ route('comentarios.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="contenido" class="form-label">Comentario</label>
            <textarea name="contenido" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('comentarios.index') }}" class="btn btn-secondary">Volver</a>
    </form>
</div>
@endsection
