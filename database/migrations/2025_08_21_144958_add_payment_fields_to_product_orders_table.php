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
            $table->enum('payment_status', ['pending', 'proof_uploaded', 'verified', 'rejected'])
                  ->default('pending')->after('status');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->string('payment_proof')->nullable()->after('payment_method');
            $table->text('payment_notes')->nullable()->after('payment_proof');
            $table->timestamp('payment_uploaded_at')->nullable()->after('negotiation_responded_at');
            $table->timestamp('payment_verified_at')->nullable()->after('payment_uploaded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_method',
                'payment_proof', 
                'payment_notes',
                'payment_uploaded_at',
                'payment_verified_at'
            ]);
        });
    }
};
