<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for token in Authorization header first
        if ($request->bearerToken()) {
            return $next($request);
        }

        // Fallback to cookie check
        if ($request->cookie('remember_token')) {
            $request->headers->set(
                'Authorization',
                'Bearer ' . $request->cookie('remember_token')
            );
        }
        return $next($request);
    }
}
