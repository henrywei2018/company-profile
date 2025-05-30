<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Setting key like 'deadline_alert_days'
            $table->text('value'); // Setting value (can be JSON)
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->string('group')->default('general'); // Group settings logically
            $table->text('description')->nullable(); // Human readable description
            $table->boolean('is_user_configurable')->default(false); // Can users change this?
            $table->timestamps();
            
            $table->index(['group', 'key']);
        });

        // Insert default notification settings
        DB::table('notification_settings')->insert([
            [
                'key' => 'project_deadline_alert_days',
                'value' => '[1, 3, 7]',
                'type' => 'json',
                'group' => 'projects',
                'description' => 'Days before deadline to send alerts',
                'is_user_configurable' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'quotation_expiry_days',
                'value' => '30',
                'type' => 'integer',
                'group' => 'quotations',
                'description' => 'Days until quotation expires',
                'is_user_configurable' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'quotation_reminder_days',
                'value' => '[5, 1]',
                'type' => 'json',
                'group' => 'quotations',
                'description' => 'Days before expiry to send reminders',
                'is_user_configurable' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'message_auto_reply_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'messages',
                'description' => 'Enable automatic replies to new messages',
                'is_user_configurable' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'certificate_expiry_alert_days',
                'value' => '[30, 7, 1]',
                'type' => 'json',
                'group' => 'certificates',
                'description' => 'Days before certificate expiry to send alerts',
                'is_user_configurable' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'chat_waiting_timeout_minutes',
                'value' => '10',
                'type' => 'integer',
                'group' => 'chat',
                'description' => 'Minutes to wait before sending waiting notification',
                'is_user_configurable' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'max_notifications_per_hour',
                'value' => '10',
                'type' => 'integer',
                'group' => 'limits',
                'description' => 'Maximum notifications per user per hour',
                'is_user_configurable' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};