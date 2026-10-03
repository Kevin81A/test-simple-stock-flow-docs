<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\Application\UseCases\GetCategoriesUseCase;
use Symfony\Component\HttpFoundation\JsonResponse;

final class CategoryController
{
    public function __construct(
        private readonly GetCategoriesUseCase $getCategoriesUseCase
    ) {}

    public function index(): JsonResponse
    {
        $categories = $this->getCategoriesUseCase->execute();
        $data = array_map(fn($c) => [
            'id' => $c->id,
            'name' => $c->name,
        ], $categories);

        return new JsonResponse($data, 200);
    }
}
