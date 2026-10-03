<?php

declare(strict_types=1);

namespace App\Presentation\Requests;

final class UpdateProductRequest extends BaseJsonRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'price' => ['required', 'numeric'],
            'stock' => ['required', 'integer'],
            'categoryId' => ['required', 'uuid'],
        ];
    }
}
