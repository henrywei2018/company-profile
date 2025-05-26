<?php
// database/migrations/2025_05_26_create_chat_system_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Chat Sessions Table
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('visitor_info')->nullable(); // name, email, phone for guests
            $table->enum('status', ['active', 'queued', 'waiting', 'closed'])->default('active');
            $table->foreignId('assigned_operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->string('source')->default('website'); // website, mobile, etc.
            $table->timestamp('started_at');
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->text('summary')->nullable(); // Summary of the conversation
            $table->json('metadata')->nullable(); // Additional data like referrer, etc.
            $table->timestamps();
            
            $table->index(['status', 'priority']);
            $table->index(['assigned_operator_id', 'status']);
            $table->index(['started_at', 'status']);
        });

        // Chat Messages Table
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->constrained('chat_sessions')->cascadeOnDelete();
            $table->enum('sender_type', ['visitor', 'operator', 'bot', 'system']);
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('message');
            $table->enum('message_type', ['text', 'file', 'image', 'system', 'template'])->default('text');
            $table->json('metadata')->nullable(); // file info, template data, etc.
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['chat_session_id', 'created_at']);
            $table->index(['sender_type', 'created_at']);
        });

        // Chat Operators Table (extends users)
        Schema::create('chat_operators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_online')->default(false);
            $table->boolean('is_available')->default(true);
            $table->integer('max_concurrent_chats')->default(3);
            $table->integer('current_chats_count')->default(0);
            $table->timestamp('last_seen_at')->nullable();
            $table->json('settings')->nullable(); // notification preferences, etc.
            $table->timestamps();
            
            $table->unique('user_id');
            $table->index(['is_online', 'is_available']);
        });

        // Chat Templates for quick responses
        Schema::create('chat_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('trigger')->nullable(); // keyword trigger
            $table->text('message');
            $table->enum('type', ['greeting', 'auto_response', 'quick_reply', 'offline']);
            $table->json('conditions')->nullable(); // when to use this template
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
            $table->index('trigger');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_templates');
        Schema::dropIfExists('chat_operators');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_sessions');
    }
};