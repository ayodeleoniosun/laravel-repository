<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\ResetPasswordRequest;
use App\Http\Requests\Users\UserRegistrationRequest;
use App\Services\Interfaces\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    private AuthServiceInterface $auth;

    public function __construct(AuthServiceInterface $auth)
    {
        $this->auth = $auth;
    }

    public function register(UserRegistrationRequest $request): JsonResponse
    {
        $response = $this->auth->register($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Registration successful',
            'data'    => $response,
        ], Response::HTTP_CREATED);
    }

    public function login(Request $request): JsonResponse
    {
        $response = $this->auth->login($request->all());

        return response()->json([
            'status'  => 'success',
            'message' => 'Login successful',
            'data'    => $response,
        ], Response::HTTP_OK);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $response = $this->auth->forgotPassword($request->all());

        return response()->json([
            'status'  => 'success',
            'message' => 'Reset password link successfully sent to ' . $request->email_address,
            'data'    => $response,
        ], Response::HTTP_OK);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $response = $this->auth->resetPassword($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Password successfully reset',
            'data'    => $response,
        ], Response::HTTP_OK);
    }
}
