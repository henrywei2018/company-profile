<?php

namespace Database\Seeders;

use App\Models\PostCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Company News',
                'description' => 'Latest updates and news about CV Usaha Prima Lestari.',
            ],
            [
                'name' => 'Construction Trends',
                'description' => 'Insights into current and emerging trends in the construction industry.',
            ],
            [
                'name' => 'Project Highlights',
                'description' => 'Spotlight on our notable projects and achievements.',
            ],
            [
                'name' => 'Building Technology',
                'description' => 'Information about new technologies and innovations in building and construction.',
            ],
            [
                'name' => 'Sustainable Construction',
                'description' => 'Topics related to eco-friendly building practices and sustainable development.',
            ],
            [
                'name' => 'Industry Insights',
                'description' => 'Expert perspectives and analysis of the construction and supply industry.',
            ],
        ];

        foreach ($categories as $category) {
            PostCategory::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
            ]);
        }
    }
}