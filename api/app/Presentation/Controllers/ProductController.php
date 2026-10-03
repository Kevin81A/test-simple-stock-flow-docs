<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\Application\DTOs\CreateProductDTO;
use App\Application\DTOs\UpdateProductDTO;
use App\Application\UseCases\AttachProductImageUseCase;
use App\Application\UseCases\CreateProductUseCase;
use App\Application\UseCases\GetProductByIdUseCase;
use App\Application\UseCases\GetProductCatalogUseCase;
use App\Application\UseCases\SoftDeleteProductUseCase;
use App\Application\UseCases\UpdateProductUseCase;
use App\Domain\Exceptions\DomainException;
use App\Presentation\Requests\CreateProductRequest;
use App\Presentation\Requests\UpdateProductRequest;
use App\Presentation\Requests\UploadImageRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ProductController
{
    public function __construct(
        private readonly CreateProductUseCase $createProductUseCase,
        private readonly UpdateProductUseCase $updateProductUseCase,
        private readonly SoftDeleteProductUseCase $softDeleteProductUseCase,
        private readonly AttachProductImageUseCase $attachProductImageUseCase,
        private readonly GetProductCatalogUseCase $getProductCatalogUseCase,
        private readonly GetProductByIdUseCase $getProductByIdUseCase
    ) {}

    public function index(Request $request): JsonResponse
    {
        // Check non-integer page/size or invalid categoryId -> 400 per D-C5 / api-contract §1.2
        if ($request->has('page') && !ctype_digit((string) $request->input('page'))) {
            return $this->badRequest('page', 'El parámetro page debe ser un número entero.');
        }
        if ($request->has('size') && !ctype_digit((string) $request->input('size'))) {
            return $this->badRequest('size', 'El parámetro size debe ser un número entero.');
        }

        $categoryId = $request->query('categoryId');
        if ($categoryId !== null && !$this->isValidUuid((string) $categoryId)) {
            return $this->badRequest('categoryId', 'El parámetro categoryId debe ser un UUID válido.');
        }

        $search = $request->query('search');
        $page = (int) $request->query('page', '1');
        $size = (int) $request->query('size', '20');

        $paged = $this->getProductCatalogUseCase->execute(
            $search !== null ? (string) $search : null,
            $categoryId !== null ? (string) $categoryId : null,
            $page,
            $size
        );

        $items = array_map(fn($item) => [
            'id' => $item->id,
            'name' => $item->name,
            'price' => $item->price,
            'currency' => $item->currency,
            'stock' => $item->stock,
            'categoryId' => $item->categoryId,
            'categoryName' => $item->categoryName,
            'imageUrl' => $item->imageUrl,
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
        // D-C7: malformed id is empty 404
        if (!$this->isValidUuid($id)) {
            return response('', 404, ['Content-Length' => '0']);
        }

        try {
            $product = $this->getProductByIdUseCase->execute($id);
        } catch (\App\Domain\Exceptions\ProductNotFoundException) {
            return response('', 404, ['Content-Length' => '0']);
        }

        return new JsonResponse([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'currency' => $product->currency,
            'stock' => $product->stock,
            'categoryId' => $product->categoryId,
            'categoryName' => $product->categoryName,
            'imageUrl' => $product->imageUrl,
        ], 200);
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        $dto = new CreateProductDTO(
            (string) $request->input('name'),
            (float) $request->input('price'),
            (int) $request->input('stock'),
            (string) $request->input('categoryId')
        );

        $id = $this->createProductUseCase->execute($dto);

        return new JsonResponse(['id' => $id], 201, [
            'Location' => "/api/products/{$id}",
        ]);
    }

    public function update(string $id, UpdateProductRequest $request): Response
    {
        if (!$this->isValidUuid($id)) {
            return response('', 404, ['Content-Length' => '0']);
        }

        $dto = new UpdateProductDTO(
            $id,
            (string) $request->input('name'),
            (float) $request->input('price'),
            (int) $request->input('stock'),
            (string) $request->input('categoryId')
        );

        try {
            $this->updateProductUseCase->execute($dto);
        } catch (\App\Domain\Exceptions\ProductNotFoundException) {
            return response('', 404, ['Content-Length' => '0']);
        }

        return response('', 204, ['Content-Length' => '0']);
    }

    public function destroy(string $id): Response
    {
        if (!$this->isValidUuid($id)) {
            return response('', 404, ['Content-Length' => '0']);
        }

        try {
            $this->softDeleteProductUseCase->execute($id);
        } catch (\App\Domain\Exceptions\ProductNotFoundException) {
            return response('', 404, ['Content-Length' => '0']);
        }

        return response('', 204, ['Content-Length' => '0']);
    }

    public function uploadImage(string $id, UploadImageRequest $request): Response
    {
        if (!$this->isValidUuid($id)) {
            return response('', 404, ['Content-Length' => '0']);
        }

        $file = $request->file('file');
        if ($file === null) {
            return $this->badRequest('file', 'Field required');
        }

        // Validate max 5MB -> 422 (E-08)
        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new class('La imagen supera el máximo de 5 MB.') extends DomainException {};
        }

        // Validate mime types: image/jpeg, image/png, image/webp
        $mime = $file->getMimeType();
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($allowed[$mime])) {
            throw new class(sprintf("Tipo de archivo no permitido: %s.", $mime)) extends DomainException {};
        }

        $ext = $allowed[$mime];

        try {
            $url = $this->attachProductImageUseCase->execute($id, $file->getRealPath(), $ext);
        } catch (\App\Domain\Exceptions\ProductNotFoundException) {
            return response('', 404, ['Content-Length' => '0']);
        }

        return new JsonResponse(['url' => $url], 200);
    }

    private function isValidUuid(string $val): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $val) === 1;
    }

    private function badRequest(string $field, string $msg): JsonResponse
    {
        return new JsonResponse([
            'title' => 'Datos de entrada no válidos',
            'status' => 400,
            'detail' => sprintf('Datos de entrada no válidos: %s.', $field),
            'errors' => [
                $field => [$msg],
            ],
        ], 400, [
            'Content-Type' => 'application/problem+json; charset=utf-8',
        ]);
    }
}
