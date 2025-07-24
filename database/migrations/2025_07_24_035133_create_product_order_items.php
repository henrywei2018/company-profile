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
            $table->foreignId('product_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            
            // Essential item info
            $table->integer('quantity');
            $table->decimal('price', 15, 2); // Price at time of order
            $table->decimal('total', 15, 2); // quantity * price
            
            // Optional specifications
            $table->text('specifications')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Simple indexes
            $table->index(['product_order_id']);
            $table->index(['product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_order_items');
    }
};
