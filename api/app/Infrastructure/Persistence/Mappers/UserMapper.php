<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers;

use App\Domain\Entities\User;
use App\Infrastructure\Persistence\Models\UserModel;

final class UserMapper
{
    public static function toDomain(UserModel $model): User
    {
        return new User(
            (string) $model->id,
            (string) $model->username,
            (string) $model->password_hash,
            (string) $model->role
        );
    }

    public static function toPersistence(User $domain): array
    {
        return [
            'id' => $domain->id(),
            'username' => $domain->username(),
            'password_hash' => $domain->passwordHash(),
            'role' => $domain->role(),
        ];
    }
}
