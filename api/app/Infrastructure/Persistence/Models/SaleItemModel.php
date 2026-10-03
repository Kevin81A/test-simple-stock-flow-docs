<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

final class SaleItemModel extends Model
{
    protected $table = 'sale_item';
    public $timestamps = false;
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_name',
        'unit_price',
        'category_name',
        'quantity',
    ];

    protected $casts = [
        'unit_price' => 'float',
        'quantity' => 'integer',
    ];
}
