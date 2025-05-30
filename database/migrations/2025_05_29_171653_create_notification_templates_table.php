<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // notification type like 'project.deadline_approaching'
            $table->string('channel'); // mail, slack, database
            $table->string('subject'); // Email subject or notification title
            $table->longText('body'); // Template body with placeholders
            $table->json('placeholders')->nullable(); // Available placeholders
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false); // System templates can't be deleted
            $table->string('language')->default('en');
            $table->timestamps();
            
            $table->unique(['type', 'channel', 'language']);
            $table->index(['is_active', 'type']);
        });

        // Insert default templates
        DB::table('notification_templates')->insert([
            [
                'type' => 'project.deadline_approaching',
                'channel' => 'mail',
                'subject' => 'Project Deadline Reminder: {project_title}',
                'body' => 'Hello {client_name},\n\nYour project "{project_title}" has a deadline approaching in {days_until_deadline} days.\n\nDeadline: {deadline_date}\nCurrent Status: {project_status}\n\nPlease contact us if you have any questions.\n\nBest regards,\n{company_name}',
                'placeholders' => json_encode(['client_name', 'project_title', 'days_until_deadline', 'deadline_date', 'project_status', 'company_name']),
                'is_active' => true,
                'is_system' => true,
                'language' => 'en',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'quotation.approved',
                'channel' => 'mail',
                'subject' => 'Great News! Your Quotation has been Approved',
                'body' => 'Hello {client_name},\n\nWe are pleased to inform you that your quotation for "{project_type}" has been approved!\n\nNext Steps:\n- We will contact you within 24 hours\n- Project timeline will be provided\n- Contract details will be sent\n\nThank you for choosing {company_name}!\n\nBest regards,\nSales Team',
                'placeholders' => json_encode(['client_name', 'project_type', 'company_name']),
                'is_active' => true,
                'is_system' => true,
                'language' => 'en',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'message.urgent',
                'channel' => 'mail',
                'subject' => '🚨 URGENT: New Message Requires Attention',
                'body' => 'An urgent message has been received from {sender_name}:\n\nSubject: {message_subject}\nReceived: {received_at}\n\nPlease respond as soon as possible.\n\nView message: {message_url}',
                'placeholders' => json_encode(['sender_name', 'message_subject', 'received_at', 'message_url']),
                'is_active' => true,
                'is_system' => true,
                'language' => 'en',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};