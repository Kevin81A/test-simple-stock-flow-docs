<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Ports\Outbound\SalesReportQueryInterface;
use App\Domain\ValueObjects\DateRange;
use Illuminate\Support\Facades\DB;

final class DatabaseSalesReportQuery implements SalesReportQueryInterface
{
    public function getReport(DateRange $range): array
    {
        $fromStr = $range->from()->format('Y-m-d H:i:s.u');
        $toStr = $range->to()->format('Y-m-d H:i:s.u');

        // 1. Overall period aggregation: salesCount & grandTotal
        $overall = DB::table('sale as s')
            ->join('sale_item as si', 's.id', '=', 'si.sale_id')
            ->where('s.sold_at', '>=', $fromStr)
            ->where('s.sold_at', '<', $toStr)
            ->selectRaw('COUNT(DISTINCT s.id) as sales_count, COALESCE(SUM(si.quantity * si.unit_price), 0) as grand_total')
            ->first();

        $salesCount = $overall ? (int) $overall->sales_count : 0;
        $grandTotal = $overall ? round((float) $overall->grand_total, 2, PHP_ROUND_HALF_UP) : 0.0;

        if ($salesCount === 0) {
            return [
                'salesCount' => 0,
                'grandTotal' => 0.0,
                'currency' => 'COP',
                'rows' => [],
            ];
        }

        // 2. Rows aggregated by product_id and category_name (DP-01: most recent product_name in range)
        $rowsData = DB::table('sale_item as si')
            ->join('sale as s', 's.id', '=', 'si.sale_id')
            ->where('s.sold_at', '>=', $fromStr)
            ->where('s.sold_at', '<', $toStr)
            ->selectRaw("
                si.product_id,
                si.category_name,
                (
                    SELECT sub_si.product_name
                    FROM sale_item sub_si
                    JOIN sale sub_s ON sub_s.id = sub_si.sale_id
                    WHERE sub_si.product_id = si.product_id
                      AND sub_s.sold_at >= ?
                      AND sub_s.sold_at < ?
                    ORDER BY sub_s.sold_at DESC, sub_s.id DESC
                    LIMIT 1
                ) as product_name,
                SUM(si.quantity) as units_sold,
                ROUND(SUM(si.quantity * si.unit_price), 2) as revenue
            ", [$fromStr, $toStr])
            ->groupBy('si.product_id', 'si.category_name')
            ->orderByRaw('revenue DESC')
            ->get();

        $rows = [];
        foreach ($rowsData as $r) {
            $rows[] = [
                'productId' => (string) $r->product_id,
                'productName' => (string) $r->product_name,
                'categoryName' => (string) $r->category_name,
                'unitsSold' => (int) $r->units_sold,
                'revenue' => (float) $r->revenue,
            ];
        }

        return [
            'salesCount' => $salesCount,
            'grandTotal' => $grandTotal,
            'currency' => 'COP',
            'rows' => $rows,
        ];
    }
}
