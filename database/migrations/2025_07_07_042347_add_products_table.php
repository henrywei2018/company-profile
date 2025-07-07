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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique()->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            
            // Categorization & Relationships
            $table->foreignId('product_category_id')->nullable()->constrained('product_categories')->onDelete('set null');
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('set null');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            
            // Pricing Information
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->string('currency', 3)->default('IDR');
            $table->enum('price_type', ['fixed', 'quote', 'contact'])->default('fixed');
            
            // Inventory Management
            $table->integer('stock_quantity')->default(0);
            $table->boolean('manage_stock')->default(false);
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'on_backorder'])->default('in_stock');
            
            // Media
            $table->string('featured_image')->nullable();
            $table->json('gallery')->nullable(); // Store multiple image paths
            
            // Product Specifications
            $table->json('specifications')->nullable(); // General specs (color, material, etc.)
            $table->json('technical_specs')->nullable(); // Technical specifications
            $table->json('dimensions')->nullable(); // Length, width, height
            $table->decimal('weight', 8, 2)->nullable(); // Weight in kg
            
            // Status & Settings
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['product_category_id']);
            $table->index(['service_id']);
            $table->index(['sku']);
            $table->index(['status', 'is_active']);
            $table->index(['is_featured']);
            $table->index(['price']);
            $table->index(['stock_status']);
            $table->index(['brand']);
            $table->index(['status', 'is_active', 'sort_order'], 'idx_status_active_sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};