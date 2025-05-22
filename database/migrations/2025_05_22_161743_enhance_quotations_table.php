<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            // Add new tracking fields
            $table->timestamp('reviewed_at')->nullable()->after('admin_notes');
            $table->timestamp('approved_at')->nullable()->after('reviewed_at');
            $table->timestamp('last_communication_at')->nullable()->after('approved_at');
            
            // Add estimation fields
            $table->string('estimated_cost')->nullable()->after('budget_range');
            $table->string('estimated_timeline')->nullable()->after('estimated_cost');
            
            // Add internal notes (separate from admin_notes)
            $table->text('internal_notes')->nullable()->after('admin_notes');
            
            // Add priority field
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->after('status');
            
            // Add source tracking
            $table->string('source')->nullable()->after('priority'); // website, phone, email, referral
            
            // Add indexes for better performance
            $table->index(['status', 'created_at']);
            $table->index(['client_id', 'status']);
            $table->index(['service_id', 'status']);
            $table->index('priority');
            $table->index('reviewed_at');
            $table->index('approved_at');
        });
    }

    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['client_id', 'status']);
            $table->dropIndex(['service_id', 'status']);
            $table->dropIndex(['priority']);
            $table->dropIndex(['reviewed_at']);
            $table->dropIndex(['approved_at']);
            
            $table->dropColumn([
                'reviewed_at',
                'approved_at', 
                'last_communication_at',
                'estimated_cost',
                'estimated_timeline',
                'internal_notes',
                'priority',
                'source'
            ]);
        });
    }
};