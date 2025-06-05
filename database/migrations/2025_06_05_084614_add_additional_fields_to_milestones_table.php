<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('project_milestones', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('project_milestones', 'progress_percent')) {
                $table->integer('progress_percent')->default(0)->after('status');
            }
            
            if (!Schema::hasColumn('project_milestones', 'estimated_hours')) {
                $table->decimal('estimated_hours', 8, 2)->nullable()->after('progress_percent');
            }
            
            if (!Schema::hasColumn('project_milestones', 'actual_hours')) {
                $table->decimal('actual_hours', 8, 2)->nullable()->after('estimated_hours');
            }
            
            if (!Schema::hasColumn('project_milestones', 'priority')) {
                $table->enum('priority', ['low', 'normal', 'high', 'critical'])->default('normal')->after('actual_hours');
            }
            
            if (!Schema::hasColumn('project_milestones', 'dependencies')) {
                $table->json('dependencies')->nullable()->after('priority');
            }
            
            if (!Schema::hasColumn('project_milestones', 'notes')) {
                $table->text('notes')->nullable()->after('dependencies');
            }
            
            if (!Schema::hasColumn('project_milestones', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('notes');
            }
            
            if (!Schema::hasColumn('project_milestones', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Add indexes safely using raw SQL with IF NOT EXISTS equivalent
        $this->addIndexSafely('project_milestones', 'project_id_status_idx', ['project_id', 'status']);
        $this->addIndexSafely('project_milestones', 'project_id_due_date_idx', ['project_id', 'due_date']);
        $this->addIndexSafely('project_milestones', 'project_id_sort_order_idx', ['project_id', 'sort_order']);
        $this->addIndexSafely('project_milestones', 'priority_idx', ['priority']);
        $this->addIndexSafely('project_milestones', 'status_due_date_idx', ['status', 'due_date']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes first
        $this->dropIndexSafely('project_milestones', 'project_id_status_idx');
        $this->dropIndexSafely('project_milestones', 'project_id_due_date_idx');
        $this->dropIndexSafely('project_milestones', 'project_id_sort_order_idx');
        $this->dropIndexSafely('project_milestones', 'priority_idx');
        $this->dropIndexSafely('project_milestones', 'status_due_date_idx');

        Schema::table('project_milestones', function (Blueprint $table) {
            $table->dropColumn([
                'progress_percent',
                'estimated_hours', 
                'actual_hours',
                'priority',
                'dependencies',
                'notes',
                'sort_order',
                'deleted_at'
            ]);
        });
    }

    /**
     * Add index safely - only if it doesn't exist
     */
    private function addIndexSafely(string $table, string $indexName, array $columns): void
    {
        $fullIndexName = "{$table}_{$indexName}";
        
        try {
            $columnsList = implode(',', array_map(function($col) { return "`{$col}`"; }, $columns));
            $sql = "CREATE INDEX `{$fullIndexName}` ON `{$table}` ({$columnsList})";
            
            DB::statement($sql);
        } catch (\Exception $e) {
            // Index likely already exists, skip
            if (!str_contains($e->getMessage(), 'Duplicate key name')) {
                throw $e;
            }
        }
    }

    /**
     * Drop index safely - only if it exists
     */
    private function dropIndexSafely(string $table, string $indexName): void
    {
        $fullIndexName = "{$table}_{$indexName}";
        
        try {
            $sql = "DROP INDEX `{$fullIndexName}` ON `{$table}`";
            DB::statement($sql);
        } catch (\Exception $e) {
            // Index likely doesn't exist, skip
            if (!str_contains($e->getMessage(), "check that column/key exists")) {
                throw $e;
            }
        }
    }
};