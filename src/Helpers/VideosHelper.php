<?php

namespace Helpers;

use DateInterval;
use DateTime;
use Dotenv\Dotenv;
use Exception;
use Models\VideosView;

class VideosHelper
{
    public static function views($video, $verify = true): int
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $videos_views = new VideosView();

        $view = $videos_views->last("WHERE video_id = $video->id");
        $created_at = date('Y-m-d H:i:s', strtotime($view->created_at));
        $ten_minutes_ago = date('Y-m-d H:i:s', strtotime('-10 minutes'));

        $views = $view->views;

        if ((!$view || $created_at < $ten_minutes_ago) || !$verify) {
            $views = self::fetchViews($video, !$verify);
        }

        return $views;
    }

    public static function byDate($video, $date): int
    {
        $videos_views = new VideosView();

        $view = $videos_views->last("WHERE video_id = $video->id AND DATE(created_at) = '$date'");
        return $view->views;
    }

    /**
     * @param mixed $video
     * @param int $views
     * @param null $date
     * @return array
     * @throws Exception
     */
    public static function daysTo(mixed $video, int $views, $date = null): array
    {
        $next = ceil($views / 50000000) * 50000000;
        $media = $video->media;

        return [
            'days' => ceil(($next - $views) / $media),
            'next' => $next,
            'media' => $media,
        ];
    }

    /**
     * @param array $views
     * @return int
     */
    public static function getMedia(array $views): int
    {
        $atual = [];
        foreach ($views as $index => $m) {
            if ($index === 0) {
                continue;
            }

            $atual[] = $m - $views[$index - 1];
        }

        return floor(array_sum($atual) / count($atual));
    }

    /**
     * @param $video
     * @param bool $fixed
     * @return int
     */
    public static function fetchViews($video, bool $fixed = false): int
    {
        $videos_views = new VideosView();

        $url1 = 'https://www.googleapis.com/youtube/v3/videos?id=';
        $url2 = '&key=' . $_ENV['YOUTUBE_API_KEY'] . '&fields=items(snippet(title,publishedAt),statistics(viewCount,likeCount))&part=snippet,statistics';

        $url = $url1 . $video->key . $url2;

        $json = file_get_contents($url);
        $data = json_decode($json);

        $views = $data->items[0]->statistics->viewCount;

        $videos_views->create([
            'video_id' => $video->id,
            'views' => $views,
            'created_at' => date('Y-m-d H:i:s'),
            'fixed' => $fixed ? '1' : '0',
        ]);

        return $views;
    }

    /**
     * @param string|null $date
     * @param mixed $video
     * @return array
     * @throws Exception
     */
    public static function getGrowth(mixed $video, string $date = null): array
    {
        $dataAtual = $date ? new DateTime($date) : new DateTime();
        $videos_views = new VideosView();

        $dataAtual->sub(new DateInterval('P6D'));

        $views = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $dataAtual->format('Y-m-d');
            $view = $videos_views->last("WHERE video_id = $video->id AND fixed = 1 AND DATE(created_at) = '$date'");

            if (!$view) {
                $view = $videos_views->last("WHERE video_id = $video->id AND DATE(created_at) = '$date'");
            }

            if ($view) {
                $views[] = $view->views;
            }

            $dataAtual->add(new DateInterval('P1D'));
        }

        return $views;
    }
}