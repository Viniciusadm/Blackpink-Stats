<?php
namespace Scripts;

require 'vendor/autoload.php';

use Models\Video;
use Models\VideosView;

function delete($date): void
{
    $video = new Video();
    $videos = $video->all('id');

    $videos_views = new VideosView();

    $ids = [];
    foreach ($videos as $video) {
        $view = $videos_views->last("WHERE video_id = $video->id AND DATE(created_at) = '$date'");
        if ($view) {
            $ids[] = $view->id;
        }
    }

    if (!empty($ids)) {
        $result = $videos_views->deleteAll("WHERE DATE(created_at) = '$date' AND id NOT IN (" . implode(',', $ids) . ") AND fixed != 1");
        echo $result ? 'Rows deleted' : 'No rows deleted';
    } else {
        echo 'No matching records found for the given date.';
    }
}

if (isset($argv[1])) {
    $date = $argv[1];
    if (strtotime($date)) {
        delete($date);
    } else {
        echo "Formato de data inválido. Use o formato YYYY-MM-DD.";
    }
} else {
    $date = date('Y-m-d');
    delete($date);
}