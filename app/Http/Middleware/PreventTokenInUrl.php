<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventTokenInUrl
{
    public function handle(Request $request, Closure $next): mixed
    {
        // Token tidak boleh ada di query string (rawan terekam di server log)
        if ($request->query('token') || $request->query('api_token')) {
            return response()->json([
                'message' => 'Token tidak boleh dikirim melalui URL. Gunakan Authorization header.',
            ], 400);
        }

        return $next($request);
    }
}
