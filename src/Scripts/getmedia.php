<?php
namespace Scripts;

require 'vendor/autoload.php';
require 'src/functions.php';

use Exception;
use Helpers\VideosHelper;
use Models\Video;

/**
 * @throws Exception
 */
function media(): void
{
    $video = new Video();
    $videos = $video->all('id, firsts_views, published_at');

    foreach ($videos as $vid) {
        $growth = VideosHelper::getGrowth($vid);
        $video->update($vid->id, ['media' => VideosHelper::getMedia($growth)]);
    }
}

try {
    media();
} catch (Exception $e) {
    echo $e->getMessage();
}