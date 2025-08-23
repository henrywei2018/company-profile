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
            $table->boolean('delivery_confirmed_by_client')->default(false)->after('payment_verified_at');
            $table->timestamp('delivery_confirmed_at')->nullable()->after('delivery_confirmed_by_client');
            $table->text('client_delivery_notes')->nullable()->after('delivery_confirmed_at');
            $table->boolean('delivery_disputed')->default(false)->after('client_delivery_notes');
            $table->text('dispute_reason')->nullable()->after('delivery_disputed');
            $table->timestamp('dispute_reported_at')->nullable()->after('dispute_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_confirmed_by_client',
                'delivery_confirmed_at', 
                'client_delivery_notes',
                'delivery_disputed',
                'dispute_reason',
                'dispute_reported_at'
            ]);
        });
    }
};
