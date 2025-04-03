@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Agregar Información</h1>

    <form action="{{ route('informaciones.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('informaciones.index') }}" class="btn btn-secondary">Volver</a>
    </form>
</div>
@endsection
