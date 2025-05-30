<?php
// File: database/migrations/2025_05_30_000001_add_notification_fields_to_existing_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add notification preferences to users table
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Notification preferences - only add if they don't exist
                if (!Schema::hasColumn('users', 'email_notifications')) {
                    $table->boolean('email_notifications')->default(true);
                }
                if (!Schema::hasColumn('users', 'project_update_notifications')) {
                    $table->boolean('project_update_notifications')->default(true);
                }
                if (!Schema::hasColumn('users', 'quotation_update_notifications')) {
                    $table->boolean('quotation_update_notifications')->default(true);
                }
                if (!Schema::hasColumn('users', 'message_reply_notifications')) {
                    $table->boolean('message_reply_notifications')->default(true);
                }
                if (!Schema::hasColumn('users', 'deadline_alert_notifications')) {
                    $table->boolean('deadline_alert_notifications')->default(true);
                }
                if (!Schema::hasColumn('users', 'system_notifications')) {
                    $table->boolean('system_notifications')->default(false);
                }
                if (!Schema::hasColumn('users', 'marketing_emails')) {
                    $table->boolean('marketing_emails')->default(false);
                }
                
                // Tracking fields for notifications
                if (!Schema::hasColumn('users', 'profile_reminder_sent_at')) {
                    $table->timestamp('profile_reminder_sent_at')->nullable();
                }
                if (!Schema::hasColumn('users', 'last_notification_sent_at')) {
                    $table->timestamp('last_notification_sent_at')->nullable();
                }
                if (!Schema::hasColumn('users', 'notification_frequency')) {
                    $table->enum('notification_frequency', ['immediate', 'hourly', 'daily', 'weekly'])->default('immediate');
                }
                if (!Schema::hasColumn('users', 'quiet_hours')) {
                    $table->json('quiet_hours')->nullable(); // Store quiet hours preferences
                }
            });
        }

        // Add notification tracking to projects table
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (!Schema::hasColumn('projects', 'deadline_notification_sent_at')) {
                    $table->timestamp('deadline_notification_sent_at')->nullable();
                }
                if (!Schema::hasColumn('projects', 'overdue_notification_sent_at')) {
                    $table->timestamp('overdue_notification_sent_at')->nullable();
                }
                if (!Schema::hasColumn('projects', 'completion_notification_sent_at')) {
                    $table->timestamp('completion_notification_sent_at')->nullable();
                }
                if (!Schema::hasColumn('projects', 'priority')) {
                    $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
                }
                if (!Schema::hasColumn('projects', 'client_notified_at')) {
                    $table->timestamp('client_notified_at')->nullable();
                }
                if (!Schema::hasColumn('projects', 'progress_percentage')) {
                    $table->tinyInteger('progress_percentage')->default(0);
                }
            });
        }

        // Add notification tracking to quotations table
        if (Schema::hasTable('quotations')) {
            Schema::table('quotations', function (Blueprint $table) {
                if (!Schema::hasColumn('quotations', 'client_notification_sent_at')) {
                    $table->timestamp('client_notification_sent_at')->nullable();
                }
                if (!Schema::hasColumn('quotations', 'expiry_notification_sent_at')) {
                    $table->timestamp('expiry_notification_sent_at')->nullable();
                }
                if (!Schema::hasColumn('quotations', 'follow_up_count')) {
                    $table->unsignedTinyInteger('follow_up_count')->default(0);
                }
                if (!Schema::hasColumn('quotations', 'client_viewed_at')) {
                    $table->timestamp('client_viewed_at')->nullable();
                }
                if (!Schema::hasColumn('quotations', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable(); // When quotation expires
                }
                if (!Schema::hasColumn('quotations', 'reminder_sent_at')) {
                    $table->timestamp('reminder_sent_at')->nullable();
                }
            });
        }

        // Add notification fields to messages table
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                if (!Schema::hasColumn('messages', 'priority')) {
                    $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
                }
                if (!Schema::hasColumn('messages', 'auto_reply_sent')) {
                    $table->boolean('auto_reply_sent')->default(false);
                }
                if (!Schema::hasColumn('messages', 'notification_sent_at')) {
                    $table->timestamp('notification_sent_at')->nullable();
                }
                if (!Schema::hasColumn('messages', 'requires_response')) {
                    $table->boolean('requires_response')->default(true);
                }
                if (!Schema::hasColumn('messages', 'response_deadline')) {
                    $table->timestamp('response_deadline')->nullable();
                }
            });
        }

        // Add notification fields to testimonials table
        if (Schema::hasTable('testimonials')) {
            Schema::table('testimonials', function (Blueprint $table) {
                if (!Schema::hasColumn('testimonials', 'approval_notification_sent_at')) {
                    $table->timestamp('approval_notification_sent_at')->nullable();
                }
                if (!Schema::hasColumn('testimonials', 'featured_notification_sent_at')) {
                    $table->timestamp('featured_notification_sent_at')->nullable();
                }
                if (!Schema::hasColumn('testimonials', 'status')) {
                    $table->enum('status', ['pending', 'approved', 'rejected', 'featured'])->default('pending');
                }
                if (!Schema::hasColumn('testimonials', 'admin_notes')) {
                    $table->text('admin_notes')->nullable();
                }
            });
        }

        // Add notification tracking to certifications table (if exists)
        if (Schema::hasTable('certifications')) {
            Schema::table('certifications', function (Blueprint $table) {
                if (!Schema::hasColumn('certifications', 'expiry_notification_sent_at')) {
                    $table->timestamp('expiry_notification_sent_at')->nullable();
                }
                if (!Schema::hasColumn('certifications', 'renewal_reminder_count')) {
                    $table->unsignedTinyInteger('renewal_reminder_count')->default(0);
                }
            });
        }

        // Add notification tracking to chat_sessions table (if exists)
        if (Schema::hasTable('chat_sessions')) {
            Schema::table('chat_sessions', function (Blueprint $table) {
                if (!Schema::hasColumn('chat_sessions', 'operator_notification_sent_at')) {
                    $table->timestamp('operator_notification_sent_at')->nullable();
                }
                if (!Schema::hasColumn('chat_sessions', 'waiting_notification_count')) {
                    $table->unsignedTinyInteger('waiting_notification_count')->default(0);
                }
                if (!Schema::hasColumn('chat_sessions', 'inactive_notification_sent_at')) {
                    $table->timestamp('inactive_notification_sent_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        // Remove notification fields from users table
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn([
                    'email_notifications',
                    'project_update_notifications',
                    'quotation_update_notifications',
                    'message_reply_notifications',
                    'deadline_alert_notifications',
                    'system_notifications',
                    'marketing_emails',
                    'profile_reminder_sent_at',
                    'last_notification_sent_at',
                    'notification_frequency',
                    'quiet_hours',
                ]);
            });
        }

        // Remove notification fields from projects table
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn([
                    'deadline_notification_sent_at',
                    'overdue_notification_sent_at',
                    'completion_notification_sent_at',
                    'priority',
                    'client_notified_at',
                    'progress_percentage',
                ]);
            });
        }

        // Remove notification fields from quotations table
        if (Schema::hasTable('quotations')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->dropColumn([
                    'client_notification_sent_at',
                    'expiry_notification_sent_at',
                    'follow_up_count',
                    'client_viewed_at',
                    'expires_at',
                    'reminder_sent_at',
                ]);
            });
        }

        // Remove notification fields from messages table
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->dropColumn([
                    'priority',
                    'auto_reply_sent',
                    'notification_sent_at',
                    'requires_response',
                    'response_deadline',
                ]);
            });
        }

        // Remove notification fields from testimonials table
        if (Schema::hasTable('testimonials')) {
            Schema::table('testimonials', function (Blueprint $table) {
                $table->dropColumn([
                    'approval_notification_sent_at',
                    'featured_notification_sent_at',
                    'status',
                    'admin_notes',
                ]);
            });
        }

        // Remove notification fields from certifications table
        if (Schema::hasTable('certifications')) {
            Schema::table('certifications', function (Blueprint $table) {
                $table->dropColumn([
                    'expiry_notification_sent_at',
                    'renewal_reminder_count',
                ]);
            });
        }

        // Remove notification fields from chat_sessions table
        if (Schema::hasTable('chat_sessions')) {
            Schema::table('chat_sessions', function (Blueprint $table) {
                $table->dropColumn([
                    'operator_notification_sent_at',
                    'waiting_notification_count',
                    'inactive_notification_sent_at',
                ]);
            });
        }
    }
};