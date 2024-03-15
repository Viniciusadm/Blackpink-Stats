<?php

namespace Controllers;

use Classes\Data;
use Exception;
use Helpers\VideosHelper;
use Models\Video;

class SiteController extends Controller
{
    /**
     * @throws Exception
     */
    public function home(): void
    {
        $video = new Video();
        $result = $video->all();

        $date = $_GET['date'];
        $admin = isset($_GET['admin']);

        $videos = [];
        foreach ($result as $video) {
            if ($date) {
                $views = VideosHelper::byDate($video, $date);
            } else {
                $views = VideosHelper::views($video);
            }

            $daysTo = VideosHelper::daysTo($video, $views, $date);

            $video->views = $views;
            $video->days_to = $daysTo['days'];
            $video->next = $daysTo['next'];
            $video->media = $daysTo['media'];

            $videos[] = new Data($video);
        }

        usort($videos, function ($a, $b) {
            return $b->views <=> $a->views;
        });

        $this->view('home.php', [
            'videos' => $videos,
            'admin' => $admin,
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