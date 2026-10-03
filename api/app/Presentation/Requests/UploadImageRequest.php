<?php

declare(strict_types=1);

namespace App\Presentation\Requests;

final class UploadImageRequest extends BaseJsonRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required', 'file'],
        ];
    }
}
