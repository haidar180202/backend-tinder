<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users/recommended",
     *     summary="Get recommended users",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     )
     * )
     */
    public function getRecommendedUsers(Request $request)
    {
        $user = $request->user();
        $users = User::where('id', '!=', $user->id)->with(['profile', 'pictures'])->paginate(10);

        return response()->json($users);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/like",
     *     summary="Like a user",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function likeUser(Request $request, $id)
    {
        $user = $request->user();
        $likedUser = User::findOrFail($id);

        $like = $user->likes()->create([
            'liked_user_id' => $likedUser->id,
        ]);

        return response()->json($like);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/dislike",
     *     summary="Dislike a user",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function dislikeUser(Request $request, $id)
    {
        $user = $request->user();
        $dislikedUser = User::findOrFail($id);

        $dislike = $user->dislikes()->create([
            'disliked_user_id' => $dislikedUser->id,
        ]);

        return response()->json($dislike);
    }

    /**
     * @OA\Get(
     *     path="/api/users/liked",
     *     summary="Get liked users",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     )
     * )
     */
    public function getLikedUsers(Request $request)
    {
        $user = $request->user();
        $likedUsers = $user->likes()->with(['likedUser.profile', 'likedUser.pictures'])->paginate(10);

        return response()->json($likedUsers);
    }
}