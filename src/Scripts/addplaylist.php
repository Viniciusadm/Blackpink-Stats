<?php
namespace Scripts;

require 'vendor/autoload.php';
require 'src/functions.php';

use Dotenv\Dotenv;
use Models\Video;
use Models\VideosView;

function add($playlist, $type = 'video'): void
{
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();

    $url = "https://www.googleapis.com/youtube/v3/playlistItems?playlistId=" . $playlist . "&key=" . $_ENV['YOUTUBE_API_KEY'] . "&fields=items(contentDetails(videoId))&part=contentDetails&maxResults=50";

    $json = file_get_contents($url);
    $data = json_decode($json);

    $video = new Video();
    $videos_views = new VideosView();
    foreach ($data->items as $item) {
        $url1 = 'https://www.googleapis.com/youtube/v3/videos?id=';
        $url2 = '&key=' . $_ENV['YOUTUBE_API_KEY'] . '&fields=items(snippet(title,publishedAt),statistics(viewCount,likeCount))&part=snippet,statistics';

        $url = $url1 . $item->contentDetails->videoId . $url2;

        $json = file_get_contents($url);
        $data = json_decode($json);

        $video_id = $item->contentDetails->videoId;

        $video->create([
            'key' => $video_id,
            'type' => $type,
            'published_at' => date('Y-m-d', strtotime($data->items[0]->snippet->publishedAt)),
            'title' => $data->items[0]->snippet->title,
        ]);

        $result = $video->last("where `key` = '$video_id'");

        $views = $data->items[0]->statistics->viewCount;

        echo "Video: " . $result->id . " - " . $result->title . " - " . $views . " views\n";

        $videos_views->create([
            'video_id' => $result->id,
            'views' => $views,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}

if (isset($argv[1])) {
    $playlist = $argv[1];
    $type = $argv[2] ?? 'video';
    add($playlist, $type);
} else {
    echo "Playlist not found.";
}