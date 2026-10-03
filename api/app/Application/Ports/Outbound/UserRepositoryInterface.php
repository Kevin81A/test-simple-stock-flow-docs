<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

use App\Domain\Entities\User;

interface UserRepositoryInterface
{
    public function findById(string $id): ?User;

    public function findByUsername(string $username): ?User;

    public function existsByUsername(string $username): bool;

    public function save(User $user): void;
}
