<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',      // Foreign key linking to the users table
        'score',        // Score value
        'updated_at', // Time of score submission
        'track_id',     // Unique identifier for this score
    ];

    /**
     * Define an inverse one-to-many relationship with the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
