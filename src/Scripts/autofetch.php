<?php
namespace Scripts;

require 'vendor/autoload.php';

use Helpers\VideosHelper;
use Models\Video;

function fetch(): void
{
    $video = new Video();
    $videos = $video->all();

    foreach ($videos as $video) {
        $result = VideosHelper::views($video);
        echo $video->title . ': ' . $result . PHP_EOL;
    }
}

fetch();