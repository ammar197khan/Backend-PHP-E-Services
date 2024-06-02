<?php

namespace Spatie\Permission\Middlewares;

use Closure;
use Spatie\Permission\Exceptions\UnauthorizedException;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $permission)
    {
        if (!get_auth_guard())
        {
            throw UnauthorizedException::notLoggedIn();
        }

        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        foreach ($permissions as $permission)
        {
            if (get_auth_guard()->hasPermissionTo($permission))
            {
                return $next($request);
            }
        }

        throw UnauthorizedException::forPermissions($permissions);
    }
}
