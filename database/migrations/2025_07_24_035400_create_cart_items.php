<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            
            // Support both logged in and guest users
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable(); // For guest users
            
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity');
            $table->text('specifications')->nullable();
            
            $table->timestamps();
            
            // Cleanup old carts automatically
            $table->index(['user_id']);
            $table->index(['session_id']);
            $table->index(['created_at']); // For cleanup
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
