<?php

declare(strict_types=1);

namespace App\Presentation\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class BaseJsonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors()->toArray();
        $fields = array_keys($errors);
        $fieldsStr = implode(', ', $fields);

        $response = new JsonResponse([
            'title' => 'Invalid input data',
            'status' => 400,
            'detail' => sprintf('Invalid input data: %s.', $fieldsStr),
            'errors' => $errors,
        ], 400, [
            'Content-Type' => 'application/problem+json; charset=utf-8',
        ]);

        throw new HttpResponseException($response);
    }
}