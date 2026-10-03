<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class SaleModel extends Model
{
    protected $table = 'sale';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'sold_at',
        'sold_by_user_id',
        'sold_by_username',
    ];

    protected $casts = [
        'sold_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SaleItemModel::class, 'sale_id', 'id');
    }
}
