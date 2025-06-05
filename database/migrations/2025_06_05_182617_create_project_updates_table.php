<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('project_updates')) {
            Schema::create('project_updates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->enum('type', ['status_change', 'milestone', 'note', 'file_upload', 'other'])->default('note');
                $table->json('data')->nullable();
                $table->timestamps();
                
                $table->index(['project_id', 'created_at']);
                $table->index(['user_id', 'created_at']);
                $table->index('type');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_updates');
    }
};