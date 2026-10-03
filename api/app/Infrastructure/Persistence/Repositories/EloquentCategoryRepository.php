<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Ports\Outbound\CategoryRepositoryInterface;
use App\Domain\Entities\Category;
use App\Infrastructure\Persistence\Mappers\CategoryMapper;
use App\Infrastructure\Persistence\Models\CategoryModel;

final class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function listAll(): array
    {
        $models = CategoryModel::query()->orderBy('name', 'asc')->get();
        return $models->map(fn(CategoryModel $m) => CategoryMapper::toDomain($m))->all();
    }

    public function findById(string $id): ?Category
    {
        $model = CategoryModel::query()->find($id);
        return $model !== null ? CategoryMapper::toDomain($model) : null;
    }

    public function existsById(string $id): bool
    {
        return CategoryModel::query()->where('id', $id)->exists();
    }
}
