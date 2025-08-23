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
        Schema::table('product_orders', function (Blueprint $table) {
            $table->enum('dispute_status', ['reported', 'acknowledged', 'resolved', 'accepted_by_client'])->default('reported')->after('dispute_reported_at');
            $table->text('admin_dispute_response')->nullable()->after('dispute_status');
            $table->timestamp('admin_responded_at')->nullable()->after('admin_dispute_response');
            $table->text('client_dispute_feedback')->nullable()->after('admin_responded_at');
            $table->timestamp('client_responded_at')->nullable()->after('client_dispute_feedback');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_orders', function (Blueprint $table) {
            $table->dropColumn([
                'dispute_status',
                'admin_dispute_response',
                'admin_responded_at',
                'client_dispute_feedback',
                'client_responded_at'
            ]);
        });
    }
};
