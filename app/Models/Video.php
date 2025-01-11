<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'instagram_video_id',
        'caption',
        'video_path',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
