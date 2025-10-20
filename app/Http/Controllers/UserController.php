<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPicture;
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
     *     @OA\Parameter(name="name", in="header", required=true, @OA\Schema(type="string", enum={"liked", "disliked", "liked_me"})),
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
        } elseif ($category === 'liked_me') {
            $data = $user->likesReceived()->with(['user.profile', 'user.pictures'])->paginate(10);
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
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="bio", type="string", example="I am a software engineer."),
     *                 @OA\Property(property="location", type="string", example="New York, USA"),
     *                 @OA\Property(property="birth_date", type="string", format="date", example="1990-01-01"),
     *                 @OA\Property(property="picture", type="string", format="binary")
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
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'bio' => 'sometimes|string',
            'location' => 'sometimes|string|max:255',
            'birth_date' => 'sometimes|date',
            'age' => 'sometimes|integer',
            'picture' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);

        if ($request->hasFile('picture')) {
            $path = Storage::disk('gcs')->putFile('profile_pictures', $request->file('picture'), 'public');
            $bucket = config('filesystems.disks.gcs.bucket');
            $url = 'https://storage.googleapis.com/' . $bucket . '/' . $path;
            $validatedData['profile_picture_url'] = $url;
            unset($validatedData['picture']);
        }

        $profile->update($validatedData);

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
    public function uploadAdditionalPicture(Request $request)
    {
        $request->validate([
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = $request->user();

        // Limit the number of additional pictures to 5.
        if ($user->pictures()->count() >= 5) {
            return response()->json(['message' => 'You have reached the maximum number of pictures.'], 400);
        }

        // Sanitize email to use as a directory name.
        $email_as_path = str_replace('@', '_at_', $user->email);
        $directory = 'additional_pictures/' . $email_as_path;

        // Store the file in GCS with public visibility.
        $path = Storage::disk('gcs')->putFile($directory, $request->file('picture'), 'public');

        // Manually construct the public URL.
        $bucket = config('filesystems.disks.gcs.bucket');
        $url = 'https://storage.googleapis.com/' . $bucket . '/' . $path;

        // Create a new UserPicture record.
        $userPicture = $user->pictures()->create([
            'picture_url' => $url,
        ]);

        return response()->json($userPicture);
    }

    /**
     * @OA\Get(
     *     path="/api/pictures/{picture}",
     *     summary="Get a specific additional picture by ID",
     *     tags={"Profile"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="picture", in="path", required=true, @OA\Schema(type="integer", format="int64", example=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/UserPicture")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User does not own this picture"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Picture not found"
     *     )
     * )
     */
    public function getPicture(Request $request, UserPicture $picture)
    {
        // Ensure the user owns the picture
        if ($request->user()->id !== $picture->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($picture);
    }

    /**
     * @OA\Post(
     *     path="/api/pictures/{picture}",
     *     summary="Update a specific additional picture by ID",
     *     tags={"Profile"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="picture", in="path", required=true, @OA\Schema(type="integer", format="int64", example=1)),
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
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User does not own this picture"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Picture not found"
     *     )
     * )
     */
    public function updatePicture(Request $request, UserPicture $picture)
    {
        $request->validate([
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Ensure the user owns the picture
        if ($request->user()->id !== $picture->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Delete the old file from GCS.
        $old_path = str_replace('https://storage.googleapis.com/' . config('filesystems.disks.gcs.bucket') . '/', '', $picture->picture_url);
        Storage::disk('gcs')->delete($old_path);

        // Store the new file.
        $user = $request->user();
        $email_as_path = str_replace('@', '_at_', $user->email);
        $directory = 'additional_pictures/' . $email_as_path;
        $path = Storage::disk('gcs')->putFile($directory, $request->file('picture'), 'public');
        $url = 'https://storage.googleapis.com/' . config('filesystems.disks.gcs.bucket') . '/' . $path;

        // Update the picture record.
        $picture->update(['picture_url' => $url]);

        return response()->json($picture->fresh());
    }

    /**
     * @OA\Delete(
     *     path="/api/pictures/{picture}",
     *     summary="Delete a specific additional picture by ID",
     *     tags={"Profile"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="picture", in="path", required=true, @OA\Schema(type="integer", format="int64", example=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Picture deleted successfully"))
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User does not own this picture"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Picture not found"
     *     )
     * )
     */
    public function deletePicture(Request $request, UserPicture $picture)
    {
        // Ensure the user owns the picture
        if ($request->user()->id !== $picture->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Delete the file from GCS.
        $old_path = str_replace('https://storage.googleapis.com/' . config('filesystems.disks.gcs.bucket') . '/', '', $picture->picture_url);
        Storage::disk('gcs')->delete($old_path);

        // Delete the picture record.
        $picture->delete();

        return response()->json(['message' => 'Picture deleted successfully']);
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
    public function getProfile(Request $request)
    {
        $user = $request->user()->load(['profile', 'pictures']);

        return response()->json($user);
    }

}