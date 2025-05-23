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
        Schema::table('projects', function (Blueprint $table) {
            // Add quotation_id field to link projects to their originating quotations
            $table->unsignedBigInteger('quotation_id')->nullable()->after('client_id');
            
            // Add foreign key constraint
            $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('set null');
            
            // Add index for better performance
            $table->index('quotation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['quotation_id']);
            
            // Drop index
            $table->dropIndex(['quotation_id']);
            
            // Drop column
            $table->dropColumn('quotation_id');
        });
    }
};