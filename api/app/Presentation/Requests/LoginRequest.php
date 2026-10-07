<?php

declare(strict_types=1);

namespace App\Presentation\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\JsonResponse;

final class LoginRequest extends BaseJsonRequest
{
    public function rules(): array
    {
        return [
            'username' => ['required'],
            'password' => ['present'],
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->input('password') === '') {
            $response = new JsonResponse([
                'title' => 'Business rule violated',
                'status' => 422,
                'detail' => 'Password cannot be empty.',
            ], 422, [
                'Content-Type' => 'application/problem+json; charset=utf-8',
            ]);
            throw new HttpResponseException($response);
        }
    }
}
