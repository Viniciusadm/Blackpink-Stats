<?php

namespace Helpers;

use Dotenv\Dotenv;
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
     */
    public static function daysTo(mixed $video, int $views, $date = null): array
    {
        $decay = $video->decay_rate;
        $next = ceil($views / 100000000) * 100000000;
        $media = self::getMedia($video, $views, $date);
        $total = $next - $views;

        $sum = $media;
        $days = 0;

        while ($total > 0) {
            $days++;
            $total -= $sum;
            $sum -= $decay;
        }

        return [
            'days' => $days,
            'next' => $next,
            'media' => $media,
        ];
    }

    /**
     * @param mixed $video
     * @param int $views
     * @param null|string $date
     * @return int
     */
    public static function getMedia(mixed $video, int $views, string $date = null): int
    {
        $published_at = date('Y-m-d', strtotime($video->published_at));
        $today = $date ?: date('Y-m-d');
        $days = (strtotime($today) - strtotime($published_at)) / (60 * 60 * 24);

        return ($views - $video->firsts_views) / ($days - 1);
    }

    /**
     * @param $video
     * @return int
     */
    public static function fetchViews($video, $fixed = false): int
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
}