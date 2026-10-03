<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\Ports\Outbound\PasswordHasherInterface;
use App\Application\Ports\Outbound\UserRepositoryInterface;
use App\Domain\Entities\User;
use App\Domain\Exceptions\AdminCreationNotAllowedException;
use App\Domain\Exceptions\DuplicateUserException;
use App\Domain\Exceptions\InvalidRoleException;

final class RegisterSellerUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHasherInterface $passwordHasher
    ) {}

    public function execute(string $username, string $password, string $role): string
    {
        // 1. Check if role is admin -> DP-04
        if ($role === User::ROLE_ADMIN) {
            throw new AdminCreationNotAllowedException();
        }

        // 2. Check if username already exists
        $normalized = User::normalizeUsername($username);
        if ($this->userRepository->existsByUsername($normalized)) {
            throw new DuplicateUserException($normalized);
        }

        // 3. Check role belongs to closed set
        if ($role !== User::ROLE_SELLER) {
            throw new InvalidRoleException($role);
        }

        $id = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
        $hash = $this->passwordHasher->hash($password);

        $user = User::create($id, $normalized, $hash, $role);
        $this->userRepository->save($user);

        return $id;
    }
}
