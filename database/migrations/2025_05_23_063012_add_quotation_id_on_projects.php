<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for basic quotation-project integration
     */
    public function up(): void
    {
        // 1. Add quotation_id to projects table if not exists
        if (!Schema::hasColumn('projects', 'quotation_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->foreignId('quotation_id')->nullable()->after('client_id')
                    ->constrained('quotations')->nullOnDelete();
                $table->index('quotation_id');
            });
        }

        // 2. Add project tracking to quotations (just to know if project was created)
        if (!Schema::hasColumn('quotations', 'project_created')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->boolean('project_created')->default(false)->after('client_approved_at');
                $table->timestamp('project_created_at')->nullable()->after('project_created');
            });
        }

        // 3. Add quotation number for better tracking
        if (!Schema::hasColumn('quotations', 'quotation_number')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->string('quotation_number')->unique()->nullable()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove quotation_id from projects
        if (Schema::hasColumn('projects', 'quotation_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropConstrainedForeignId('quotation_id');
            });
        }

        // Remove columns from quotations table
        Schema::table('quotations', function (Blueprint $table) {
            if (Schema::hasColumn('quotations', 'project_created')) {
                $table->dropColumn('project_created');
            }
            if (Schema::hasColumn('quotations', 'project_created_at')) {
                $table->dropColumn('project_created_at');
            }
            if (Schema::hasColumn('quotations', 'quotation_number')) {
                $table->dropColumn('quotation_number');
            }
        });
    }
};