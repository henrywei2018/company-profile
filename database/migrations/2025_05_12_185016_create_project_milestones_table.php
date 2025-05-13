<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectMilestonesTable extends Migration
{
    public function up()
    {
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'delayed'])->default('pending');
            $table->integer('progress_percent')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_milestones');
    }
}