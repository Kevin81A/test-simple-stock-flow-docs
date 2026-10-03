<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\DTOs\CategoryDTO;
use App\Application\Ports\Outbound\CategoryRepositoryInterface;

final class GetCategoriesUseCase
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    /**
     * @return list<CategoryDTO>
     */
    public function execute(): array
    {
        $categories = $this->categoryRepository->listAll();
        $dtos = [];
        foreach ($categories as $cat) {
            $dtos[] = new CategoryDTO($cat->id(), $cat->name());
        }
        return $dtos;
    }
}
