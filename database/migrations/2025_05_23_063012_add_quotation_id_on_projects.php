<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This migration updates the projects table to properly reference quotations
        // Since quotations table is created after projects, we need to add the foreign key here
        if (!Schema::hasColumn('projects', 'quotation_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->foreignId('quotation_id')->nullable()->after('client_id')
                    ->constrained('quotations')->nullOnDelete();
                $table->index('quotation_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('projects', 'quotation_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropConstrainedForeignId('quotation_id');
            });
        }
    }
};