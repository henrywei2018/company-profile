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
            $table->text('negotiation_notes')->nullable()->after('notes');
            $table->decimal('negotiated_total', 15, 2)->nullable()->after('total_amount');
            $table->timestamp('negotiation_completed_at')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_orders', function (Blueprint $table) {
            $table->dropColumn([
                'negotiation_notes',
                'negotiated_total', 
                'negotiation_completed_at'
            ]);
        });
    }
};
