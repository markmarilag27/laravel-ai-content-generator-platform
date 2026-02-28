<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ElevateRlsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow Laravel to read the system tables for Auth
        DB::statement("SET app.is_super_admin = 'true'");

        $response = $next($request);

        // Ensure the vault is closed when the response goes back out
        DB::statement("SET app.is_super_admin = 'false'");

        return $response;
    }
}
