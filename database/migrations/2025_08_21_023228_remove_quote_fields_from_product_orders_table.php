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
        Schema::table('product_orders', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign('product_orders_quotation_id_foreign');
            
            $table->dropColumn([
                'negotiation_notes',
                'negotiated_total',
                'negotiation_completed_at',
                'quotation_id',
                'needs_quotation'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_orders', function (Blueprint $table) {
            $table->text('negotiation_notes')->nullable();
            $table->decimal('negotiated_total', 15, 2)->nullable();
            $table->timestamp('negotiation_completed_at')->nullable();
            $table->unsignedBigInteger('quotation_id')->nullable();
            $table->boolean('needs_quotation')->default(false);
            
            // Recreate foreign key constraint
            $table->foreign('quotation_id')->references('id')->on('quotations');
        });
    }
};
