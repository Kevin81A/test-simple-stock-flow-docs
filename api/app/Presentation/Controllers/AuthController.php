<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\Application\UseCases\AuthenticateUserUseCase;
use App\Application\UseCases\RegisterSellerUseCase;
use App\Presentation\Requests\LoginRequest;
use App\Presentation\Requests\RegisterSellerRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class AuthController
{
    public function __construct(
        private readonly AuthenticateUserUseCase $authenticateUserUseCase,
        private readonly RegisterSellerUseCase $registerSellerUseCase
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $username = (string) $request->input('username');
        $password = (string) $request->input('password');

        $result = $this->authenticateUserUseCase->execute($username, $password);

        return new JsonResponse([
            'accessToken' => $result->accessToken,
            'expiresAt' => $result->expiresAt,
            'username' => $result->username,
            'role' => $result->role,
        ], 200);
    }

    public function register(RegisterSellerRequest $request): JsonResponse
    {
        $username = (string) $request->input('username');
        $password = (string) $request->input('password');
        $role = (string) $request->input('role');

        $id = $this->registerSellerUseCase->execute($username, $password, $role);

        // D-C6: 201 Created without Location header
        return new JsonResponse(['id' => $id], 201);
    }
}
