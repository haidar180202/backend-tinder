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
     * @OA\Get(
     *     path="/api/users/{id}/action",
     *     summary="Like or dislike a user",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="action", in="query", required=true, @OA\Schema(type="string", enum={"like", "dislike"})),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid action specified"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=422, description="Cannot perform action on yourself")
     * )
     */
    public function userAction(Request $request, $id)
    {
        $user = $request->user();
        $action = $request->query('action');

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

    /**
     * @OA\Post(
     *     path="/api/profile",
     *     summary="Update user profile",
     *     tags={"Profile"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="bio", type="string", example="I am a software engineer."),
     *             @OA\Property(property="location", type="string", example="New York, USA"),
     *             @OA\Property(property="birth_date", type="string", format="date", example="1990-01-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Profile")
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/profile/picture",
     *     summary="Upload profile picture",
     *     tags={"Profile"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="picture",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Profile")
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/pictures",
     *     summary="Upload additional picture",
     *     tags={"Profile"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="picture",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/UserPicture")
     *     )
     * )
     */
    public function uploadPicture(Request $request)
    {
        $request->validate([
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = $request->user();

        if ($request->hasFile('picture')) {
            $path = $request->file('picture')->store('public/user_pictures');
            $picture = $user->pictures()->create([
                'picture_url' => Storage::url($path),
            ]);
        }

        return response()->json($picture);
    }

    /**
     * @OA\Get(
     *     path="/api/profile",
     *     summary="Get user profile",
     *     tags={"Profile"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     )
     * )
     */

}