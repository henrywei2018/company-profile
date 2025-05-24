<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            // Konstruksi Bangunan
            [
                'title' => 'Konstruksi Gedung Perkantoran',
                'slug' => 'konstruksi-gedung-perkantoran',
                'category_id' => 1,
                'short_description' => 'Pembangunan gedung perkantoran modern dengan standar internasional dan teknologi terkini.',
                'description' => '<p>Kami menyediakan layanan lengkap untuk pembangunan gedung perkantoran mulai dari perencanaan, desain, hingga konstruksi. Tim ahli kami berpengalaman dalam membangun berbagai jenis gedung perkantoran dengan standar kualitas tinggi dan teknologi modern.</p><p>Layanan meliputi struktur bangunan, sistem MEP (Mechanical, Electrical, Plumbing), interior finishing, dan landscape. Semua proyek dikerjakan sesuai standar SNI dan regulasi yang berlaku.</p>',
                'icon' => 'building',
                'featured' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Konstruksi Rumah Tinggal',
                'slug' => 'konstruksi-rumah-tinggal',
                'category_id' => 1,
                'short_description' => 'Pembangunan rumah tinggal dengan desain custom sesuai kebutuhan dan budget keluarga.',
                'description' => '<p>Spesialisasi dalam pembangunan rumah tinggal dari yang sederhana hingga mewah. Kami menawarkan layanan desain custom, konsultasi arsitektur, dan pelaksanaan konstruksi dengan material berkualitas.</p><p>Tim kami akan membantu mewujudkan rumah impian Anda dengan memperhatikan aspek fungsionalitas, estetika, dan efisiensi biaya.</p>',
                'icon' => 'home',
                'featured' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Konstruksi Apartemen & Hotel',
                'slug' => 'konstruksi-apartemen-hotel',
                'category_id' => 1,
                'short_description' => 'Pembangunan apartemen dan hotel dengan standar hospitality dan comfort tinggi.',
                'description' => '<p>Pengalaman luas dalam membangun apartemen dan hotel dengan memperhatikan aspek comfort, safety, dan operasional. Kami memahami kebutuhan khusus untuk bangunan hunian vertikal dan hospitality.</p>',
                'icon' => 'building-skyscraper',
                'featured' => false,
                'sort_order' => 3,
            ],
            
            // Konstruksi Infrastruktur
            [
                'title' => 'Pembangunan Jalan Raya',
                'slug' => 'pembangunan-jalan-raya',
                'category_id' => 2,
                'short_description' => 'Konstruksi jalan raya dengan standar kualitas tinggi dan teknologi perkerasan modern.',
                'description' => '<p>Layanan pembangunan jalan raya meliputi pekerjaan tanah, perkerasan aspal, beton, drainage, dan marking jalan. Menggunakan peralatan modern dan material berkualitas untuk hasil yang tahan lama.</p>',
                'icon' => 'road',
                'featured' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'Konstruksi Jembatan',
                'slug' => 'konstruksi-jembatan',
                'category_id' => 2,
                'short_description' => 'Pembangunan jembatan dengan teknologi engineering terdepan dan standar keamanan tinggi.',
                'description' => '<p>Spesialisasi dalam pembangunan berbagai jenis jembatan mulai dari jembatan sederhana hingga jembatan besar. Tim engineering kami berpengalaman dalam structural design dan konstruksi jembatan.</p>',
                'icon' => 'bridge',
                'featured' => true,
                'sort_order' => 5,
            ],
            [
                'title' => 'Sistem Drainase & Irigasi',
                'slug' => 'sistem-drainase-irigasi',
                'category_id' => 2,
                'short_description' => 'Pembangunan sistem drainase dan irigasi untuk pengelolaan air yang optimal.',
                'description' => '<p>Layanan pembangunan sistem drainase perkotaan, saluran irigasi, dan pengelolaan air. Menggunakan teknologi modern untuk memastikan sistem berfungsi optimal dan tahan lama.</p>',
                'icon' => 'waves',
                'featured' => false,
                'sort_order' => 6,
            ],
            
            // Perawatan & Pemeliharaan
            [
                'title' => 'Maintenance Bangunan',
                'slug' => 'maintenance-bangunan',
                'category_id' => 3,
                'short_description' => 'Layanan perawatan rutin dan darurat untuk menjaga kondisi bangunan tetap optimal.',
                'description' => '<p>Layanan maintenance komprehensif meliputi perawatan struktur, sistem MEP, cat ulang, waterproofing, dan perbaikan kerusakan. Tim maintenance kami siap melayani 24/7 untuk kebutuhan darurat.</p>',
                'icon' => 'tools',
                'featured' => false,
                'sort_order' => 7,
            ],
            [
                'title' => 'Renovasi & Upgrade',
                'slug' => 'renovasi-upgrade',
                'category_id' => 3,
                'short_description' => 'Layanan renovasi dan upgrade bangunan untuk meningkatkan fungsi dan estetika.',
                'description' => '<p>Spesialisasi dalam renovasi bangunan lama, upgrade fasilitas, dan modernisasi sistem. Kami membantu mengoptimalkan fungsi bangunan sesuai kebutuhan terkini.</p>',
                'icon' => 'hammer',
                'featured' => false,
                'sort_order' => 8,
            ],
            
            // Penjualan Peralatan
            [
                'title' => 'Penjualan Alat Berat',
                'slug' => 'penjualan-alat-berat',
                'category_id' => 4,
                'short_description' => 'Penjualan dan sewa alat berat konstruksi dengan kondisi prima dan harga kompetitif.',
                'description' => '<p>Menyediakan berbagai alat berat konstruksi baik untuk dijual maupun disewa. Semua equipment dalam kondisi terawat dan siap operasi dengan dukungan maintenance.</p>',
                'icon' => 'truck',
                'featured' => false,
                'sort_order' => 9,
            ],
            [
                'title' => 'Penjualan Material Konstruksi',
                'slug' => 'penjualan-material-konstruksi',
                'category_id' => 4,
                'short_description' => 'Penjualan material konstruksi berkualitas dengan harga kompetitif dan pengiriman cepat.',
                'description' => '<p>Menyediakan berbagai material konstruksi berkualitas tinggi termasuk semen, besi, pasir, batu, dan material finishing. Melayani pembelian dalam jumlah kecil hingga besar.</p>',
                'icon' => 'package',
                'featured' => false,
                'sort_order' => 10,
            ],
        ];

        foreach ($services as $service) {
            DB::table('services')->insert(array_merge($service, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}