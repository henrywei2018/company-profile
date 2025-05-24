<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique()->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('project_type')->nullable();
            $table->string('location')->nullable();
            $table->text('requirements')->nullable();
            $table->string('budget_range')->nullable();
            $table->string('estimated_cost')->nullable();
            $table->string('estimated_timeline')->nullable();
            $table->date('start_date')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'approved', 'rejected'])->default('pending');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->string('source')->nullable(); // website, phone, email, referral
            $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->boolean('client_approved')->nullable();
            $table->text('client_decline_reason')->nullable();
            $table->timestamp('client_approved_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('last_communication_at')->nullable();
            $table->boolean('project_created')->default(false);
            $table->timestamp('project_created_at')->nullable();
            $table->text('additional_info')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['status', 'created_at']);
            $table->index(['client_id', 'status']);
            $table->index(['service_id', 'status']);
            $table->index(['priority']);
            $table->index(['reviewed_at']);
            $table->index(['approved_at']);
            $table->index(['project_created']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};