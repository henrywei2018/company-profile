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
        Schema::create('project_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Add category_id to projects table if it doesn't exist
        if (!Schema::hasColumn('projects', 'category_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->after('id')->constrained('project_categories')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key constraint first
        if (Schema::hasColumn('projects', 'category_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropConstrainedForeignId('category_id');
            });
        }

        Schema::dropIfExists('project_categories');
    }
};