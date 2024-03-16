<?php

namespace Controllers;

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
        $videos = $video->all();

        $date = $_GET['date'];
        $admin = isset($_GET['admin']);

        foreach ($videos as $video) {
            if ($date) {
                $views = VideosHelper::byDate($video, $date);
            } else {
                $views = VideosHelper::views($video);
            }

            $daysTo = VideosHelper::daysTo($video, $views, $date);

            $video->set('views', $views);
            $video->set('days_to', $daysTo['days']);
            $video->set('next', $daysTo['next']);
            $video->set('media', $daysTo['media']);
        }

        $videos->sort('views', 'desc');

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

    public function details($slug): void
    {
        $video = new Video();
        $result = $video->first("where slug = '$slug'");

        if ($result) {
            $this->view('details.php', [
                'video' => $result,
            ]);
        } else {
            $this->notFound();
        }
    }

    public function notFound(): void
    {
        $this->view('notFound.php');
    }
}