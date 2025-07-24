<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            // Simple back-reference to product order
            if (!Schema::hasColumn('quotations', 'product_order_id')) {
                $table->foreignId('product_order_id')->nullable()->after('client_id')
                    ->constrained('product_orders')->nullOnDelete();
            }
            
            // Simple type flag
            if (!Schema::hasColumn('quotations', 'has_products')) {
                $table->boolean('has_products')->default(false)->after('source');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['product_order_id', 'has_products']);
        });
    }
};
