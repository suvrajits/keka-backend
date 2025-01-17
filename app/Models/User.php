<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'google_id',
        'name',
        'email',
        'avatar',
        'instagram_user_id',
    ];

    /**
     * Define a one-to-many relationship with the Score model.
     */
    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}