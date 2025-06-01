<?php
// File: app/Console/Commands/CheckNotificationsTable.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CheckNotificationsTable extends Command
{
    protected $signature = 'notifications:check-table';
    protected $description = 'Check and display notifications table structure';

    public function handle()
    {
        $this->info('🔍 Checking notifications table structure...');
        
        if (!Schema::hasTable('notifications')) {
            $this->error('❌ Notifications table does not exist!');
            $this->info('💡 Run: php artisan notifications:table');
            $this->info('💡 Then: php artisan migrate');
            return 1;
        }

        $this->info('✅ Notifications table exists');
        
        // Check columns
        $columns = $this->getTableColumns('notifications');
        $this->info('📋 Current columns:');
        foreach ($columns as $column) {
            $this->line("  - {$column->Field} ({$column->Type})");
        }
        
        // Check indexes
        $indexes = $this->getTableIndexes('notifications');
        $this->info('🔗 Current indexes:');
        foreach ($indexes as $index) {
            $this->line("  - {$index->Key_name} on ({$index->Column_name})");
        }
        
        // Check required structure
        $this->checkRequiredStructure($columns, $indexes);
        
        return 0;
    }

    private function getTableColumns(string $table): array
    {
        return DB::select("DESCRIBE {$table}");
    }

    private function getTableIndexes(string $table): array
    {
        return DB::select("SHOW INDEX FROM {$table}");
    }

    private function checkRequiredStructure(array $columns, array $indexes): void
    {
        $requiredColumns = ['id', 'type', 'notifiable_type', 'notifiable_id', 'data', 'read_at', 'created_at', 'updated_at'];
        $existingColumns = collect($columns)->pluck('Field')->toArray();
        
        $this->info('🔍 Checking required columns:');
        foreach ($requiredColumns as $required) {
            if (in_array($required, $existingColumns)) {
                $this->line("  ✅ {$required}");
            } else {
                $this->error("  ❌ {$required} - MISSING");
            }
        }
        
        $requiredIndexes = [
            'notifications_notifiable_type_notifiable_id_index',
            'notifications_read_at_index',
            'notifications_created_at_index'
        ];
        $existingIndexes = collect($indexes)->pluck('Key_name')->unique()->toArray();
        
        $this->info('🔍 Checking required indexes:');
        foreach ($requiredIndexes as $required) {
            if (in_array($required, $existingIndexes)) {
                $this->line("  ✅ {$required}");
            } else {
                $this->warn("  ⚠️ {$required} - MISSING");
            }
        }
    }
}