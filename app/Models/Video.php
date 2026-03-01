<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Video extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'videos';

    protected $fillable = [
        'key',
        'title',
        'slug',
        'type',
        'firsts_views',
        'decay_rate',
        'media',
        'published_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'firsts_views' => 'integer',
        'decay_rate' => 'integer',
        'media' => 'integer',
        'published_at' => 'date',
    ];

    public function views(): HasMany
    {
        return $this->hasMany(VideosView::class, 'video_id');
    }

    public function formatNumber(string $key): string
    {
        return number_format($this->{$key}, 0, ',', '.');
    }

    public function formatDate(string $key): string
    {
        return date('d/m/Y', strtotime($this->{$key}));
    }

    public function set(string $key, mixed $value): void
    {
        $this->setAttribute($key, $value);
    }
}
