<?php
// File: database/seeders/NotificationSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        // Get all users with client role
        $clients = User::role('client')->take(5)->get();

        foreach ($clients as $client) {
            // Create sample notifications
            $notifications = [
                [
                    'id' => Str::uuid(),
                    'type' => 'App\\Notifications\\WelcomeNotification',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id' => $client->id,
                    'data' => json_encode([
                        'type' => 'welcome',
                        'title' => 'Welcome to ' . config('app.name'),
                        'message' => 'Welcome to our platform! Explore your dashboard to get started.',
                        'user_id' => $client->id,
                        'user_name' => $client->name,
                        'action_url' => route('client.dashboard'),
                        'action_text' => 'Explore Dashboard',
                        'priority' => 'normal',
                    ]),
                    'read_at' => null,
                    'created_at' => now()->subDays(rand(1, 7)),
                    'updated_at' => now()->subDays(rand(1, 7)),
                ],
                [
                    'id' => Str::uuid(),
                    'type' => 'App\\Notifications\\ProjectUpdateNotification',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id' => $client->id,
                    'data' => json_encode([
                        'type' => 'project.status_changed',
                        'title' => 'Project Status Updated',
                        'message' => 'Your project status has been updated to "In Progress".',
                        'project_id' => 1,
                        'project_title' => 'Sample Construction Project',
                        'old_status' => 'planning',
                        'new_status' => 'in_progress',
                        'action_url' => route('client.projects.index'),
                        'action_text' => 'View Projects',
                        'priority' => 'normal',
                    ]),
                    'read_at' => rand(0, 1) ? now()->subHours(rand(1, 48)) : null,
                    'created_at' => now()->subDays(rand(1, 3)),
                    'updated_at' => now()->subDays(rand(1, 3)),
                ],
                [
                    'id' => Str::uuid(),
                    'type' => 'App\\Notifications\\QuotationNotification',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id' => $client->id,
                    'data' => json_encode([
                        'type' => 'quotation.approved',
                        'title' => 'Quotation Approved',
                        'message' => 'Your quotation request has been approved! Our team will contact you soon.',
                        'quotation_id' => 1,
                        'project_type' => 'Building Construction',
                        'action_url' => route('client.quotations.index'),
                        'action_text' => 'View Quotations',
                        'priority' => 'high',
                    ]),
                    'read_at' => null,
                    'created_at' => now()->subHours(rand(2, 24)),
                    'updated_at' => now()->subHours(rand(2, 24)),
                ],
                [
                    'id' => Str::uuid(),
                    'type' => 'App\\Notifications\\MessageNotification',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id' => $client->id,
                    'data' => json_encode([
                        'type' => 'message.reply',
                        'title' => 'New Message Reply',
                        'message' => 'You have received a reply to your message.',
                        'message_id' => 1,
                        'subject' => 'Project Inquiry',
                        'action_url' => route('client.messages.index'),
                        'action_text' => 'View Messages',
                        'priority' => 'normal',
                    ]),
                    'read_at' => rand(0, 1) ? now()->subMinutes(rand(30, 180)) : null,
                    'created_at' => now()->subMinutes(rand(30, 360)),
                    'updated_at' => now()->subMinutes(rand(30, 360)),
                ],
            ];

            // Insert notifications
            foreach ($notifications as $notification) {
                DB::table('notifications')->insert($notification);
            }
        }

        $this->command->info('✅ Sample notifications created for client users');
    }
}