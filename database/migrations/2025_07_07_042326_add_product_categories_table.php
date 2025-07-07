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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            
            // Self-referencing for hierarchical categories
            $table->foreignId('parent_id')->nullable()->constrained('product_categories')->onDelete('set null');
            
            // Link to service categories for business logic
            $table->foreignId('service_category_id')->nullable()->constrained('service_categories')->onDelete('set null');
            
            // Status and ordering
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['parent_id']);
            $table->index(['service_category_id']);
            $table->index(['is_active', 'sort_order']);
            $table->index(['parent_id', 'is_active', 'sort_order'], 'idx_parent_active_sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};