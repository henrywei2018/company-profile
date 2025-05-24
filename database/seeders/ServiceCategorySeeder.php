<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Konstruksi Bangunan',
                'slug' => 'konstruksi-bangunan',
                'icon' => 'building',
                'description' => 'Layanan konstruksi bangunan gedung komersial, residential, dan industrial dengan standar kualitas tinggi.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Konstruksi Infrastruktur',
                'slug' => 'konstruksi-infrastruktur',
                'icon' => 'road',
                'description' => 'Pembangunan infrastruktur jalan, jembatan, dan saluran air dengan teknologi modern.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Perawatan & Pemeliharaan',
                'slug' => 'perawatan-pemeliharaan',
                'icon' => 'tools',
                'description' => 'Layanan perawatan dan pemeliharaan bangunan dan infrastruktur untuk menjaga kualitas dan daya tahan.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Penjualan Peralatan',
                'slug' => 'penjualan-peralatan',
                'icon' => 'truck',
                'description' => 'Penjualan peralatan teknik dan mesin konstruksi berkualitas untuk mendukung proyek konstruksi.',
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('service_categories')->insert(array_merge($category, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}