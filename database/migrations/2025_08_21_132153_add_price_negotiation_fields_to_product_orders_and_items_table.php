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
            $table->boolean('needs_negotiation')->default(false)->after('status');
            $table->text('negotiation_message')->nullable()->after('needs_negotiation');
            $table->decimal('requested_total', 15, 2)->nullable()->after('total_amount');
            $table->enum('negotiation_status', ['pending', 'in_progress', 'accepted', 'rejected', 'completed'])
                  ->nullable()->after('requested_total');
            $table->timestamp('negotiation_requested_at')->nullable()->after('updated_at');
            $table->timestamp('negotiation_responded_at')->nullable()->after('negotiation_requested_at');
        });

        Schema::table('product_order_items', function (Blueprint $table) {
            $table->decimal('requested_unit_price', 15, 2)->nullable()->after('price');
            $table->decimal('requested_total_price', 15, 2)->nullable()->after('requested_unit_price');
            $table->text('price_justification')->nullable()->after('specifications');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_orders', function (Blueprint $table) {
            $table->dropColumn([
                'needs_negotiation',
                'negotiation_message',
                'requested_total',
                'negotiation_status',
                'negotiation_requested_at',
                'negotiation_responded_at'
            ]);
        });

        Schema::table('product_order_items', function (Blueprint $table) {
            $table->dropColumn([
                'requested_unit_price',
                'requested_total_price',
                'price_justification'
            ]);
        });
    }
};
