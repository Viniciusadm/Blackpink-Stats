<?php

namespace Controllers;

use Models\Video;

class SiteController extends Controller
{
    public function home(): void
    {
        $video = new Video();
        $videos = $video->all();

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