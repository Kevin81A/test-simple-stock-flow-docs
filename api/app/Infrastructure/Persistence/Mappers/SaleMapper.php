<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers;

use App\Domain\Entities\Sale;
use App\Domain\Entities\SaleItem;
use App\Domain\ValueObjects\Money;
use App\Domain\ValueObjects\Quantity;
use App\Infrastructure\Persistence\Models\SaleItemModel;
use App\Infrastructure\Persistence\Models\SaleModel;
use DateTimeImmutable;

final class SaleMapper
{
    public static function toDomain(SaleModel $model): Sale
    {
        $items = [];
        foreach ($model->items as $itemModel) {
            $items[] = new SaleItem(
                (string) $itemModel->product_id,
                (string) $itemModel->product_name,
                Money::of((float) $itemModel->unit_price),
                (string) $itemModel->category_name,
                new Quantity((int) $itemModel->quantity)
            );
        }

        $soldAt = $model->sold_at instanceof \DateTimeInterface
            ? DateTimeImmutable::createFromInterface($model->sold_at)
            : new DateTimeImmutable((string) $model->sold_at);

        return new Sale(
            (string) $model->id,
            $soldAt,
            (string) $model->sold_by_user_id,
            (string) $model->sold_by_username,
            $items
        );
    }

    public static function toPersistence(Sale $domain): array
    {
        return [
            'id' => $domain->id(),
            'sold_at' => $domain->soldAt()->format('Y-m-d H:i:s.u'),
            'sold_by_user_id' => $domain->soldByUserId(),
            'sold_by_username' => $domain->soldByUsername(),
        ];
    }
}
