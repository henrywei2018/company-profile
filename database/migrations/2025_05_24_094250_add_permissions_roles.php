<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add additional fields to permissions table for better management
        if (Schema::hasTable('permissions')) {
            Schema::table('permissions', function (Blueprint $table) {
                if (!Schema::hasColumn('permissions', 'description')) {
                    $table->text('description')->nullable()->after('guard_name');
                }
                if (!Schema::hasColumn('permissions', 'module')) {
                    $table->string('module')->nullable()->after('description');
                }
                if (!Schema::hasColumn('permissions', 'is_system')) {
                    $table->boolean('is_system')->default(false)->after('module');
                }
                if (!Schema::hasColumn('permissions', 'created_at')) {
                    $table->timestamps();
                }
            });
        }

        // Add additional fields to roles table for better management
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                if (!Schema::hasColumn('roles', 'description')) {
                    $table->text('description')->nullable()->after('guard_name');
                }
                if (!Schema::hasColumn('roles', 'is_system')) {
                    $table->boolean('is_system')->default(false)->after('description');
                }
                if (!Schema::hasColumn('roles', 'color')) {
                    $table->string('color')->nullable()->after('is_system');
                }
                if (!Schema::hasColumn('roles', 'created_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('permissions')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->dropColumn(['description', 'module', 'is_system', 'created_at', 'updated_at']);
            });
        }

        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn(['description', 'is_system', 'color', 'created_at', 'updated_at']);
            });
        }
    }
};