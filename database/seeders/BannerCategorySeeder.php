<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Homepage Hero',
                'slug' => 'homepage-hero',
                'description' => 'Banner utama di halaman depan website',
                'display_order' => 1,
            ],
            [
                'name' => 'Services Promotion',
                'slug' => 'services-promotion',
                'description' => 'Banner promosi layanan perusahaan',
                'display_order' => 2,
            ],
            [
                'name' => 'Project Showcase',
                'slug' => 'project-showcase',
                'description' => 'Banner showcase proyek-proyek unggulan',
                'display_order' => 3,
            ],
            [
                'name' => 'Certification',
                'slug' => 'certification',
                'description' => 'Banner menampilkan sertifikasi dan pencapaian',
                'display_order' => 4,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('banner_categories')->insert(array_merge($category, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}