<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\DTOs\PagedResultDTO;
use App\Application\DTOs\ProductViewDTO;
use App\Application\Ports\Outbound\CategoryRepositoryInterface;
use App\Application\Ports\Outbound\FileStorageInterface;
use App\Application\Ports\Outbound\ProductRepositoryInterface;

final class GetProductCatalogUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly FileStorageInterface $fileStorage
    ) {}

    /**
     * @return PagedResultDTO<ProductViewDTO>
     */
    public function execute(?string $search, ?string $categoryId, int $page, int $size): PagedResultDTO
    {
        // Clamp pagination per spec D-C5
        $servedPage = $page < 1 ? 1 : $page;
        $servedSize = $size < 1 ? 20 : ($size > 100 ? 100 : $size);

        $result = $this->productRepository->searchPaginated($search, $categoryId, $servedPage, $servedSize);

        $categories = [];
        foreach ($this->categoryRepository->listAll() as $cat) {
            $categories[$cat->id()] = $cat->name();
        }

        $items = [];
        foreach ($result['items'] as $product) {
            $catName = $categories[$product->categoryId()] ?? 'General';
            $imageUrl = $product->imageKey() !== null ? $this->fileStorage->getUrl($product->imageKey()) : null;

            $items[] = new ProductViewDTO(
                $product->id(),
                $product->name(),
                $product->price()->amount(),
                $product->price()->currency(),
                $product->stock(),
                $product->categoryId(),
                $catName,
                $imageUrl
            );
        }

        return PagedResultDTO::create($items, $servedPage, $servedSize, $result['total']);
    }
}
