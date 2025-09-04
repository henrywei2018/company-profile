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
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->integer('satisfaction_rating'); // 1-5 rating
            $table->enum('ease_of_use', ['very_easy', 'easy', 'neutral', 'difficult', 'very_difficult'])->nullable();
            $table->text('comments')->nullable();
            $table->string('page_url')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('session_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // if logged in
            $table->timestamp('submitted_at');
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['submitted_at']);
            $table->index(['satisfaction_rating']);
            $table->index(['page_url']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
