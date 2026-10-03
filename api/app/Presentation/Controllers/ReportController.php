<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\Application\UseCases\GetSalesReportUseCase;
use App\Domain\ValueObjects\DateRange;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ReportController
{
    public function __construct(
        private readonly GetSalesReportUseCase $getSalesReportUseCase
    ) {}

    public function sales(Request $request): Response
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $errors = [];
        if ($from === null || trim((string) $from) === '') {
            $errors['from'] = ['Field required'];
        }
        if ($to === null || trim((string) $to) === '') {
            $errors['to'] = ['Field required'];
        }

        if (!empty($errors)) {
            return $this->badRequestMultiple($errors);
        }

        $isoPattern = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?(Z|[+-]\d{2}:\d{2})$/';
        if (!preg_match($isoPattern, (string) $from)) {
            $errors['from'] = ['Formato de fecha inválido. Se requiere ISO 8601 con desplazamiento explícito.'];
        }
        if (!preg_match($isoPattern, (string) $to)) {
            $errors['to'] = ['Formato de fecha inválido. Se requiere ISO 8601 con desplazamiento explícito.'];
        }

        if (!empty($errors)) {
            return $this->badRequestMultiple($errors);
        }

        $range = new DateRange(new DateTimeImmutable((string) $from), new DateTimeImmutable((string) $to));
        $report = $this->getSalesReportUseCase->execute($range);

        $rows = array_map(fn($r) => [
            'productId' => $r->productId,
            'productName' => $r->productName,
            'categoryName' => $r->categoryName,
            'unitsSold' => $r->unitsSold,
            'revenue' => $r->revenue,
        ], $report->rows);

        return new JsonResponse([
            'from' => $report->from,
            'to' => $report->to,
            'salesCount' => $report->salesCount,
            'grandTotal' => $report->grandTotal,
            'currency' => $report->currency,
            'rows' => $rows,
        ], 200);
    }

    private function badRequestMultiple(array $errors): JsonResponse
    {
        $fields = array_keys($errors);
        return new JsonResponse([
            'title' => 'Datos de entrada no válidos',
            'status' => 400,
            'detail' => sprintf('Datos de entrada no válidos: %s.', implode(', ', $fields)),
            'errors' => $errors,
        ], 400, [
            'Content-Type' => 'application/problem+json; charset=utf-8',
        ]);
    }
}
