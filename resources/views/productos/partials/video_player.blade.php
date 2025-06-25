{{--
    Este parcial se encarga de mostrar un reproductor de video.
    Puede manejar URLs de YouTube o URLs directas a archivos de video (asume MP4 por defecto).

    @param string $videoUrl La URL del video a reproducir.
--}}
@props(['videoUrl'])

@if ($videoUrl)
    <div class="mb-4">
        {{--
            La lógica para detectar y parsear URLs de YouTube se mantiene aquí para modularidad.
            Considera mover esto a un helper o un accesorio en el modelo si se usa en muchos lugares.
        --}}
        @php
            $isYouTube = false;
            $youtubeVideoId = null;

            if (str_contains($videoUrl, 'youtube.com/watch')) {
                parse_str( parse_url( $videoUrl, PHP_URL_QUERY ), $params );
                if (isset($params['v'])) {
                    $youtubeVideoId = $params['v'];
                    $isYouTube = true;
                }
            } elseif (str_contains($videoUrl, 'youtu.be/')) {
                $youtubeVideoId = substr(parse_url($videoUrl, PHP_URL_PATH), 1);
                $isYouTube = true;
            }
        @endphp

        @if ($isYouTube && $youtubeVideoId)
            {{-- Si es un video de YouTube, usa un iframe para incrustar --}}
            <iframe
                class="w-full aspect-video rounded-lg shadow-lg border border-gray-200"
                src="https://www.youtube.com/embed/{{ $youtubeVideoId }}"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
            </iframe>
        @else
            {{-- Si no es de YouTube, asume que es una URL directa a un archivo de video y usa la etiqueta <video> --}}
            <video controls class="w-full max-w-lg rounded-lg shadow-lg border border-gray-200" style="max-height: 360px;">
                <source src="{{ $videoUrl }}" type="video/mp4"> {{-- Asume MP4 por defecto --}}
                Tu navegador no soporta la etiqueta de video.
            </video>
        @endif

        <p class="mt-2 text-sm text-gray-600">
            Si hay problemas con la reproducción, puedes <a href="{{ $videoUrl }}" target="_blank" class="text-blue-500 hover:underline">ver el video directamente aquí</a>.
        </p>
    </div>
@else
    {{-- Mensaje si no hay video --}}
    <div class="mb-4 text-gray-600">
        <strong class="font-semibold">Video del Producto:</strong> No se ha proporcionado un video para este producto.
    </div>
@endif
