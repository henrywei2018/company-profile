<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Simple purchase behavior - just 2 options
            if (!Schema::hasColumn('products', 'purchase_type')) {
                $table->enum('purchase_type', ['direct', 'quote'])->default('direct')->after('price_type');
            }
            
            // Minimum order only (most important)
            if (!Schema::hasColumn('products', 'min_quantity')) {
                $table->integer('min_quantity')->default(1)->after('stock_quantity');
            }
            
            // Simple lead time
            if (!Schema::hasColumn('products', 'lead_days')) {
                $table->integer('lead_days')->nullable()->after('min_quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['purchase_type', 'min_quantity', 'lead_days']);
        });
    }
};
