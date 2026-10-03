<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Exceptions\DomainException;
use App\Domain\Exceptions\InvalidRoleException;

final class User
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_SELLER = 'seller';

    private string $id;
    private string $username;
    private string $passwordHash;
    private string $role;

    public function __construct(
        string $id,
        string $username,
        string $passwordHash,
        string $role
    ) {
        $this->id = $id;
        $this->username = self::normalizeUsername($username);

        $trimmedHash = trim($passwordHash);
        if ($trimmedHash === '') {
            throw new class('El hash de la contraseña no puede estar vacío.') extends DomainException {};
        }
        $this->passwordHash = $trimmedHash;

        if (!in_array($role, [self::ROLE_ADMIN, self::ROLE_SELLER], true)) {
            throw new InvalidRoleException($role);
        }
        $this->role = $role;
    }

    public static function create(
        string $id,
        string $username,
        string $passwordHash,
        string $role
    ): self {
        return new self($id, $username, $passwordHash, $role);
    }

    public static function normalizeUsername(string $username): string
    {
        $normalized = mb_strtolower(trim($username), 'UTF-8');
        if ($normalized === '') {
            throw new class('El nombre de usuario es obligatorio.') extends DomainException {};
        }
        return $normalized;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }

    public function role(): string
    {
        return $this->role;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isSeller(): bool
    {
        return $this->role === self::ROLE_SELLER;
    }
}
