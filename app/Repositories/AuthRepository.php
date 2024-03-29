<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;

class AuthRepository implements AuthRepositoryInterface
{
    private User $user;

    protected UserRepositoryInterface $userRepo;

    public function __construct(User $user, UserRepositoryInterface $userRepo)
    {
        $this->user = $user;
        $this->userRepo = $userRepo;
    }

    public function store(array $data): User
    {
        return $this->user->create($data);
    }

    public function getUserByEmailAddress(string $email): ?User
    {
        return $this->userRepo->getUserByEmailAddress($email);
    }

    public function createToken(User $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }
}
