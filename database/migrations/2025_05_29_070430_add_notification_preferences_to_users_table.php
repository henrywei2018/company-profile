// File: database/migrations/xxxx_xx_xx_add_notification_preferences_to_users_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // General notification preferences
            $table->boolean('email_notifications')->default(true);
            
            // Specific notification type preferences
            $table->boolean('project_update_notifications')->default(true);
            $table->boolean('quotation_update_notifications')->default(true);
            $table->boolean('message_reply_notifications')->default(true);
            $table->boolean('deadline_alert_notifications')->default(true);
            $table->boolean('chat_notifications')->default(true);
            $table->boolean('system_notifications')->default(false);
            $table->boolean('marketing_notifications')->default(false);
            $table->boolean('testimonial_notifications')->default(true);
            
            // Admin-specific preferences
            $table->boolean('urgent_notifications')->default(true);
            $table->boolean('user_registration_notifications')->default(false);
            $table->boolean('security_alert_notifications')->default(true);
            
            // Notification frequency preferences
            $table->enum('notification_frequency', ['immediate', 'hourly', 'daily', 'weekly'])->default('immediate');
            $table->json('quiet_hours')->nullable(); // Store time ranges when notifications should not be sent
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_notifications',
                'project_update_notifications',
                'quotation_update_notifications',
                'message_reply_notifications',
                'deadline_alert_notifications',
                'chat_notifications',
                'system_notifications',
                'marketing_notifications',
                'testimonial_notifications',
                'urgent_notifications',
                'user_registration_notifications',
                'security_alert_notifications',
                'notification_frequency',
                'quiet_hours',
            ]);
        });
    }
};