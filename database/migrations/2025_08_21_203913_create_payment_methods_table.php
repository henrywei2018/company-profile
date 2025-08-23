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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "BCA Transfer", "GoPay"
            $table->string('type'); // "bank_transfer", "e_wallet", "credit_card"
            $table->string('account_number')->nullable(); // Bank account number
            $table->string('account_name')->nullable(); // Account holder name
            $table->string('bank_code')->nullable(); // Bank code for transfers
            $table->string('phone_number')->nullable(); // For e-wallets
            $table->text('instructions')->nullable(); // Payment instructions
            $table->string('logo')->nullable(); // Logo/icon file path
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('additional_info')->nullable(); // Extra config as JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
