<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ===== EXISTING GENERAL SETTINGS =====
            ['key' => 'site_name', 'value' => 'CV Usaha Prima Lestari', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Perusahaan konstruksi terpercaya dengan layanan berkualitas tinggi', 'group' => 'general'],
            ['key' => 'site_keywords', 'value' => 'konstruksi, bangunan, jalan, jembatan, infrastruktur, kontraktor', 'group' => 'general'],
            ['key' => 'site_status', 'value' => 'active', 'group' => 'general'],
            ['key' => 'maintenance_mode', 'value' => 'false', 'group' => 'general'],
            
            // ===== EXISTING CONTACT SETTINGS =====
            ['key' => 'contact_email', 'value' => 'info@usahaprimaestari.com', 'group' => 'contact'],
            ['key' => 'contact_phone', 'value' => '+62 21 1234567', 'group' => 'contact'],
            ['key' => 'contact_whatsapp', 'value' => '+62 812 3456789', 'group' => 'contact'],
            ['key' => 'contact_address', 'value' => 'Jl. Raya Konstruksi No. 123, Jakarta', 'group' => 'contact'],
            
            // ===== EXISTING SEO SETTINGS =====
            ['key' => 'meta_title_format', 'value' => '{title} | CV Usaha Prima Lestari', 'group' => 'seo'],
            ['key' => 'default_meta_description', 'value' => 'CV Usaha Prima Lestari - Solusi konstruksi terpercaya dengan kualitas superior dan layanan profesional.', 'group' => 'seo'],
            ['key' => 'google_analytics_id', 'value' => '', 'group' => 'seo'],
            ['key' => 'google_site_verification', 'value' => '', 'group' => 'seo'],
            
            // ===== EXISTING EMAIL SETTINGS =====
            ['key' => 'mail_from_name', 'value' => 'CV Usaha Prima Lestari', 'group' => 'email'],
            ['key' => 'mail_from_address', 'value' => 'noreply@usahaprimaestari.com', 'group' => 'email'],
            ['key' => 'notification_emails', 'value' => json_encode(['info@usahaprimaestari.com', 'admin@usahaprimaestari.com']), 'group' => 'email', 'is_json' => true],
            
            // ===== EXISTING BUSINESS SETTINGS =====
            ['key' => 'business_hours', 'value' => json_encode([
                'monday' => '08:00-17:00',
                'tuesday' => '08:00-17:00',
                'wednesday' => '08:00-17:00',
                'thursday' => '08:00-17:00',
                'friday' => '08:00-17:00',
                'saturday' => '08:00-12:00',
                'sunday' => 'Closed'
            ]), 'group' => 'business', 'is_json' => true],
            
            // ===== EXISTING DISPLAY SETTINGS =====
            ['key' => 'projects_per_page', 'value' => '12', 'group' => 'display'],
            ['key' => 'posts_per_page', 'value' => '10', 'group' => 'display'],
            ['key' => 'testimonials_per_page', 'value' => '6', 'group' => 'display'],
            ['key' => 'show_featured_projects', 'value' => 'true', 'group' => 'display'],
            ['key' => 'show_client_logos', 'value' => 'true', 'group' => 'display'],
            
            // ===== EXISTING SOCIAL MEDIA SETTINGS =====
            ['key' => 'social_facebook', 'value' => 'https://facebook.com/usahaprimaestari', 'group' => 'social'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/usahaprimaestari', 'group' => 'social'],
            ['key' => 'social_linkedin', 'value' => 'https://linkedin.com/company/usahaprimaestari', 'group' => 'social'],
            ['key' => 'social_youtube', 'value' => 'https://youtube.com/@usahaprimaestari', 'group' => 'social'],
            ['key' => 'social_twitter', 'value' => '', 'group' => 'social'],

            // ===== NEW EMAIL MANAGEMENT SETTINGS =====
            
            // Admin & Support Email Addresses
            ['key' => 'admin_email', 'value' => 'admin@usahaprimaestari.com', 'group' => 'email'],
            ['key' => 'support_email', 'value' => 'support@usahaprimaestari.com', 'group' => 'email'],
            
            // Message Email Settings
            ['key' => 'message_email_enabled', 'value' => '1', 'group' => 'email'],
            ['key' => 'message_auto_reply_enabled', 'value' => '1', 'group' => 'email'],
            ['key' => 'message_reply_to', 'value' => 'admin@usahaprimaestari.com', 'group' => 'email'],
            ['key' => 'message_auto_reply_template', 'value' => '', 'group' => 'email'], // Will use default template
            
            // Quotation Email Settings
            ['key' => 'quotation_email_enabled', 'value' => '1', 'group' => 'email'],
            ['key' => 'quotation_client_confirmation_enabled', 'value' => '1', 'group' => 'email'],
            ['key' => 'quotation_reply_to', 'value' => 'quotations@usahaprimaestari.com', 'group' => 'email'],
            ['key' => 'quotation_cc_email', 'value' => 'sales@usahaprimaestari.com', 'group' => 'email'],
            ['key' => 'quotation_status_updates_enabled', 'value' => '1', 'group' => 'email'],
            ['key' => 'quotation_confirmation_template', 'value' => '', 'group' => 'email'], // Will use default template
            
            // Email Queue & Delivery Settings
            ['key' => 'mail_logging_enabled', 'value' => '1', 'group' => 'email'],
            ['key' => 'daily_email_limit', 'value' => '500', 'group' => 'email'],
            ['key' => 'email_retry_attempts', 'value' => '3', 'group' => 'email'],
            
            // Company Information for Email Templates
            ['key' => 'company_name', 'value' => 'CV Usaha Prima Lestari', 'group' => 'company'],
            ['key' => 'company_phone', 'value' => '+62 21 1234567', 'group' => 'company'],
            ['key' => 'company_website', 'value' => 'www.usahaprimaestari.com', 'group' => 'company'],
            ['key' => 'company_tagline', 'value' => 'Professional Construction & General Supplier', 'group' => 'company'],
            
            // Email Template Settings
            ['key' => 'email_header_logo', 'value' => '', 'group' => 'email'],
            ['key' => 'email_footer_text', 'value' => 'CV Usaha Prima Lestari - Professional Construction & General Supplier', 'group' => 'email'],
            ['key' => 'email_primary_color', 'value' => '#1f2937', 'group' => 'email'],
            ['key' => 'email_secondary_color', 'value' => '#3b82f6', 'group' => 'email'],
            
            // Notification Settings
            ['key' => 'notify_admin_new_message', 'value' => '1', 'group' => 'notifications'],
            ['key' => 'notify_admin_new_quotation', 'value' => '1', 'group' => 'notifications'],
            ['key' => 'notify_client_quotation_status', 'value' => '1', 'group' => 'notifications'],
            ['key' => 'notify_client_message_received', 'value' => '1', 'group' => 'notifications'],
            
            // Email Security & Performance
            ['key' => 'email_rate_limit_per_hour', 'value' => '50', 'group' => 'email'],
            ['key' => 'email_blacklist', 'value' => json_encode([]), 'group' => 'email', 'is_json' => true],
            ['key' => 'email_whitelist', 'value' => json_encode([]), 'group' => 'email', 'is_json' => true],
            ['key' => 'email_queue_enabled', 'value' => '1', 'group' => 'email'],
            
            // Auto-Reply Templates (Default will be loaded from controller if empty)
            ['key' => 'message_subject_prefix', 'value' => 'Thank you for contacting us', 'group' => 'email'],
            ['key' => 'quotation_subject_prefix', 'value' => 'Quotation Request Received', 'group' => 'email'],
            
            // Email Tracking & Analytics
            ['key' => 'email_tracking_enabled', 'value' => '0', 'group' => 'email'],
            ['key' => 'email_open_tracking', 'value' => '0', 'group' => 'email'],
            ['key' => 'email_click_tracking', 'value' => '0', 'group' => 'email'],
            
            // Response Time Settings
            ['key' => 'expected_response_time_hours', 'value' => '24', 'group' => 'business'],
            ['key' => 'quotation_validity_days', 'value' => '30', 'group' => 'business'],
            ['key' => 'quotation_preparation_days', 'value' => '3', 'group' => 'business'],
        ];

        // Insert or update settings
        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']], // Check for existing key
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // Output success message
        $this->command->info('Settings seeder completed successfully!');
        $this->command->info('Total settings: ' . count($settings));
        $this->command->info('Email management settings have been added to your existing settings.');
    }
}