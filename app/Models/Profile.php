<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

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

    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn () => Carbon::parse($this->attributes['birth_date'])->age,
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}