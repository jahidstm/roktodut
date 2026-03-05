<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        $role = $user->role; // can be UserRole|string|null depending on casts

        $roleValue = $role instanceof UserRole ? $role->value : (string) ($role ?? '');

        // roles passed from route middleware are strings: 'admin', 'org_admin', 'donor', 'recipient'
        if (!in_array($roleValue, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}