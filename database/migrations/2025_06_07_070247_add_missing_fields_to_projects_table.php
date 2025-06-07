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
        Schema::table('projects', function (Blueprint $table) {
            // Basic project information fields
            if (!Schema::hasColumn('projects', 'short_description')) {
                $table->text('short_description')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('projects', 'estimated_completion_date')) {
                $table->date('estimated_completion_date')->nullable()->after('end_date');
            }
            
            if (!Schema::hasColumn('projects', 'budget')) {
                $table->decimal('budget', 15, 2)->nullable()->after('value');
            }
            
            if (!Schema::hasColumn('projects', 'actual_cost')) {
                $table->decimal('actual_cost', 15, 2)->nullable()->after('budget');
            }
            
            // Add priority field if missing
            if (!Schema::hasColumn('projects', 'priority')) {
                $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->after('status');
            }
            
            // Add progress percentage if missing (you have it in notification migrations)
            if (!Schema::hasColumn('projects', 'progress_percentage')) {
                $table->tinyInteger('progress_percentage')->default(0)->after('priority');
            }
            
            // Add is_active field for better project management
            if (!Schema::hasColumn('projects', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('featured');
            }
            
            // Add display_order for sorting
            if (!Schema::hasColumn('projects', 'display_order')) {
                $table->integer('display_order')->default(0)->after('is_active');
            }
            
            // Project methodology and documentation fields
            if (!Schema::hasColumn('projects', 'technologies_used')) {
                $table->json('technologies_used')->nullable()->after('services_used');
            }
            
            if (!Schema::hasColumn('projects', 'team_members')) {
                $table->json('team_members')->nullable()->after('technologies_used');
            }
            
            if (!Schema::hasColumn('projects', 'client_feedback')) {
                $table->text('client_feedback')->nullable()->after('result');
            }
            
            if (!Schema::hasColumn('projects', 'lessons_learned')) {
                $table->text('lessons_learned')->nullable()->after('client_feedback');
            }
            
            // SEO fields for better web presence
            if (!Schema::hasColumn('projects', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('lessons_learned');
            }
            
            if (!Schema::hasColumn('projects', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            
            if (!Schema::hasColumn('projects', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            }
            
            // Add service_id foreign key if missing (for service categorization)
            if (!Schema::hasColumn('projects', 'service_id')) {
                $table->foreignId('service_id')->nullable()->after('category_id')
                    ->constrained('services')->nullOnDelete();
            }
            
            // Add client_name field for cases where client_id is null but we want to store client name
            if (!Schema::hasColumn('projects', 'client_name')) {
                $table->string('client_name')->nullable()->after('client_id');
            }
            
            // Notification tracking fields (sync with notification migration)
            if (!Schema::hasColumn('projects', 'deadline_notification_sent_at')) {
                $table->timestamp('deadline_notification_sent_at')->nullable()->after('meta_keywords');
            }
            
            if (!Schema::hasColumn('projects', 'overdue_notification_sent_at')) {
                $table->timestamp('overdue_notification_sent_at')->nullable()->after('deadline_notification_sent_at');
            }
            
            if (!Schema::hasColumn('projects', 'completion_notification_sent_at')) {
                $table->timestamp('completion_notification_sent_at')->nullable()->after('overdue_notification_sent_at');
            }
            
            if (!Schema::hasColumn('projects', 'client_notified_at')) {
                $table->timestamp('client_notified_at')->nullable()->after('completion_notification_sent_at');
            }
            
            // Soft deletes for better data management
            if (!Schema::hasColumn('projects', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        
        // Add indexes for better performance
        $this->addIndexSafely('projects', 'projects_priority_status_idx', ['priority', 'status']);
        $this->addIndexSafely('projects', 'projects_progress_percentage_idx', ['progress_percentage']);
        $this->addIndexSafely('projects', 'projects_is_active_featured_idx', ['is_active', 'featured']);
        $this->addIndexSafely('projects', 'projects_display_order_idx', ['display_order']);
        $this->addIndexSafely('projects', 'projects_service_id_status_idx', ['service_id', 'status']);
        $this->addIndexSafely('projects', 'projects_estimated_completion_idx', ['estimated_completion_date']);
        $this->addIndexSafely('projects', 'projects_budget_idx', ['budget']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes first
        $this->dropIndexSafely('projects', 'projects_priority_status_idx');
        $this->dropIndexSafely('projects', 'projects_progress_percentage_idx');
        $this->dropIndexSafely('projects', 'projects_is_active_featured_idx');
        $this->dropIndexSafely('projects', 'projects_display_order_idx');
        $this->dropIndexSafely('projects', 'projects_service_id_status_idx');
        $this->dropIndexSafely('projects', 'projects_estimated_completion_idx');
        $this->dropIndexSafely('projects', 'projects_budget_idx');
        
        Schema::table('projects', function (Blueprint $table) {
            // Drop columns that we added (only the safe ones)
            $columnsToCheck = [
                'short_description',
                'estimated_completion_date',
                'budget',
                'actual_cost',
                'priority',
                'progress_percentage',
                'is_active',
                'display_order',
                'technologies_used',
                'team_members',
                'client_feedback',
                'lessons_learned',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'client_name',
                'deadline_notification_sent_at',
                'overdue_notification_sent_at',
                'completion_notification_sent_at',
                'client_notified_at',
                'deleted_at'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('projects', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            // Drop foreign key for service_id
            if (Schema::hasColumn('projects', 'service_id')) {
                $table->dropConstrainedForeignId('service_id');
            }
        });
    }
    
    /**
     * Add index safely using raw SQL
     */
    private function addIndexSafely(string $table, string $indexName, array $columns): void
    {
        try {
            $columnsList = implode(',', array_map(function($col) { return "`{$col}`"; }, $columns));
            $sql = "CREATE INDEX `{$indexName}` ON `{$table}` ({$columnsList})";
            
            DB::statement($sql);
        } catch (\Exception $e) {
            // Index likely already exists, skip
            if (!str_contains($e->getMessage(), 'Duplicate key name')) {
                throw $e;
            }
        }
    }
    
    /**
     * Drop index safely using raw SQL
     */
    private function dropIndexSafely(string $table, string $indexName): void
    {
        try {
            $sql = "DROP INDEX `{$indexName}` ON `{$table}`";
            DB::statement($sql);
        } catch (\Exception $e) {
            // Index likely doesn't exist, skip
            if (!str_contains($e->getMessage(), "check that column/key exists")) {
                throw $e;
            }
        }
    }
};