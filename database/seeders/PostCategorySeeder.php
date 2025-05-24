<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Berita Perusahaan',
                'slug' => 'berita-perusahaan',
                'description' => 'Update terbaru tentang perkembangan perusahaan, pencapaian, dan milestone penting.',
            ],
            [
                'name' => 'Proyek Terbaru',
                'slug' => 'proyek-terbaru',
                'description' => 'Informasi tentang proyek-proyek terbaru yang sedang dikerjakan atau telah selesai.',
            ],
            [
                'name' => 'Tips & Tutorial',
                'slug' => 'tips-tutorial',
                'description' => 'Tips praktis dan tutorial seputar konstruksi, maintenance, dan teknologi bangunan.',
            ],
            [
                'name' => 'Teknologi Konstruksi',
                'slug' => 'teknologi-konstruksi',
                'description' => 'Artikel tentang perkembangan teknologi terbaru dalam industri konstruksi.',
            ],
            [
                'name' => 'Keselamatan Kerja',
                'slug' => 'keselamatan-kerja',
                'description' => 'Informasi dan panduan tentang K3 (Kesehatan dan Keselamatan Kerja) di konstruksi.',
            ],
        ];

        foreach ($categories as $category) {
            DB::table('post_categories')->insert(array_merge($category, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
