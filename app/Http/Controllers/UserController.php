<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getRecommendedUsers(Request $request)
    {
        $user = $request->user();
        $users = User::where('id', '!=', $user->id)->with(['profile', 'pictures'])->paginate(10);

        return response()->json($users);
    }

    public function likeUser(Request $request, $id)
    {
        $user = $request->user();
        $likedUser = User::findOrFail($id);

        $like = $user->likes()->create([
            'liked_user_id' => $likedUser->id,
        ]);

        return response()->json($like);
    }

    public function dislikeUser(Request $request, $id)
    {
        $user = $request->user();
        $dislikedUser = User::findOrFail($id);

        $dislike = $user->dislikes()->create([
            'disliked_user_id' => $dislikedUser->id,
        ]);

        return response()->json($dislike);
    }

    public function getLikedUsers(Request $request)
    {
        $user = $request->user();
        $likedUsers = $user->likes()->with(['likedUser.profile', 'likedUser.pictures'])->paginate(10);

        return response()->json($likedUsers);
    }
}