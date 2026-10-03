<?php

declare(strict_types=1);

namespace App\Presentation\Requests;

final class RegisterSellerRequest extends BaseJsonRequest
{
    public function rules(): array
    {
        return [
            'username' => ['required'],
            'password' => ['required'],
            'role' => ['required'],
        ];
    }
}
