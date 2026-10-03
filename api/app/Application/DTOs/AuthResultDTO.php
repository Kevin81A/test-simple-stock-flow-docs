<?php

declare(strict_types=1);

namespace App\Application\DTOs;

final class AuthResultDTO
{
    public function __construct(
        public readonly string $accessToken,
        public readonly string $expiresAt,
        public readonly string $username,
        public readonly string $role
    ) {}
}
