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
            // Remove all dispute-related fields
            $table->dropColumn([
                'delivery_confirmed_by_client',
                'delivery_confirmed_at',
                'client_delivery_notes',
                'delivery_disputed',
                'dispute_reason',
                'dispute_reported_at',
                'dispute_status',
                'admin_dispute_response',
                'admin_responded_at',
                'client_dispute_feedback',
                'client_responded_at',
                'dispute_images'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_orders', function (Blueprint $table) {
            // Client confirmation fields
            $table->boolean('delivery_confirmed_by_client')->default(false)->after('admin_notes');
            $table->timestamp('delivery_confirmed_at')->nullable()->after('delivery_confirmed_by_client');
            $table->text('client_delivery_notes')->nullable()->after('delivery_confirmed_at');
            
            // Dispute fields
            $table->boolean('delivery_disputed')->default(false)->after('client_delivery_notes');
            $table->text('dispute_reason')->nullable()->after('delivery_disputed');
            $table->timestamp('dispute_reported_at')->nullable()->after('dispute_reason');
            
            // Dispute interaction fields
            $table->enum('dispute_status', ['reported', 'acknowledged', 'resolved', 'accepted_by_client'])->nullable()->after('dispute_reported_at');
            $table->text('admin_dispute_response')->nullable()->after('dispute_status');
            $table->timestamp('admin_responded_at')->nullable()->after('admin_dispute_response');
            $table->text('client_dispute_feedback')->nullable()->after('admin_responded_at');
            $table->timestamp('client_responded_at')->nullable()->after('client_dispute_feedback');
            
            // Dispute images
            $table->json('dispute_images')->nullable()->after('client_responded_at');
        });
    }
};
