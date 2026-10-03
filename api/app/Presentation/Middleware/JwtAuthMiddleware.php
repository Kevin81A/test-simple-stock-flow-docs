<?php

declare(strict_types=1);

namespace App\Presentation\Middleware;

use App\Application\Ports\Outbound\TokenGeneratorInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class JwtAuthMiddleware
{
    public function __construct(
        private readonly TokenGeneratorInterface $tokenGenerator
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization', '');
        if (!str_starts_with($authHeader, 'Bearer ')) {
            return response('', 401, [
                'Content-Length' => '0',
                'WWW-Authenticate' => 'Bearer',
            ]);
        }

        $token = substr($authHeader, 7);
        $claims = $this->tokenGenerator->validateToken($token);

        if ($claims === null) {
            return response('', 401, [
                'Content-Length' => '0',
                'WWW-Authenticate' => 'Bearer error="invalid_token"',
            ]);
        }

        $request->attributes->set('current_user', $claims);

        return $next($request);
    }
}
