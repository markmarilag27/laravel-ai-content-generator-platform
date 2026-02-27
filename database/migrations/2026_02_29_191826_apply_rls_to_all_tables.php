<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The tables that should be secured via Row Level Security.
     *
     * @var array<int, string>
     */
    protected array $workspaceTables = [
        'workspaces',
        'workspace_user',
        'users',
        'brand_voice_profiles',
        'credit_ledgers',
        'campaigns',
        'campaign_items',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::transaction(function (): void {
            foreach ($this->workspaceTables as $table) {
                // Enable RLS and Force it even for table owners
                DB::statement("ALTER TABLE {$table} ENABLE ROW LEVEL SECURITY;");
                DB::statement("ALTER TABLE {$table} FORCE ROW LEVEL SECURITY;");

                DB::statement("DROP POLICY IF EXISTS tenant_isolation_policy ON {$table};");

                $usingCondition = $this->resolvePolicyCondition($table);

                DB::statement("
                    CREATE POLICY tenant_isolation_policy ON {$table}
                    USING ({$usingCondition})
                    WITH CHECK ({$usingCondition});
                ");
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::transaction(function (): void {
            foreach ($this->workspaceTables as $table) {
                DB::statement("DROP POLICY IF EXISTS tenant_isolation_policy ON {$table};");
                DB::statement("ALTER TABLE {$table} NO FORCE ROW LEVEL SECURITY;");
                DB::statement("ALTER TABLE {$table} DISABLE ROW LEVEL SECURITY;");
            }
        });
    }

    /**
     * Resolves the appropriate SQL condition for RLS based on the table name.
     */
    protected function resolvePolicyCondition(string $table): string
    {
        $currentWorkspace = "NULLIF(NULLIF(current_setting('app.current_workspace_id', TRUE), '0'), '')";
        $currentUser = "NULLIF(NULLIF(current_setting('app.current_user_id', TRUE), '0'), '')";

        // The Super Admin bypass check
        $superAdminCheck = "current_setting('app.is_super_admin', TRUE) = 'true'";

        if (in_array($table, ['workspaces', 'users', 'workspace_user'])) {
            $idColumn = ($table === 'workspaces') ? 'id' : 'workspace_id';

            return "
            {$superAdminCheck}
            OR current_setting('app.current_workspace_id', TRUE) IN ('0', '')
            OR {$idColumn} = ({$currentWorkspace})::bigint
            ".($table === 'users' ? "OR id = ({$currentUser})::bigint" : '');
        }

        return "
        {$superAdminCheck}
            OR workspace_id = ({$currentWorkspace})::bigint
        ";
    }
};
