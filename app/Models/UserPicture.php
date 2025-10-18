<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="UserPicture",
 *     type="object",
 *     title="User Picture",
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="user_id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="picture_url", type="string", format="url", example="http://localhost/storage/user_pictures/picture.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true)
 * )
 */
class UserPicture extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'picture_url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}