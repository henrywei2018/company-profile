<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'purchase_type')) {
                $table->enum('purchase_type', ['direct', 'quote'])->default('direct')->after('price_type');
            }
            
            if (!Schema::hasColumn('products', 'min_quantity')) {
                $table->integer('min_quantity')->default(1)->after('stock_quantity');
            }
            
            if (!Schema::hasColumn('products', 'lead_days')) {
                $table->integer('lead_days')->nullable()->after('min_quantity');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['purchase_type'], 'idx_products_purchase_type');
            $table->index(['purchase_type', 'status'], 'idx_products_purchase_status');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_purchase_type');
            $table->dropIndex('idx_products_purchase_status');
            $table->dropColumn(['purchase_type', 'min_quantity', 'lead_days']);
        });
    }
};
