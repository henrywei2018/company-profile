<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Construction Services',
                'description' => 'Professional construction services for commercial, residential, and industrial projects.',
                'icon' => 'bi bi-building',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'General Supplier',
                'description' => 'Quality construction materials and equipment supply services.',
                'icon' => 'bi bi-box-seam',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Building Maintenance',
                'description' => 'Regular maintenance services to keep your buildings in optimal condition.',
                'icon' => 'bi bi-tools',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Project Management',
                'description' => 'End-to-end project management services for construction projects.',
                'icon' => 'bi bi-clipboard-check',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Renovation',
                'description' => 'High-quality renovation services for residential and commercial properties.',
                'icon' => 'bi bi-house-gear',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'icon' => $category['icon'],
                'is_active' => $category['is_active'],
                'sort_order' => $category['sort_order'],
            ]);
        }
    }
}