<?php

namespace Models;

require_once 'Model.php';

class Video extends Model
{
    public int $id;
    public string $key;
    public string $title;
    public string|null $slug = null;
    const type = ['video', 'music'];
    public string $type;
    public int|null $firsts_views = null;
    public int|null $decay_rate = null;
    public int|null $media = null;
    public string|null $published_at = null;

    public string $table = 'videos';
}