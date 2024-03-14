<?php
namespace Scripts;

require 'vendor/autoload.php';
require 'src/functions.php';

use DateInterval;
use DateTime;
use Exception;
use Helpers\VideosHelper;
use Models\Video;
use Models\VideosView;

/**
 * @throws Exception
 */
function media(): void
{
    $video = new Video();
    $videos = $video->all('id, firsts_views, published_at');

    $dataAtual = new DateTime();
    $videos_views = new VideosView();

    $views = [];
    foreach ($videos as $vid) {
        $dataAtual->sub(new DateInterval('P7D'));

        for ($i = 0; $i < 7; $i++) {
            $date = $dataAtual->format('Y-m-d');
            $view = $videos_views->last("WHERE video_id = $vid->id AND DATE(created_at) = '$date'");

            if ($view) {
                $views[$vid->id][] = [
                    'date' => $date,
                    'views' => $view->views
                ];
            }

            $dataAtual->add(new DateInterval('P1D'));
        }
    }

    $medias = [];
    foreach ($views as $video_id => $dates) {
        $obj = getObjectById($videos, $video_id);

        foreach ($dates as $date) {
            $media = VideosHelper::getMedia($obj, $date['date']);
            $medias[$video_id][] = $media;
        }
    }

    foreach ($medias as $video_id => $media) {
        $atual = [];
        foreach ($media as $index => $m) {
            if ($index === 0) {
                continue;
            }

            $atual[] = $media[$index - 1] - $m;
        }

        $sum = floor(array_sum($atual) / count($atual));
        $video->update($video_id, ['decay_rate' => $sum]);
    }
}

media();