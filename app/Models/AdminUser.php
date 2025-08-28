<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AdminUser extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'verification_code', 'is_verified', 'is_approved', 'access_level'
    ];

    protected $hidden = [
        'password', 'verification_code',
    ];
}
