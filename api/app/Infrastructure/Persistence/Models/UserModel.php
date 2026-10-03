<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

final class UserModel extends Model
{
    protected $table = 'user';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'username',
        'password_hash',
        'role',
    ];
}
