<?php

declare(strict_types=1);

namespace App\Traits\Database;

use Illuminate\Support\Facades\DB;

trait InteractsWithPostgresRls
{
    /**
     * Sets the Postgres session variables for RLS using SET LOCAL.
     * Use SET LOCAL to ensure settings don't bleed into the next request
     * if connections are pooled.
     */
    protected function setPostgresContext(?int $userId = null, ?int $workspaceId = null, bool $isSuperAdmin = false): void
    {
        // Default to '0' string for RLS policies
        $userId = $userId ?? '0';
        $workspaceId = $workspaceId ?? '0';
        $isAdmin = $isSuperAdmin ? 'true' : 'false';

        DB::statement("SET LOCAL app.current_user_id = '{$userId}'");
        DB::statement("SET LOCAL app.current_workspace_id = '{$workspaceId}'");
        DB::statement("SET LOCAL app.is_super_admin = '{$isAdmin}'");
    }
}
