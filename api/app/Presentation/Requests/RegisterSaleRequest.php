<?php

declare(strict_types=1);

namespace App\Presentation\Requests;

final class RegisterSaleRequest extends BaseJsonRequest
{
    public function rules(): array
    {
        return [
            'lines' => ['required', 'array'],
            'lines.*.productId' => ['required', 'uuid'],
            'lines.*.quantity' => ['required', 'integer'],
        ];
    }
}
