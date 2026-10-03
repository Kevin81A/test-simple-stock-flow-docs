<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers;

use App\Domain\Entities\Category;
use App\Infrastructure\Persistence\Models\CategoryModel;

final class CategoryMapper
{
    public static function toDomain(CategoryModel $model): Category
    {
        return new Category(
            (string) $model->id,
            (string) $model->name
        );
    }

    public static function toPersistence(Category $domain): array
    {
        return [
            'id' => $domain->id(),
            'name' => $domain->name(),
        ];
    }
}
