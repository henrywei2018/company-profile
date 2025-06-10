<?php
// File: database/migrations/xxxx_xx_xx_add_link_type_to_banners_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->enum('link_type', ['internal', 'external', 'route', 'email', 'phone', 'anchor', 'auto'])
                  ->default('auto')
                  ->after('button_link');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('link_type');
        });
    }
};