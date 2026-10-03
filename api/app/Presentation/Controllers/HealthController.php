<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;

final class HealthController
{
    public function health(): JsonResponse
    {
        return new JsonResponse(['status' => 'ok'], 200);
    }
}
