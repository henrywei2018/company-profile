<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Add project_id column if it doesn't exist
            if (!Schema::hasColumn('messages', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable()->after('user_id');
                $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            }
            
            // Add priority column if it doesn't exist
            if (!Schema::hasColumn('messages', 'priority')) {
                $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->after('type');
            }
            
            // Add index for better performance
            if (!Schema::hasColumn('messages', 'project_id')) {
                $table->index(['project_id', 'created_at']);
            }
            if (!Schema::hasColumn('messages', 'priority')) {
                $table->index(['priority', 'is_read']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Drop foreign key and column
            if (Schema::hasColumn('messages', 'project_id')) {
                $table->dropForeign(['project_id']);
                $table->dropColumn('project_id');
            }
            
            // Drop priority column
            if (Schema::hasColumn('messages', 'priority')) {
                $table->dropColumn('priority');
            }
        });
    }
};