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
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="picture_url", type="string", example="/storage/pictures/1.jpg")
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