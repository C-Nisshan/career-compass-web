<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CheckRoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = auth('api')->user();

        Log::info('CheckRoleMiddleware: Processing request', [
            'path' => $request->path(),
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'user_role' => $user?->role?->value,
            'required_roles' => $roles,
            'auth_check' => auth('api')->check() ? 'authenticated' : 'unauthenticated',
        ]);

        if (!$user || !in_array($user->role->value ?? '', $roles)) {
            Log::warning('CheckRoleMiddleware: Access denied', [
                'user_role' => $user?->role?->value ?? 'none',
                'required_roles' => $roles,
            ]);
            throw new AccessDeniedHttpException('Unauthorized access');
        }

        Log::info('CheckRoleMiddleware: Access granted', [
            'user_role' => $user->role->value,
            'required_roles' => $roles,
        ]);

        return $next($request);
    }
}
