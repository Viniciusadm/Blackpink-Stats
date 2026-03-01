<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideosView extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'videos_views';

    protected $fillable = [
        'video_id',
        'views',
        'fixed',
        'created_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'video_id' => 'integer',
        'views' => 'integer',
        'fixed' => 'integer',
        'created_at' => 'datetime',
    ];

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, 'video_id');
    }
}
