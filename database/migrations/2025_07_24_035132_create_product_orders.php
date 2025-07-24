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
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            
            $table->enum('status', [
                'pending', 'confirmed', 'processing', 'ready', 'delivered', 'completed'
            ])->default('pending');
            
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('delivery_address');
            $table->date('needed_date')->nullable();
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            $table->foreignId('quotation_id')->nullable()->constrained('quotations')->nullOnDelete();
            $table->boolean('needs_quotation')->default(false);
            
            $table->timestamps();
        });

        Schema::table('product_orders', function (Blueprint $table) {
            $table->index(['client_id', 'status', 'created_at'], 'idx_po_client_status_date');
            $table->index(['status'], 'idx_po_status');
            $table->index(['quotation_id'], 'idx_po_quotation');
            $table->index(['needs_quotation'], 'idx_po_needs_quotation');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_orders');
    }
};
