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
        // Update users table - add fields needed for testimonials if missing
        Schema::table('users', function (Blueprint $table) {
            // Company/organization information for testimonials
            if (!Schema::hasColumn('users', 'company')) {
                $table->string('company')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position')->nullable()->after('company');
            }
            
            if (!Schema::hasColumn('users', 'website')) {
                $table->string('website')->nullable()->after('position');
            }
            
            // Contact information
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('website');
            }
            
            // Address fields if not present
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            
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
            
            // Profile information
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('country');
            }
            
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('avatar');
            }
            
            // Status and preferences
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('bio');
            }
            
            // Testimonial preferences
            if (!Schema::hasColumn('users', 'allow_testimonials')) {
                $table->boolean('allow_testimonials')->default(true)->after('is_active');
            }
            
            if (!Schema::hasColumn('users', 'allow_public_profile')) {
                $table->boolean('allow_public_profile')->default(false)->after('allow_testimonials');
            }
            
            // Settings JSON field if not present
            if (!Schema::hasColumn('users', 'settings')) {
                $table->json('settings')->nullable()->after('allow_public_profile');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Only drop columns we added that are specifically for testimonials
            $columnsToCheck = [
                'allow_testimonials',
                'allow_public_profile',
                'bio'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};