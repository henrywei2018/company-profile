<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add additional fields to users table for enhanced RBAC functionality
        Schema::table('users', function (Blueprint $table) {
            // Login tracking fields
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
            }
            
            if (!Schema::hasColumn('users', 'login_count')) {
                $table->unsignedInteger('login_count')->default(0)->after('last_login_at');
            }
            
            // Security fields
            if (!Schema::hasColumn('users', 'failed_login_attempts')) {
                $table->unsignedTinyInteger('failed_login_attempts')->default(0)->after('login_count');
            }
            
            if (!Schema::hasColumn('users', 'locked_at')) {
                $table->timestamp('locked_at')->nullable()->after('failed_login_attempts');
            }
            
            // Additional profile fields that might be missing
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            
            if (!Schema::hasColumn('users', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('state');
            }
            
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country')->nullable()->after('postal_code');
            }
        });

        // Add indexes for better performance (without checking if they exist)
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index('is_active', 'users_is_active_idx');
                $table->index('last_login_at', 'users_last_login_at_idx');
                $table->index('locked_at', 'users_locked_at_idx');
            });
        } catch (\Exception $e) {
            // Indexes might already exist, ignore the error
        }

        // Ensure role and permission tables have the enhanced fields
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                if (!Schema::hasColumn('roles', 'description')) {
                    $table->text('description')->nullable()->after('guard_name');
                }
                
                if (!Schema::hasColumn('roles', 'color')) {
                    $table->string('color')->nullable()->after('description');
                }
                
                if (!Schema::hasColumn('roles', 'is_system')) {
                    $table->boolean('is_system')->default(false)->after('color');
                }
                
                if (!Schema::hasColumn('roles', 'created_at')) {
                    $table->timestamps();
                }
            });
        }

        if (Schema::hasTable('permissions')) {
            Schema::table('permissions', function (Blueprint $table) {
                if (!Schema::hasColumn('permissions', 'description')) {
                    $table->text('description')->nullable()->after('guard_name');
                }
                
                if (!Schema::hasColumn('permissions', 'module')) {
                    $table->string('module')->nullable()->after('description');
                }
                
                if (!Schema::hasColumn('permissions', 'is_system')) {
                    $table->boolean('is_system')->default(false)->after('module');
                }
                
                if (!Schema::hasColumn('permissions', 'created_at')) {
                    $table->timestamps();
                }
            });
        }

        // Add indexes for permissions and roles (with error handling)
        try {
            if (Schema::hasTable('permissions')) {
                Schema::table('permissions', function (Blueprint $table) {
                    $table->index('module', 'permissions_module_idx');
                    $table->index('is_system', 'permissions_is_system_idx');
                });
            }
        } catch (\Exception $e) {
            // Indexes might already exist, ignore the error
        }

        try {
            if (Schema::hasTable('roles')) {
                Schema::table('roles', function (Blueprint $table) {
                    $table->index('is_system', 'roles_is_system_idx');
                });
            }
        } catch (\Exception $e) {
            // Indexes might already exist, ignore the error
        }
    }

    public function down(): void
    {
        // Remove added fields from users table
        Schema::table('users', function (Blueprint $table) {
            $columns = ['last_login_at', 'login_count', 'failed_login_attempts', 'locked_at'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Drop indexes (with error handling)
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('users_is_active_idx');
                $table->dropIndex('users_last_login_at_idx');
                $table->dropIndex('users_locked_at_idx');
            });
        } catch (\Exception $e) {
            // Indexes might not exist, ignore the error
        }

        // Remove enhanced fields from roles table
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                $columns = ['description', 'color', 'is_system'];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn('roles', $column)) {
                        $table->dropColumn($column);
                    }
                }
                
                // Drop timestamps if they were added
                if (Schema::hasColumn('roles', 'created_at') && Schema::hasColumn('roles', 'updated_at')) {
                    $table->dropTimestamps();
                }
            });
            
            try {
                Schema::table('roles', function (Blueprint $table) {
                    $table->dropIndex('roles_is_system_idx');
                });
            } catch (\Exception $e) {
                // Index might not exist, ignore the error
            }
        }

        // Remove enhanced fields from permissions table
        if (Schema::hasTable('permissions')) {
            Schema::table('permissions', function (Blueprint $table) {
                $columns = ['description', 'module', 'is_system'];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn('permissions', $column)) {
                        $table->dropColumn($column);
                    }
                }
                
                // Drop timestamps if they were added
                if (Schema::hasColumn('permissions', 'created_at') && Schema::hasColumn('permissions', 'updated_at')) {
                    $table->dropTimestamps();
                }
            });
            
            try {
                Schema::table('permissions', function (Blueprint $table) {
                    $table->dropIndex('permissions_module_idx');
                    $table->dropIndex('permissions_is_system_idx');
                });
            } catch (\Exception $e) {
                // Indexes might not exist, ignore the error
            }
        }
    }
};