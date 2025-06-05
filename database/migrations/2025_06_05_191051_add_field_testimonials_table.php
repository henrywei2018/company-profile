<?php

// File: database/migrations/xxxx_xx_xx_update_testimonials_table_add_missing_fields.php
// Fixed version without Doctrine dependency

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
        // Update testimonials table - add only missing critical fields
        Schema::table('testimonials', function (Blueprint $table) {
            // Add project_id foreign key if it doesn't exist
            if (!Schema::hasColumn('testimonials', 'project_id')) {
                $table->foreignId('project_id')->nullable()->after('id')
                    ->constrained('projects')->nullOnDelete();
            }
            
            // Add client_id foreign key if it doesn't exist  
            if (!Schema::hasColumn('testimonials', 'client_id')) {
                $table->foreignId('client_id')->nullable()->after('project_id')
                    ->constrained('users')->nullOnDelete();
            }
            
            // Add essential fields that might be missing
            if (!Schema::hasColumn('testimonials', 'client_name')) {
                $table->string('client_name')->after('project_id');
            }
            
            if (!Schema::hasColumn('testimonials', 'client_position')) {
                $table->string('client_position')->nullable()->after('client_name');
            }
            
            if (!Schema::hasColumn('testimonials', 'client_company')) {
                $table->string('client_company')->nullable()->after('client_position');
            }
            
            if (!Schema::hasColumn('testimonials', 'content')) {
                $table->text('content')->after('client_company');
            }
            
            if (!Schema::hasColumn('testimonials', 'rating')) {
                $table->integer('rating')->default(5)->after('content');
            }
            
            if (!Schema::hasColumn('testimonials', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('rating');
            }
            
            if (!Schema::hasColumn('testimonials', 'featured')) {
                $table->boolean('featured')->default(false)->after('is_active');
            }
            
            // Add status field for workflow if missing
            if (!Schema::hasColumn('testimonials', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected', 'featured'])->default('pending')->after('featured');
            }
            
            // Add approval tracking if missing
            if (!Schema::hasColumn('testimonials', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('status');
            }
            
            // Add admin notes if missing
            if (!Schema::hasColumn('testimonials', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('approved_at');
            }
            
            // Add notification tracking fields if missing
            if (!Schema::hasColumn('testimonials', 'approval_notification_sent_at')) {
                $table->timestamp('approval_notification_sent_at')->nullable()->after('admin_notes');
            }
            
            if (!Schema::hasColumn('testimonials', 'featured_notification_sent_at')) {
                $table->timestamp('featured_notification_sent_at')->nullable()->after('approval_notification_sent_at');
            }
        });
        
        // Add indexes using raw SQL (Laravel 12 compatible way)
        $this->addIndexSafely('testimonials', 'testimonials_project_id_is_active_index', ['project_id', 'is_active']);
        $this->addIndexSafely('testimonials', 'testimonials_featured_is_active_index', ['featured', 'is_active']);
        $this->addIndexSafely('testimonials', 'testimonials_status_index', ['status']);
        $this->addIndexSafely('testimonials', 'testimonials_rating_is_active_index', ['rating', 'is_active']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes first
        $this->dropIndexSafely('testimonials', 'testimonials_project_id_is_active_index');
        $this->dropIndexSafely('testimonials', 'testimonials_featured_is_active_index');
        $this->dropIndexSafely('testimonials', 'testimonials_status_index');
        $this->dropIndexSafely('testimonials', 'testimonials_rating_is_active_index');
        
        Schema::table('testimonials', function (Blueprint $table) {
            // Only drop columns that we added in this migration
            $columnsToCheck = [
                'approval_notification_sent_at',
                'featured_notification_sent_at', 
                'admin_notes',
                'approved_at',
                'status'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('testimonials', $column)) {
                    $table->dropColumn($column);
                }
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
                // Re-throw if it's not a duplicate key error
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
                // Re-throw if it's not a "doesn't exist" error
                throw $e;
            }
        }
    }
};