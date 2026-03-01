<?php

use App\Models\Video;
use App\Models\VideosView;
use App\Services\VideosService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('videos:autofetch', function () {
    $videosService = app(VideosService::class);
    $videos = Video::all();

    foreach ($videos as $video) {
        $result = $videosService->views($video, false);
        $this->info($video->title . ': ' . $result);
    }
})->purpose('Atualiza snapshots de visualizações via API do YouTube');

Artisan::command('videos:autodelete {date?}', function (?string $date = null) {
    $date = $date ?? now()->format('Y-m-d');

    if (!strtotime($date)) {
        $this->error('Formato de data inválido. Use o formato YYYY-MM-DD.');
        return;
    }

    $videoIds = Video::query()->pluck('id');
    $idsToKeep = [];

    foreach ($videoIds as $videoId) {
        $view = VideosView::query()
            ->where('video_id', $videoId)
            ->whereDate('created_at', $date)
            ->orderByDesc('id')
            ->first();

        if ($view) {
            $idsToKeep[] = $view->id;
        }
    }

    if (!empty($idsToKeep)) {
        $deleted = VideosView::query()
            ->whereDate('created_at', $date)
            ->whereNotIn('id', $idsToKeep)
            ->where('fixed', '!=', 1)
            ->delete();

        $this->info($deleted > 0 ? 'Rows deleted' : 'No rows deleted');
        return;
    }

    $this->info('No matching records found for the given date.');
})->purpose('Remove snapshots duplicados do dia, preservando o último por vídeo');

Artisan::command('videos:getmedia', function () {
    $videosService = app(VideosService::class);

    $videos = Video::query()->select(['id', 'firsts_views', 'published_at'])->get();

    foreach ($videos as $video) {
        $growth = $videosService->getGrowth($video);
        $media = $videosService->getMedia($growth);

        Video::query()->where('id', $video->id)->update(['media' => $media]);

        $this->info("Video {$video->id} updated with media {$media}");
    }
})->purpose('Recalcula a média diária de crescimento de visualizações');

Artisan::command('videos:addplaylist {playlist} {type=video}', function (string $playlist, string $type = 'video') {
    $videosService = app(VideosService::class);
    $videosService->addPlaylist($playlist, $type);
    $this->info('Playlist processada com sucesso.');
})->purpose('Adiciona vídeos de uma playlist do YouTube ao banco');

Schedule::command('videos:autofetch')->daily();
Schedule::command('videos:autodelete')->daily();
Schedule::command('videos:getmedia')->daily();
