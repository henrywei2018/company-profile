<?php
// database/seeders/ProjectCategorySeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Bangunan Gedung',
                'slug' => 'bangunan-gedung',
                'icon' => 'building-2',
                'description' => 'Proyek pembangunan gedung perkantoran, apartemen, hotel, dan bangunan komersial lainnya.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Jalan & Jembatan',
                'slug' => 'jalan-jembatan',
                'icon' => 'bridge',
                'description' => 'Proyek pembangunan dan perbaikan jalan raya, jalan tol, jembatan, dan flyover.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Saluran Air & Drainase',
                'slug' => 'saluran-air-drainase',
                'icon' => 'waves',
                'description' => 'Proyek pembangunan sistem drainase, saluran air, dan infrastruktur pengelolaan air.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Renovasi & Pemeliharaan',
                'slug' => 'renovasi-pemeliharaan',
                'icon' => 'hammer',
                'description' => 'Proyek renovasi, rehabilitasi, dan pemeliharaan bangunan dan infrastruktur.',
                'sort_order' => 4,
            ],
            [
                'name' => 'Industrial',
                'slug' => 'industrial',
                'icon' => 'factory',
                'description' => 'Proyek pembangunan fasilitas industri, pabrik, warehouse, dan infrastruktur industrial.',
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('project_categories')->insert(array_merge($category, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}