<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_order_id')->constrained('product_orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('total', 15, 2);
            $table->text('specifications')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });

        Schema::table('product_order_items', function (Blueprint $table) {
            $table->index(['product_order_id'], 'idx_poi_order');
            $table->index(['product_id'], 'idx_poi_product');
            $table->index(['product_order_id', 'product_id'], 'idx_poi_order_product');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_order_items');
    }
};
