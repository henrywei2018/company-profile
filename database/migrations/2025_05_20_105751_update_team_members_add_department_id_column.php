<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTeamMembersAddDepartmentIdColumn extends Migration
{
    public function up()
    {
        Schema::table('team_members', function (Blueprint $table) {
            // Drop the old string column (only if it exists)
            if (Schema::hasColumn('team_members', 'department')) {
                $table->dropColumn('department');
            }
            
            // Add the new foreign key column
            $table->foreignId('department_id')->nullable()->constrained('team_member_departments')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('team_members', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
            
            // Add back the original string column
            $table->string('department')->nullable();
        });
    }
}