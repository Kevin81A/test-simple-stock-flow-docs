<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Domain\Entities\Product;
use App\Domain\Exceptions\ConcurrencyConflictException;
use App\Infrastructure\Persistence\Mappers\ProductMapper;
use App\Infrastructure\Persistence\Models\ProductModel;

final class EloquentProductRepository implements ProductRepositoryInterface
{
    public function findById(string $id): ?Product
    {
        $model = ProductModel::withTrashed()->find($id);
        return $model !== null ? ProductMapper::toDomain($model) : null;
    }

    public function findActiveById(string $id): ?Product
    {
        $model = ProductModel::query()->find($id);
        return $model !== null ? ProductMapper::toDomain($model) : null;
    }

    public function searchPaginated(?string $search, ?string $categoryId, int $page, int $size): array
    {
        $query = ProductModel::query();

        if ($categoryId !== null && trim($categoryId) !== '') {
            $query->where('category_id', trim($categoryId));
        }

        if ($search !== null && trim($search) !== '') {
            $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($search));
            $query->where('name', 'LIKE', '%' . $escaped . '%');
        }

        $total = $query->count();
        $models = $query->orderBy('name', 'asc')
            ->forPage($page, $size)
            ->get();

        $items = $models->map(fn(ProductModel $m) => ProductMapper::toDomain($m))->all();

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    public function save(Product $product): void
    {
        $existing = ProductModel::withTrashed()->find($product->id());
        $data = ProductMapper::toPersistence($product);

        if ($existing === null) {
            $data['version'] = 1;
            ProductModel::query()->create($data);
        } else {
            $currentVersion = (int) $existing->version;
            $data['version'] = $currentVersion + 1;

            $affected = ProductModel::query()
                ->where('id', $product->id())
                ->where('version', $currentVersion)
                ->update($data);

            if ($affected === 0) {
                throw new ConcurrencyConflictException();
            }
        }
    }

    public function updateWithOptimisticLock(Product $product, int $expectedVersion): void
    {
        $data = ProductMapper::toPersistence($product);
        $data['version'] = $expectedVersion + 1;

        $affected = ProductModel::query()
            ->where('id', $product->id())
            ->where('version', $expectedVersion)
            ->update($data);

        if ($affected === 0) {
            throw new ConcurrencyConflictException();
        }
    }

    public function softDelete(string $id): void
    {
        ProductModel::query()->where('id', $id)->delete();
    }
}
