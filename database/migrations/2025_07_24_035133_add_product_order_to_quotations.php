<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'product_order_id')) {
                $table->foreignId('product_order_id')->nullable()->after('client_id')
                    ->constrained('product_orders')->nullOnDelete();
            }
            
            if (!Schema::hasColumn('quotations', 'has_products')) {
                $table->boolean('has_products')->default(false)->after('source');
            }
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->index(['product_order_id'], 'idx_quotations_product_order');
            $table->index(['has_products'], 'idx_quotations_has_products');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropIndex('idx_quotations_product_order');
            $table->dropIndex('idx_quotations_has_products');
            $table->dropColumn(['product_order_id', 'has_products']);
        });
    }
};
