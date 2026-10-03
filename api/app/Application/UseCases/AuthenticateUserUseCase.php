<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\DTOs\AuthResultDTO;
use App\Application\Ports\Outbound\PasswordHasherInterface;
use App\Application\Ports\Outbound\TokenGeneratorInterface;
use App\Application\Ports\Outbound\UserRepositoryInterface;
use App\Domain\Entities\User;
use App\Domain\Exceptions\DomainException;

final class AuthenticateUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHasherInterface $passwordHasher,
        private readonly TokenGeneratorInterface $tokenGenerator
    ) {}

    public function execute(string $username, string $password): AuthResultDTO
    {
        $normalized = User::normalizeUsername($username);
        $user = $this->userRepository->findByUsername($normalized);

        if ($user === null || !$this->passwordHasher->verify($password, $user->passwordHash())) {
            throw new class('Usuario o contraseña incorrectos.') extends DomainException {};
        }

        $tokenData = $this->tokenGenerator->generateToken($user);

        return new AuthResultDTO(
            $tokenData['accessToken'],
            $tokenData['expiresAt'],
            $user->username(),
            $user->role()
        );
    }
}
