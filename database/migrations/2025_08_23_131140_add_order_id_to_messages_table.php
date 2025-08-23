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
        Schema::table('messages', function (Blueprint $table) {
            // Check if order_id column doesn't exist before adding it
            if (!Schema::hasColumn('messages', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('project_id')->constrained('product_orders')->nullOnDelete();
                
                // Add indexes for order filtering
                $table->index(['order_id', 'type']);
                $table->index(['user_id', 'order_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropIndex(['order_id', 'type']);
            $table->dropIndex(['user_id', 'order_id']);
            $table->dropColumn('order_id');
        });
    }
};
