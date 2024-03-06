<?php

namespace Controllers;

use Helpers\VideosHelper;
use Models\Video;
use Models\VideosView;

class SiteController extends Controller
{
    public function home(): void
    {
        $video = new Video();
        $videos = $video->all();
        $videos_views = new VideosView();

        foreach ($videos as $video) {
            $views = $this->getViews($videos_views, $video);
            $daysTo = $this->daysTo($views, $video);

            $video->views = $views;
            $video->formatted_views = number_format($views, 0, ',', '.');
            $video->days_to = $daysTo['days'];
            $video->next = number_format($daysTo['next'], 0, ',', '.');
        }

        usort($videos, function ($a, $b) {
            return $b->views <=> $a->views;
        });

        $this->view('home.php', [
            'videos' => $videos,
            'classes' => [
                1 => 'primeiro',
                2 => 'segundo',
                3 => 'terceiro'
            ]
        ]);
    }

    public function notFound(): void
    {
        $this->view('notFound.php');
    }

    /**
     * @param VideosView $videos_views
     * @param mixed $video
     * @return int
     */
    public function getViews(VideosView $videos_views, mixed $video): int
    {
        $view = $videos_views->last("WHERE video_id = $video->id");
        $crated_at = date('Y-m-d h:i:s', strtotime($view->created_at));
        $ten_minutes_ago = date('Y-m-d h:i:s', strtotime('-10 minutes'));

        $views = $view->views;

        if (!$view || $crated_at < $ten_minutes_ago) {
            $views = VideosHelper::views($video->key);
            $videos_views->create([
                'video_id' => $video->id,
                'views' => $views,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return $views;
    }

    /**
     * @param int $views
     * @param mixed $video
     * @return array
     */
    public function daysTo(int $views, mixed $video): array
    {
        $published_at = date('Y-m-d', strtotime($video->published_at));
        $today = date('Y-m-d');
        $days = (strtotime($today) - strtotime($published_at)) / (60 * 60 * 24);

        $next = ceil($views / 100000000) * 100000000;

        $media = $views / $days;

        return [
            'days' => round(($next - $views) / $media),
            'next' => $next
        ];
    }
}