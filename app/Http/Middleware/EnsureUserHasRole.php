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

        // If the route allows 'donor' and the user has the is_donor flag set to true, allow them.
        if (in_array('donor', $roles, true) && $user->is_donor) {
            return $next($request);
        }

        // roles passed from route middleware are strings: 'admin', 'org_admin', 'donor', 'recipient'
        if (!in_array($roleValue, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}