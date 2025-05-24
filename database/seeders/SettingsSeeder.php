<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'site_name', 'value' => 'CV Usaha Prima Lestari', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Perusahaan konstruksi terpercaya dengan layanan berkualitas tinggi', 'group' => 'general'],
            ['key' => 'site_keywords', 'value' => 'konstruksi, bangunan, jalan, jembatan, infrastruktur, kontraktor', 'group' => 'general'],
            ['key' => 'site_status', 'value' => 'active', 'group' => 'general'],
            ['key' => 'maintenance_mode', 'value' => 'false', 'group' => 'general'],
            
            // Contact Settings
            ['key' => 'contact_email', 'value' => 'info@usahaprimaestari.com', 'group' => 'contact'],
            ['key' => 'contact_phone', 'value' => '+62 21 1234567', 'group' => 'contact'],
            ['key' => 'contact_whatsapp', 'value' => '+62 812 3456789', 'group' => 'contact'],
            ['key' => 'contact_address', 'value' => 'Jl. Raya Konstruksi No. 123, Jakarta', 'group' => 'contact'],
            
            // SEO Settings
            ['key' => 'meta_title_format', 'value' => '{title} | CV Usaha Prima Lestari', 'group' => 'seo'],
            ['key' => 'default_meta_description', 'value' => 'CV Usaha Prima Lestari - Solusi konstruksi terpercaya dengan kualitas superior dan layanan profesional.', 'group' => 'seo'],
            ['key' => 'google_analytics_id', 'value' => '', 'group' => 'seo'],
            ['key' => 'google_site_verification', 'value' => '', 'group' => 'seo'],
            
            // Email Settings
            ['key' => 'mail_from_name', 'value' => 'CV Usaha Prima Lestari', 'group' => 'email'],
            ['key' => 'mail_from_address', 'value' => 'noreply@usahaprimaestari.com', 'group' => 'email'],
            ['key' => 'notification_emails', 'value' => json_encode(['info@usahaprimaestari.com', 'admin@usahaprimaestari.com']), 'group' => 'email', 'is_json' => true],
            
            // Business Settings
            ['key' => 'business_hours', 'value' => json_encode([
                'monday' => '08:00-17:00',
                'tuesday' => '08:00-17:00',
                'wednesday' => '08:00-17:00',
                'thursday' => '08:00-17:00',
                'friday' => '08:00-17:00',
                'saturday' => '08:00-12:00',
                'sunday' => 'Closed'
            ]), 'group' => 'business', 'is_json' => true],
            
            // Display Settings
            ['key' => 'projects_per_page', 'value' => '12', 'group' => 'display'],
            ['key' => 'posts_per_page', 'value' => '10', 'group' => 'display'],
            ['key' => 'testimonials_per_page', 'value' => '6', 'group' => 'display'],
            ['key' => 'show_featured_projects', 'value' => 'true', 'group' => 'display'],
            ['key' => 'show_client_logos', 'value' => 'true', 'group' => 'display'],
            
            // Social Media
            ['key' => 'social_facebook', 'value' => 'https://facebook.com/usahaprimaestari', 'group' => 'social'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/usahaprimaestari', 'group' => 'social'],
            ['key' => 'social_linkedin', 'value' => 'https://linkedin.com/company/usahaprimaestari', 'group' => 'social'],
            ['key' => 'social_youtube', 'value' => 'https://youtube.com/@usahaprimaestari', 'group' => 'social'],
            ['key' => 'social_twitter', 'value' => '', 'group' => 'social'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}