<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users with different roles
        $this->call(UserSeeder::class);
        
        // Create company profile data
        $this->call(CompanyProfileSeeder::class);
        
        // Create services and categories
        $this->call(ServiceCategorySeeder::class);
        $this->call(ServiceSeeder::class);
        
        // Create projects and related data
        $this->call(ProjectSeeder::class);
        $this->call(ProjectImageSeeder::class);
        $this->call(ProjectMilestoneSeeder::class);
        $this->call(ProjectFileSeeder::class);
        
        // Create blog content
        $this->call(PostCategorySeeder::class);
        $this->call(PostSeeder::class);
        
        // Create team members
        $this->call(TeamMemberSeeder::class);
        
        // Create testimonials
        $this->call(TestimonialSeeder::class);
        
        // Create certifications
        $this->call(CertificationSeeder::class);
        
        // Create messages and quotations for testing
        $this->call(MessageSeeder::class);
        $this->call(QuotationSeeder::class);
        
        // Create site settings
        $this->call(SettingSeeder::class);
    }
}