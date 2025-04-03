@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Boletín</h1>

    <form action="{{ route('boletines.update', $boletin->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="asunto" class="form-label">Asunto</label>
            <input type="text" name="asunto" class="form-control" value="{{ $boletin->asunto }}" required>
        </div>
        <div class="mb-3">
            <label for="contenido" class="form-label">Contenido</label>
            <textarea name="contenido" class="form-control" rows="3" required>{{ $boletin->contenido }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="{{ route('boletines.index') }}" class="btn btn-secondary">Volver</a>
    </form>
</div>
@endsection
