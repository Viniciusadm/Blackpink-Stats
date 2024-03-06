<?php

namespace Controllers;

use Models\Video;

class SiteController extends Controller
{
    public function home(): void
    {
        $videos = new Video();
        $videos = $videos->all();

        $this->view('home.php', ['videos' => $videos]);
    }

    public function notFound(): void
    {
        $this->view('notFound.php');
    }
}