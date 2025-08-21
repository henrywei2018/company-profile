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
        Schema::table('product_order_items', function (Blueprint $table) {
            $table->decimal('proposed_unit_price', 15, 2)->nullable()->after('total');
            $table->decimal('proposed_total_price', 15, 2)->nullable()->after('proposed_unit_price');
            $table->decimal('negotiated_unit_price', 15, 2)->nullable()->after('proposed_total_price');
            $table->decimal('negotiated_total_price', 15, 2)->nullable()->after('negotiated_unit_price');
            $table->decimal('final_unit_price', 15, 2)->nullable()->after('negotiated_total_price');
            $table->decimal('final_total_price', 15, 2)->nullable()->after('final_unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_order_items', function (Blueprint $table) {
            $table->dropColumn([
                'proposed_unit_price',
                'proposed_total_price',
                'negotiated_unit_price',
                'negotiated_total_price',
                'final_unit_price',
                'final_total_price'
            ]);
        });
    }
};
