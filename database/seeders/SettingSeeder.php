<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General settings
            [
                'key' => 'site_name',
                'value' => 'CV Usaha Prima Lestari',
                'group' => 'general',
                'is_json' => false,
            ],
            [
                'key' => 'site_tagline',
                'value' => 'Building Excellence, Crafting Quality',
                'group' => 'general',
                'is_json' => false,
            ],
            [
                'key' => 'site_logo',
                'value' => 'images/logo.png',
                'group' => 'general',
                'is_json' => false,
            ],
            [
                'key' => 'site_logo_white',
                'value' => 'images/logo-white.png',
                'group' => 'general',
                'is_json' => false,
            ],
            [
                'key' => 'favicon',
                'value' => 'images/favicon.ico',
                'group' => 'general',
                'is_json' => false,
            ],
            [
                'key' => 'footer_text',
                'value' => 'All rights reserved. CV Usaha Prima Lestari is a leading construction and general supplier company in Indonesia.',
                'group' => 'general',
                'is_json' => false,
            ],
            
            // SEO settings
            [
                'key' => 'seo_title',
                'value' => 'CV Usaha Prima Lestari - Professional Construction & General Supplier',
                'group' => 'seo',
                'is_json' => false,
            ],
            [
                'key' => 'seo_description',
                'value' => 'CV Usaha Prima Lestari provides high-quality construction services, general supplies, and building maintenance solutions across Indonesia.',
                'group' => 'seo',
                'is_json' => false,
            ],
            [
                'key' => 'seo_keywords',
                'value' => 'construction, general supplier, building maintenance, civil engineering, jakarta, indonesia',
                'group' => 'seo',
                'is_json' => false,
            ],
            [
                'key' => 'google_analytics',
                'value' => 'UA-XXXXXXXX-X',
                'group' => 'seo',
                'is_json' => false,
            ],
            
            // Contact settings
            [
                'key' => 'contact_email',
                'value' => 'info@usahaprimalestari.com',
                'group' => 'contact',
                'is_json' => false,
            ],
            [
                'key' => 'contact_phone',
                'value' => '+62 21 7654 3210',
                'group' => 'contact',
                'is_json' => false,
            ],
            [
                'key' => 'contact_address',
                'value' => 'Jl. Raya Bogor No. 123, Jakarta, Indonesia',
                'group' => 'contact',
                'is_json' => false,
            ],
            [
                'key' => 'business_hours',
                'value' => json_encode([
                    'Monday' => '8:00 AM - 5:00 PM',
                    'Tuesday' => '8:00 AM - 5:00 PM',
                    'Wednesday' => '8:00 AM - 5:00 PM',
                    'Thursday' => '8:00 AM - 5:00 PM',
                    'Friday' => '8:00 AM - 5:00 PM',
                    'Saturday' => '9:00 AM - 1:00 PM',
                    'Sunday' => 'Closed',
                ]),
                'group' => 'contact',
                'is_json' => true,
            ],
            
            // Social media settings
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/usahaprimalestari',
                'group' => 'social',
                'is_json' => false,
            ],
            [
                'key' => 'social_twitter',
                'value' => 'https://twitter.com/usahaprimalestari',
                'group' => 'social',
                'is_json' => false,
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/usahaprimalestari',
                'group' => 'social',
                'is_json' => false,
            ],
            [
                'key' => 'social_linkedin',
                'value' => 'https://linkedin.com/company/usahaprimalestari',
                'group' => 'social',
                'is_json' => false,
            ],
            [
                'key' => 'social_youtube',
                'value' => 'https://youtube.com/usahaprimalestari',
                'group' => 'social',
                'is_json' => false,
            ],
            
            // Email settings
            [
                'key' => 'mail_from_address',
                'value' => 'no-reply@usahaprimalestari.com',
                'group' => 'email',
                'is_json' => false,
            ],
            [
                'key' => 'mail_from_name',
                'value' => 'CV Usaha Prima Lestari',
                'group' => 'email',
                'is_json' => false,
            ],
            [
                'key' => 'mail_admin',
                'value' => 'admin@usahaprimalestari.com',
                'group' => 'email',
                'is_json' => false,
            ],
            
            // Home page settings
            [
                'key' => 'home_hero_title',
                'value' => 'Building Excellence, Crafting Quality',
                'group' => 'home',
                'is_json' => false,
            ],
            [
                'key' => 'home_hero_subtitle',
                'value' => 'Professional Construction & General Supplier Services',
                'group' => 'home',
                'is_json' => false,
            ],
            [
                'key' => 'home_hero_text',
                'value' => 'CV Usaha Prima Lestari delivers reliable construction solutions and quality supplies for projects of all sizes across Indonesia.',
                'group' => 'home',
                'is_json' => false,
            ],
            [
                'key' => 'home_services_count',
                'value' => '6',
                'group' => 'home',
                'is_json' => false,
            ],
            [
                'key' => 'home_projects_count',
                'value' => '6',
                'group' => 'home',
                'is_json' => false,
            ],
            [
                'key' => 'home_testimonials_count',
                'value' => '4',
                'group' => 'home',
                'is_json' => false,
            ],
            [
                'key' => 'home_posts_count',
                'value' => '3',
                'group' => 'home',
                'is_json' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}