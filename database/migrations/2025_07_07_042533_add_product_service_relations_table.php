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
        Schema::create('product_service_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->enum('relation_type', ['compatible', 'recommended', 'required'])->default('compatible');
            $table->timestamps();
            
            // Unique constraint to prevent duplicate relations
            $table->unique(['product_id', 'service_id'], 'unique_product_service');
            
            // Indexes
            $table->index(['product_id', 'relation_type']);
            $table->index(['service_id', 'relation_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_service_relations');
    }
};