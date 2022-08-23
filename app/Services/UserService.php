<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService implements UserServiceInterface
{
    protected UserRepositoryInterface $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function profile(array $data): UserResource
    {
        $user = $this->userRepo->getUser($data['slug']);

        if (! $user) {
            throw new CustomException('User not found', Response::HTTP_NOT_FOUND);
        }

        return new UserResource($user);
    }

    public function updateProfile(User $user, array $data): UserResource
    {
        $phoneNumberExist = $this->userRepo->getDuplicateUserByPhoneNumber($data['phone'], $user->id);

        if ($phoneNumberExist) {
            throw new CustomException('Phone number belongs to another user', Response::HTTP_FORBIDDEN);
        }

        return new UserResource($this->userRepo->updateProfile($data, $user));
    }

    public function updatePassword(User $user, array $data): void
    {
        if (! $user || ! Hash::check($data['current_password'], $user->password)) {
            throw new CustomException('Incorrect current password', Response::HTTP_FORBIDDEN);
        }

        $this->userRepo->updatePassword($data, $user);
    }

    public function updateProfilePicture(User $user, array $data): UserResource
    {
        $image = (object) $data['image'];

        $extension = $image->extension();
        $filename = $user->id . '' . time() . '.' . $extension;

        Storage::disk('profile_pictures')->put($filename, file_get_contents($image->getRealPath()));

        return new UserResource($this->userRepo->updateProfilePicture($filename, $user));
    }

    public function logout(User $user): int
    {
        return $this->userRepo->logout($user);
    }
}
