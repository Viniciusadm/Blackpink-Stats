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

    /**
     * @var Video[] $videos
     */
    $videos = $video->all('id, firsts_views, published_at');

    foreach ($videos as $vid) {
        $growth = VideosHelper::getGrowth($vid);
        $media = VideosHelper::getMedia($growth);
        $video->find($vid->id)->update(['media' => $media]);
        echo "Video $vid->id updated with media $media\n";
    }
}

try {
    media();
} catch (Exception $e) {
    echo $e->getMessage();
}