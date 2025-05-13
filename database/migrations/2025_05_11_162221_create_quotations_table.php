<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration
{
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->foreignId('service_id')->nullable()->constrained()->onDelete('set null');
            $table->string('project_type')->nullable();
            $table->string('location')->nullable();
            $table->text('requirements')->nullable();
            $table->string('budget_range')->nullable();
            $table->date('start_date')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'approved', 'rejected'])->default('pending');
            $table->foreignId('client_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('admin_notes')->nullable();
            $table->boolean('client_approved')->nullable();
            $table->text('client_decline_reason')->nullable();
            $table->timestamp('client_approved_at')->nullable();
            $table->text('additional_info')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotations');
    }
}