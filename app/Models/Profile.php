<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'bio',
        'location',
        'birth_date',
        'profile_picture',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}