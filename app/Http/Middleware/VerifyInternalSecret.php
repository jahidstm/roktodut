<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyInternalSecret
{
    public function handle(Request $request, Closure $next)
    {
        $expected = (string) env('INTERNAL_WEBHOOK_SECRET', 'ROKTODUT_INTERNAL_SECRET');
        $provided = (string) $request->header('X-Internal-Secret', '');

        if ($expected === '' || $provided === '' || !hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Invalid internal secret.'], 401);
        }

        return $next($request);
    }
}
