<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Profile",
 *     type="object",
 *     title="Profile",
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="user_id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="bio", type="string", example="I am a software engineer."),
 *     @OA\Property(property="location", type="string", example="New York, USA"),
 *     @OA\Property(property="birth_date", type="string", format="date", example="1990-01-01"),
 *     @OA\Property(property="profile_picture", type="string", format="url", example="http://localhost/storage/profile_pictures/picture.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true)
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