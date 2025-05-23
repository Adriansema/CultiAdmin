<!DOCTYPE html>
<html>
<head>
    <title>Nueva Revisión Pendiente</title>
</head>
<body>
    <h1>¡Hola Operador!</h1>
    <p>Tienes una nueva {{ strtolower($itemTipo) }} pendiente de revisión.</p>

    <p><strong>Detalles de la {{ strtolower($itemTipo) }}:</strong></p>
    <ul>
        <li><strong>ID:</strong> {{ $item->id }}</li>
        @if ($itemTipo === 'Noticia')
            <li><strong>Tipo:</strong> {{ ucfirst($item->tipo) }}</li>
            {{-- Puedes añadir más detalles de la noticia aquí si son relevantes para el operador --}}
            <li><strong>Contenido (extracto):</strong> {{ Str::limit($item->detalles_json ? json_decode($item->detalles_json, true)['historia'] ?? 'N/A' : 'N/A', 100) }}</li>
            <li><strong>Creado por:</strong> {{ $item->user->name ?? 'Usuario Desconocido' }}</li>
            <li><strong>Fecha de Creación:</strong> {{ $item->created_at->format('d/m/Y H:i') }}</li>
        @elseif ($itemTipo === 'Boletín')
            <li><strong>Contenido (extracto):</strong> {{ Str::limit($item->contenido, 100) }}</li>
            {{-- Puedes añadir más detalles del boletín aquí --}}
            <li><strong>Creado por:</strong> {{ $item->user->name ?? 'Usuario Desconocido' }}</li>
            <li><strong>Fecha de Creación:</strong> {{ $item->created_at->format('d/m/Y H:i') }}</li>
        @endif
    </ul>

    <p>Por favor, revisa la {{ strtolower($itemTipo) }} y cámbiale el estado según corresponda.</p>
    <p>
        <a href="{{ route('operador.pendientes') }}" style="background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
            Ir a Elementos Pendientes
        </a>
    </p>

    <p>Gracias.</p>
</body>
</html>