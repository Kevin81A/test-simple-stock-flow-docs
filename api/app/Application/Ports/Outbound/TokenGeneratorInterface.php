<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

use App\Domain\Entities\User;

interface TokenGeneratorInterface
{
    /**
     * @return array{accessToken: string, expiresAt: string}
     */
    public function generateToken(User $user): array;

    /**
     * @return array{sub: string, unique_name: string, role: string, jti: string, exp: int}|null
     */
    public function validateToken(string $token): ?array;
}
