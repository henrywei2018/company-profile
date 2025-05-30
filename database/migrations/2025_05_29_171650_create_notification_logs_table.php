<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('notification_type'); // e.g., 'project.deadline_approaching'
            $table->string('channel'); // mail, database, slack, etc.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('recipient_email')->nullable();
            $table->string('status'); // sent, failed, queued, delivered
            $table->json('data')->nullable(); // Store notification data
            $table->text('error_message')->nullable(); // If failed
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable(); // Email tracking
            $table->timestamp('clicked_at')->nullable(); // Link tracking
            $table->timestamps();
            
            $table->index(['notification_type', 'status']);
            $table->index(['user_id', 'sent_at']);
            $table->index(['status', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};