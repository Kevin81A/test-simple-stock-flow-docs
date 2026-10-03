<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers;

use App\Domain\Entities\Product;
use App\Domain\ValueObjects\Money;
use App\Infrastructure\Persistence\Models\ProductModel;

final class ProductMapper
{
    public static function toDomain(ProductModel $model): Product
    {
        return new Product(
            (string) $model->id,
            (string) $model->name,
            Money::of((float) $model->price),
            (int) $model->stock,
            (string) $model->category_id,
            $model->image_key !== null ? (string) $model->image_key : null
        );
    }

    public static function toPersistence(Product $domain): array
    {
        return [
            'id' => $domain->id(),
            'name' => $domain->name(),
            'price' => $domain->price()->amount(),
            'stock' => $domain->stock(),
            'category_id' => $domain->categoryId(),
            'image_key' => $domain->imageKey(),
        ];
    }
}
