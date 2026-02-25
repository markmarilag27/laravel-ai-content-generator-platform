<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\Traits\Database\InteractsWithPostgresRls;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class WorkspaceScopeMiddleware
{
    use InteractsWithPostgresRls;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var ?User $user */
        $user = Auth::user();

        if ($request->is('telescope*', 'horizon*', 'nova*')) {
            return $next($request);
        }

        $this->setPostgresContext(
            userId: $user?->id,
            workspaceId: $user?->workspace_id
        );

        return $next($request);
    }
}
