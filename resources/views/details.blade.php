@include('layouts.header')

<main class="container principal p-3">
    <h1 class="h4">{{ $video->title }}</h1>
    <p class="mb-1"><strong>ID:</strong> {{ $video->id }}</p>
    <p class="mb-1"><strong>Slug:</strong> {{ $video->slug }}</p>
    <p class="mb-1"><strong>Publicado em:</strong> {{ $video->formatDate('published_at') }}</p>
    <p class="mb-3"><strong>Tipo:</strong> {{ $video->type }}</p>
    <a href="https://www.youtube.com/watch?v={{ $video->key }}" target="_blank">Abrir no YouTube</a>
</main>

@include('layouts.footer')
