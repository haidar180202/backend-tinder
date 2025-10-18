<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
     *     path="/api/users/{id}/action",
     *     summary="Like or dislike a user",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="name", in="header", required=true, @OA\Schema(type="string", enum={"like", "dislike"})),
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function userAction(Request $request, $id)
    {
        $user = $request->user();
        $action = $request->header('name');

        if ($user->id == $id) {
            return response()->json(['message' => 'You cannot perform this action on yourself.'], 422);
        }

        if (!User::where('id', $id)->exists()) {
            return response()->json(['message' => 'The user you are trying to interact with does not exist.'], 404);
        }

        if ($action === 'like') {
            $user->likes()->firstOrCreate(['liked_user_id' => $id]);
        } elseif ($action === 'dislike') {
            $user->dislikes()->firstOrCreate(['disliked_user_id' => $id]);
        } else {
            return response()->json(['message' => 'Invalid action specified.'], 400);
        }

        return response()->json(['message' => 'Action recorded successfully.'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/users/mycategories",
     *     summary="Get user data by category",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="name", in="header", required=true, @OA\Schema(type="string", enum={"liked", "disliked"})),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     )
     * )
     */
    public function getMyDataByCategory(Request $request)
    {
        $user = $request->user();
        $category = $request->header('name');

        if ($category === 'liked') {
            $data = $user->likes()->with(['likedUser.profile', 'likedUser.pictures'])->paginate(10);
        } elseif ($category === 'disliked') {
            $data = $user->dislikes()->with(['dislikedUser.profile', 'dislikedUser.pictures'])->paginate(10);
        } else {
            return response()->json(['message' => 'Invalid category'], 400);
        }

        return response()->json($data);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'bio' => 'sometimes|string',
            'location' => 'sometimes|string|max:255',
            'birth_date' => 'sometimes|date',
        ]);

        $profile = $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $validatedData
        );

        return response()->json($profile);
    }

    public function uploadProfilePicture(Request $request)
    {
        $request->validate([
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = $request->user();
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);

        if ($request->hasFile('picture')) {
            $path = $request->file('picture')->store('public/profile_pictures');
            $profile->profile_picture = Storage::url($path);
            $profile->save();
        }

        return response()->json($profile);
    }
}