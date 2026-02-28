<?php

declare(strict_types=1);

namespace App\Jobs\Middleware;

use App\Traits\Database\InteractsWithPostgresRls;
use Illuminate\Support\Facades\DB;

class TenantScopeJobMiddleware
{
    use InteractsWithPostgresRls;

    public function handle(mixed $job, callable $next): void
    {
        DB::transaction(function () use ($job, $next) {
            $workspaceId = $job->workspaceId ?? null;
            $userId = $job->userId ?? null;

            $this->setPostgresContext($userId, $workspaceId);

            $next($job);
        });
    }
}
