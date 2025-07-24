<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            
            // Client info - support both registered and guest
            $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('client_name');
            $table->string('client_email');
            $table->string('client_phone')->nullable();
            
            // Simple status flow
            $table->enum('status', ['pending', 'confirmed', 'processing', 'ready', 'delivered', 'completed'])->default('pending');
            
            // Financial - keep it simple
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('delivery_address');
            $table->date('needed_date')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            // Link to existing systems
            $table->foreignId('quotation_id')->nullable()->constrained('quotations')->nullOnDelete();
            $table->boolean('needs_quotation')->default(false);
            
            $table->timestamps();
            
            // Essential indexes only
            $table->index(['status', 'created_at']);
            $table->index(['client_id']);
            $table->index(['client_email']);
            $table->index(['quotation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_orders');
    }
};
