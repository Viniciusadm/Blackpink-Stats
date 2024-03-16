<?php

namespace Models;

require_once 'Model.php';

class VideosView extends Model
{
    public int $id;
    public int $video_id;
    public int $views;
    public int $fixed;
    public string $created_at;

    public string $table = 'videos_views';
}