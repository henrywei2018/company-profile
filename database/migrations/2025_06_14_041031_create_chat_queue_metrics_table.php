<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_queue_metrics', function (Blueprint $table) {
            $table->id();
            $table->timestamp('timestamp');
            $table->integer('waiting_sessions')->default(0);
            $table->integer('active_sessions')->default(0);
            $table->integer('operators_online')->default(0);
            $table->decimal('avg_wait_time', 8, 2)->default(0);
            $table->decimal('avg_response_time', 8, 2)->default(0);
            $table->integer('sessions_completed_hour')->default(0);
            $table->integer('sessions_timeout_hour')->default(0);
            $table->json('additional_metrics')->nullable();
            $table->timestamps();
            
            $table->index('timestamp');
            $table->index(['timestamp', 'waiting_sessions']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_queue_metrics');
    }
};