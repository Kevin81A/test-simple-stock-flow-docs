<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class ProductModel extends Model
{
    use SoftDeletes;

    protected $table = 'product';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'price',
        'stock',
        'category_id',
        'image_key',
        'version',
        'deleted_at',
    ];

    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
        'version' => 'integer',
        'deleted_at' => 'datetime',
    ];
}
