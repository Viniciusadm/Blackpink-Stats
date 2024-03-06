<?php

namespace Helpers;

use Dotenv\Dotenv;

class VideosHelper
{
    public static function views(string $key): int
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $url1 = 'https://www.googleapis.com/youtube/v3/videos?id=';
        $url2 = '&key=' . $_ENV['YOUTUBE_API_KEY'] . '&fields=items(snippet(title,publishedAt),statistics(viewCount,likeCount))&part=snippet,statistics';

        $url = $url1 . $key . $url2;

        $json = file_get_contents($url);
        $data = json_decode($json);

        return $data->items[0]->statistics->viewCount;
    }
}