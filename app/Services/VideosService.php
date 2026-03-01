<?php

namespace App\Services;

use App\Models\Video;
use App\Models\VideosView;
use DateInterval;
use DateTime;

class VideosService
{
    public function views(Video $video, bool $verify = true): int
    {
        $view = VideosView::where('video_id', $video->id)
            ->orderByDesc('id')
            ->first();

        $createdAt = $view ? $view->created_at->format('Y-m-d H:i:s') : null;
        $tenMinutesAgo = now()->subMinutes(10)->format('Y-m-d H:i:s');

        $views = $view ? (int) $view->views : 0;

        if ((!$view || $createdAt < $tenMinutesAgo) || !$verify) {
            $views = $this->fetchViews($video, !$verify);
        }

        return $views;
    }

    public function byDate(Video $video, string $date): int
    {
        $view = VideosView::where('video_id', $video->id)
            ->whereDate('created_at', $date)
            ->orderByDesc('id')
            ->first();

        return $view->views;
    }

    public function daysTo(Video $video, int $views, ?string $date = null): array
    {
        $next = (int) (ceil($views / 100000000) * 100000000);
        $media = $video->media;

        return [
            'days' => (int) ceil(($next - $views) / $media),
            'next' => $next,
            'media' => $media,
        ];
    }

    public function getMedia(array $views): int
    {
        $current = [];

        foreach ($views as $index => $value) {
            if ($index === 0) {
                continue;
            }

            $current[] = $value - $views[$index - 1];
        }

        return (int) floor(array_sum($current) / count($current));
    }

    public function fetchViews(Video $video, bool $fixed = false): int
    {
        $apiKey = (string) config('youtube.api_key');

        $url1 = 'https://www.googleapis.com/youtube/v3/videos?id=';
        $url2 = '&key=' . $apiKey . '&fields=items(snippet(title,publishedAt),statistics(viewCount,likeCount))&part=snippet,statistics';
        $url = $url1 . $video->key . $url2;

        $json = file_get_contents($url);
        $data = json_decode($json);

        $views = (int) $data->items[0]->statistics->viewCount;

        VideosView::create([
            'video_id' => $video->id,
            'views' => $views,
            'created_at' => now()->format('Y-m-d H:i:s'),
            'fixed' => $fixed ? 1 : 0,
        ]);

        return $views;
    }

    public function getGrowth(Video $video, ?string $date = null): array
    {
        $currentDate = $date ? new DateTime($date) : new DateTime();
        $currentDate->sub(new DateInterval('P6D'));

        $views = [];

        for ($i = 0; $i < 7; $i++) {
            $formattedDate = $currentDate->format('Y-m-d');

            $view = VideosView::where('video_id', $video->id)
                ->where('fixed', 1)
                ->whereDate('created_at', $formattedDate)
                ->orderByDesc('id')
                ->first();

            if (!$view) {
                $view = VideosView::where('video_id', $video->id)
                    ->whereDate('created_at', $formattedDate)
                    ->orderByDesc('id')
                    ->first();
            }

            if ($view) {
                $views[] = (int) $view->views;
            }

            $currentDate->add(new DateInterval('P1D'));
        }

        return $views;
    }

    public function addPlaylist(string $playlist, string $type = 'video'): void
    {
        $apiKey = (string) config('youtube.api_key');

        $url = 'https://www.googleapis.com/youtube/v3/playlistItems?playlistId=' . $playlist
            . '&key=' . $apiKey
            . '&fields=items(contentDetails(videoId))&part=contentDetails&maxResults=50';

        $json = file_get_contents($url);
        $data = json_decode($json);

        foreach ($data->items as $item) {
            $url1 = 'https://www.googleapis.com/youtube/v3/videos?id=';
            $url2 = '&key=' . $apiKey . '&fields=items(snippet(title,publishedAt),statistics(viewCount,likeCount))&part=snippet,statistics';
            $videoUrl = $url1 . $item->contentDetails->videoId . $url2;

            $videoJson = file_get_contents($videoUrl);
            $videoData = json_decode($videoJson);

            $videoId = $item->contentDetails->videoId;

            Video::create([
                'key' => $videoId,
                'type' => $type,
                'published_at' => date('Y-m-d', strtotime($videoData->items[0]->snippet->publishedAt)),
                'title' => $videoData->items[0]->snippet->title,
            ]);

            $result = Video::where('key', $videoId)->orderByDesc('id')->first();
            $views = (int) $videoData->items[0]->statistics->viewCount;

            VideosView::create([
                'video_id' => $result->id,
                'views' => $views,
                'created_at' => now()->format('Y-m-d H:i:s'),
                'fixed' => 0,
            ]);
        }
    }
}
