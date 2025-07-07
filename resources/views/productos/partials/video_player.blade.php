@props(['videoUrl'])

@if ($videoUrl)
    <div class="mb-4">
        @php
            $youtubeVideoId = null;
            // Regex mejorada para capturar IDs de varios formatos de URL de YouTube
            // (watch, youtu.be, embed, etc.)
            $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/';
            preg_match($pattern, $videoUrl, $matches);
            if (isset($matches[1])) {
                $youtubeVideoId = $matches[1];
            }
        @endphp

        @if ($youtubeVideoId)
            {{-- Si es un video de YouTube, usa un iframe responsivo --}}
            <div class="w-full aspect-video rounded-lg shadow-lg overflow-hidden border border-gray-200">
                {{-- <iframe
                    class="w-full h-full"
                    src="https://www.youtube.com/embed/{{ $youtubeVideoId }}?rel=0" {{-- rel=0 para no mostrar videos relacionados -
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe> --}}
                <iframe
                    class="w-full h-full"
                    {{-- CAMBIO: Se usa youtube-nocookie.com para mejorar la compatibilidad --}}
                    src="https://www.youtube-nocookie.com/embed/{{ $youtubeVideoId }}?rel=0" 
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
        @else
            {{-- Si no es de YouTube, asume que es una URL directa y usa la etiqueta <video> --}}
            <video controls class="w-full rounded-lg shadow-lg border border-gray-200">
                <source src="{{ $videoUrl }}"> {{-- El navegador detectará el tipo automáticamente --}}
                Tu navegador no soporta la etiqueta de video.
            </video>
        @endif

        <p class="mt-2 text-sm text-gray-600">
            Si hay problemas con la reproducción, puedes <a href="{{ $videoUrl }}" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:underline">ver el video directamente aquí</a>.
        </p>
    </div>
@else
    {{-- Mensaje si no hay video --}}
    <div class="mb-4 text-gray-600">
        <strong class="font-semibold">Video del producto:</strong> No se ha proporcionado un video para este producto.
    </div>
@endif
