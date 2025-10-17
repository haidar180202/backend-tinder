<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

/**
 * @OA\Schema(
 *     schema="Profile",
 *     type="object",
 *     title="Profile",
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="bio", type="string", example="I am a software engineer."),
 *     @OA\Property(property="location", type="string", example="San Francisco, CA"),
 *     @OA\Property(property="birth_date", type="string", format="date", example="1990-01-01"),
 *     @OA\Property(property="age", type="integer", example=34),
 *     @OA\Property(property="profile_picture", type="string", example="/storage/pictures/1.jpg")
 * )
 */
class Profile extends Model
{
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