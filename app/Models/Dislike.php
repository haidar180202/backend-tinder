<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dislike extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'disliked_user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dislikedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disliked_user_id');
    }
}