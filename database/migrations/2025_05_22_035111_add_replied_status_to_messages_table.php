<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRepliedStatusToMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_replied')->default(false)->after('is_read');
            $table->timestamp('replied_at')->nullable()->after('read_at');
            $table->foreignId('replied_by')->nullable()->constrained('users')->onDelete('set null')->after('replied_at');
            
            // Add indexes for better performance
            $table->index(['type', 'is_read', 'is_replied']);
            $table->index(['is_replied', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['replied_by']);
            $table->dropIndex(['type', 'is_read', 'is_replied']);
            $table->dropIndex(['is_replied', 'created_at']);
            $table->dropColumn(['is_replied', 'replied_at', 'replied_by']);
        });
    }
}