<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Http\Resources\UserResource;
use App\Jobs\SendForgotPasswordMail;
use App\Jobs\SendWelcomeMail;
use App\Models\PasswordReset;
use App\Models\User;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use App\Repositories\Interfaces\PasswordResetRepositoryInterface;
use App\Services\Interfaces\AuthServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService implements AuthServiceInterface
{
    protected AuthRepositoryInterface $authRepo;

    protected PasswordResetRepositoryInterface $passwordResetRepo;

    public function __construct(AuthRepositoryInterface $authRepo, PasswordResetRepositoryInterface $passwordResetRepo)
    {
        $this->authRepo = $authRepo;
        $this->passwordResetRepo = $passwordResetRepo;
    }

    public function register(array $data): User
    {
        $fullname = strtolower($data['first_name'] . ' ' . $data['last_name']);

        $data['slug'] = Str::slug($fullname) . '-' . strtolower(Str::random(8));
        $data['password'] = bcrypt($data['password']);

        $user = $this->authRepo->store($data);

        SendWelcomeMail::dispatch($user);

        return $user;
    }

    public function login(array $data): array
    {
        $user = $this->authRepo->getUserByEmailAddress($data['email_address']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new CustomException('Incorrect login credentials', Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->authRepo->createToken($user);

        return [
            'user'  => new UserResource($user),
            'token' => $token,
        ];
    }

    public function forgotPassword(array $data): ?PasswordReset
    {
        $user = $this->authRepo->getUserByEmailAddress($data['email_address']);

        if (!$user) {
            throw new CustomException('Email address does not exist', Response::HTTP_NOT_FOUND);
        }

        $token = Str::random(60);
        $forgotPasswordLink = config('app.url') . '/reset-password?token=' . $token;
        $expiration = Carbon::now()->addMinutes(30)->toDateTimeString();

        SendForgotPasswordMail::dispatch($user, $forgotPasswordLink);

        return $this->passwordResetRepo->create([
            'email'      => $user->email_address,
            'token'      => $token,
            'expires_at' => $expiration,
        ]);
    }

    public function resetPassword(array $data): User
    {
        $token = $this->passwordResetRepo->getToken($data);

        if (!$token) {
            throw new CustomException('Invalid token', Response::HTTP_FORBIDDEN);
        }

        $user = $this->authRepo->getUserByEmailAddress($token->email);

        $tokenExpiryMinutes = $token->created_at->diffInMinutes(now());
        $configExpiryMinutes = config('auth.passwords.users.expire');

        if ($tokenExpiryMinutes > $configExpiryMinutes) {
            throw new CustomException('Token has expired. Kindly request for a forgot password link again.', Response::HTTP_FORBIDDEN);
        }

        DB::transaction(function () use ($data, $user, $token) {
            $this->authRepo->updatePassword($data, $user);
            $this->passwordResetRepo->deleteToken($token);
        });

        $user->refresh();

        return $user;
    }
}
