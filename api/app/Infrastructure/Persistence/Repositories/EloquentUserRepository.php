<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Ports\Outbound\UserRepositoryInterface;
use App\Domain\Entities\User;
use App\Infrastructure\Persistence\Mappers\UserMapper;
use App\Infrastructure\Persistence\Models\UserModel;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(string $id): ?User
    {
        $model = UserModel::query()->find($id);
        return $model !== null ? UserMapper::toDomain($model) : null;
    }

    public function findByUsername(string $username): ?User
    {
        $model = UserModel::query()->where('username', $username)->first();
        return $model !== null ? UserMapper::toDomain($model) : null;
    }

    public function existsByUsername(string $username): bool
    {
        return UserModel::query()->where('username', $username)->exists();
    }

    public function save(User $user): void
    {
        UserModel::query()->updateOrCreate(
            ['id' => $user->id()],
            UserMapper::toPersistence($user)
        );
    }
}
