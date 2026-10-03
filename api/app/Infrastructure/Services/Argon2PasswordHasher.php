<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Application\Ports\Outbound\PasswordHasherInterface;

final class Argon2PasswordHasher implements PasswordHasherInterface
{
    public function hash(string $plainPassword): string
    {
        return password_hash($plainPassword, PASSWORD_ARGON2ID);
    }

    public function verify(string $plainPassword, string $hash): bool
    {
        return password_verify($plainPassword, $hash);
    }
}
