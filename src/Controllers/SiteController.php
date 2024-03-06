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

        foreach ($videos as $video) {
            $views = VideosHelper::views($video);
            $daysTo = VideosHelper::daysTo($video, $views);

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
}