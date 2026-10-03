<?php

declare(strict_types=1);

namespace App\Presentation\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RequireAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->attributes->get('current_user');
        if (!is_array($user) || ($user['role'] ?? '') !== 'admin') {
            return response('', 403, [
                'Content-Length' => '0',
            ]);
        }

        return $next($request);
    }
}
