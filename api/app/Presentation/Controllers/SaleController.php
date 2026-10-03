<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\Application\DTOs\RegisterSaleDTO;
use App\Application\UseCases\GetSaleDetailsUseCase;
use App\Application\UseCases\ListSalesUseCase;
use App\Application\UseCases\RegisterSaleUseCase;
use App\Domain\ValueObjects\DateRange;
use App\Presentation\Requests\RegisterSaleRequest;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class SaleController
{
    public function __construct(
        private readonly RegisterSaleUseCase $registerSaleUseCase,
        private readonly ListSalesUseCase $listSalesUseCase,
        private readonly GetSaleDetailsUseCase $getSaleDetailsUseCase
    ) {}

    public function store(RegisterSaleRequest $request): JsonResponse
    {
        $user = $request->attributes->get('current_user');
        $userId = is_array($user) ? (string) ($user['sub'] ?? '') : '';
        $username = is_array($user) ? (string) ($user['unique_name'] ?? '') : '';

        $lines = (array) $request->input('lines', []);

        $dto = new RegisterSaleDTO($lines, $userId, $username);
        $saleId = $this->registerSaleUseCase->execute($dto);

        return new JsonResponse(['id' => $saleId], 201, [
            'Location' => "/api/sales/{$saleId}",
        ]);
    }

    public function index(Request $request): Response
    {
        $dateValidation = $this->parseStrictDateRange($request);
        if ($dateValidation instanceof JsonResponse) {
            return $dateValidation;
        }

        $page = (int) $request->query('page', '1');
        $size = (int) $request->query('size', '20');

        $paged = $this->listSalesUseCase->execute($dateValidation, $page, $size);

        $items = array_map(fn($sale) => [
            'id' => $sale->id,
            'soldAt' => $sale->soldAt,
            'soldBy' => $sale->soldBy,
            'total' => $sale->total,
            'currency' => $sale->currency,
            'items' => array_map(fn($i) => [
                'productId' => $i->productId,
                'productName' => $i->productName,
                'quantity' => $i->quantity,
                'unitPrice' => $i->unitPrice,
                'subtotal' => $i->subtotal,
            ], $sale->items),
        ], $paged->items);

        return new JsonResponse([
            'items' => $items,
            'page' => $paged->page,
            'size' => $paged->size,
            'total' => $paged->total,
            'totalPages' => $paged->totalPages,
        ], 200);
    }

    public function show(string $id): Response
    {
        if (!$this->isValidUuid($id)) {
            return response('', 404, ['Content-Length' => '0']);
        }

        try {
            $sale = $this->getSaleDetailsUseCase->execute($id);
        } catch (\Exception) {
            return response('', 404, ['Content-Length' => '0']);
        }

        return new JsonResponse([
            'id' => $sale->id,
            'soldAt' => $sale->soldAt,
            'soldBy' => $sale->soldBy,
            'total' => $sale->total,
            'currency' => $sale->currency,
            'items' => array_map(fn($i) => [
                'productId' => $i->productId,
                'productName' => $i->productName,
                'quantity' => $i->quantity,
                'unitPrice' => $i->unitPrice,
                'subtotal' => $i->subtotal,
            ], $sale->items),
        ], 200);
    }

    private function isValidUuid(string $val): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $val) === 1;
    }

    private function parseStrictDateRange(Request $request): DateRange|JsonResponse
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

        // Strict ISO 8601 with explicit offset check (D-C3)
        // e.g. 2026-01-01T00:00:00Z or 2026-01-01T00:00:00+00:00
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

        $fromDate = new DateTimeImmutable((string) $from);
        $toDate = new DateTimeImmutable((string) $to);

        return new DateRange($fromDate, $toDate);
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
