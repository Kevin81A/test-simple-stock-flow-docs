<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Ports\Outbound\SaleRepositoryInterface;
use App\Domain\Entities\Sale;
use App\Domain\ValueObjects\DateRange;
use App\Infrastructure\Persistence\Mappers\SaleMapper;
use App\Infrastructure\Persistence\Models\SaleItemModel;
use App\Infrastructure\Persistence\Models\SaleModel;

final class EloquentSaleRepository implements SaleRepositoryInterface
{
    public function findById(string $id): ?Sale
    {
        $model = SaleModel::query()->with('items')->find($id);
        return $model !== null ? SaleMapper::toDomain($model) : null;
    }

    public function searchByDateRangePaginated(DateRange $range, int $page, int $size): array
    {
        $fromStr = $range->from()->format('Y-m-d H:i:s.u');
        $toStr = $range->to()->format('Y-m-d H:i:s.u');

        $query = SaleModel::query()
            ->with('items')
            ->where('sold_at', '>=', $fromStr)
            ->where('sold_at', '<', $toStr);

        $total = $query->count();
        $models = $query->orderBy('sold_at', 'desc')
            ->forPage($page, $size)
            ->get();

        $items = $models->map(fn(SaleModel $m) => SaleMapper::toDomain($m))->all();

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    public function save(Sale $sale): void
    {
        $saleData = SaleMapper::toPersistence($sale);
        SaleModel::query()->create($saleData);

        foreach ($sale->items() as $item) {
            SaleItemModel::query()->create([
                'sale_id' => $sale->id(),
                'product_id' => $item->productId(),
                'product_name' => $item->productName(),
                'unit_price' => $item->unitPrice()->amount(),
                'category_name' => $item->categoryName(),
                'quantity' => $item->quantity()->value(),
            ]);
        }
    }
}
