<?php

declare(strict_types=1);

namespace App\Presentation\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\JsonResponse;

final class RegisterSaleRequest extends BaseJsonRequest
{
    public function rules(): array
    {
        return [
            'lines' => ['present', 'array'],
            'lines.*.productId' => ['required', 'uuid'],
            'lines.*.quantity' => ['required', 'integer'],
        ];
    }

    protected function passedValidation(): void
    {
        $lines = $this->input('lines');
        if (!is_array($lines) || empty($lines)) {
            $response = new JsonResponse([
                'title' => 'Business rule violated',
                'status' => 422,
                'detail' => 'The sale must have at least one item.',
            ], 422, [
                'Content-Type' => 'application/problem+json; charset=utf-8',
            ]);
            throw new HttpResponseException($response);
        }
    }
}
