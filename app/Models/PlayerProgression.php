<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerProgression extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id', 'level', 'current_xp', 'tracks_unlocked', 'skills_acquired'
    ];

    protected $casts = [
        'tracks_unlocked' => 'array',
        'skills_acquired' => 'array',
    ];

    public function player()
    {
        return $this->belongsTo(User::class);
    }
}
