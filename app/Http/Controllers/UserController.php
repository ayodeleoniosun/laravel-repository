<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\UpdatePasswordRequest;
use App\Http\Requests\Users\UpdateProfilePictureRequest;
use App\Http\Requests\Users\UpdateUserBusinessInformationRequest;
use App\Http\Requests\Users\UpdateUserProfileRequest;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    private UserServiceInterface $user;

    public function __construct(UserServiceInterface $user)
    {
        $this->user = $user;
    }

    public function profile(Request $request, string $slug): JsonResponse
    {
        $request->merge(['slug' => $slug]);

        $response = $this->user->profile($request->all());

        return response()->json([
            'status'  => 'success',
            'message' => '',
            'data'    => $response,
        ], Response::HTTP_OK);
    }

    public function updateProfile(UpdateUserProfileRequest $request): JsonResponse
    {
        $response = $this->user->updateProfile($request->user(), $request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Profile successfully updated',
            'data'    => $response,
        ], Response::HTTP_OK);
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $response = $this->user->updatePassword($request->user(), $request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Password successfully updated',
            'data'    => $response,
        ], Response::HTTP_OK);
    }

    public function updateProfilePicture(UpdateProfilePictureRequest $request): JsonResponse
    {
        $response = $this->user->updateProfilePicture($request->user(), $request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Profile picture successfully updated',
            'data'    => $response,
        ], Response::HTTP_OK);
    }

    public function logout(Request $request): JsonResponse
    {
        $response = $this->user->logout($request->user());

        return response()->json([
            'status'  => 'success',
            'message' => 'User logged out',
            'data'    => $response,
        ], Response::HTTP_OK);
    }
}
