<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Core system data
            PermissionSeeder::class,
            UserSeeder::class,
            CompanyProfileSeeder::class,
            SettingsSeeder::class,
            
            // Categories first (required by other tables)
            ServiceCategorySeeder::class,
            ProjectCategorySeeder::class,
            PostCategorySeeder::class,
            BannerCategorySeeder::class,
            TeamMemberDepartmentSeeder::class,
            
            ChatTemplateSeeder::class,

            // Content data
            ServiceSeeder::class,
            TeamMemberSeeder::class,
            CertificationSeeder::class,
            BannerSeeder::class,
            PostSeeder::class,
            ProjectSeeder::class,
            TestimonialSeeder::class,
            
            // Communication & business data
            QuotationSeeder::class,
            MessageSeeder::class,
        ]);
    }
}