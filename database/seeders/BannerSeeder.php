<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            // Homepage Hero Banners
            [
                'banner_category_id' => 1, // Homepage Hero
                'title' => 'Solusi Konstruksi Terpercaya',
                'subtitle' => 'CV Usaha Prima Lestari',
                'description' => 'Membangun masa depan dengan kualitas superior, teknologi terkini, dan komitmen terhadap kepuasan pelanggan. Berpengalaman lebih dari 10 tahun melayani berbagai proyek konstruksi.',
                'button_text' => 'Lihat Portfolio',
                'button_link' => '/portfolio',
                'display_order' => 1,
                'start_date' => now(),
                'end_date' => now()->addYear(),
            ],
            [
                'banner_category_id' => 1, // Homepage Hero
                'title' => 'Konstruksi Bangunan & Infrastruktur',
                'subtitle' => 'Professional & Berkualitas',
                'description' => 'Spesialisasi dalam pembangunan gedung, jalan, jembatan, dan infrastruktur dengan standar internasional dan keselamatan kerja yang tinggi.',
                'button_text' => 'Konsultasi Gratis',
                'button_link' => '/contact',
                'display_order' => 2,
                'start_date' => now(),
                'end_date' => now()->addYear(),
            ],
            
            // Services Promotion
            [
                'banner_category_id' => 2, // Services Promotion
                'title' => 'Layanan Konstruksi Lengkap',
                'subtitle' => 'One Stop Solution',
                'description' => 'Dari perencanaan hingga finishing, kami menyediakan layanan konstruksi lengkap dengan tim ahli dan peralatan modern.',
                'button_text' => 'Lihat Layanan',
                'button_link' => '/services',
                'display_order' => 1,
                'start_date' => now(),
                'end_date' => now()->addMonths(6),
            ],
            
            // Project Showcase
            [
                'banner_category_id' => 3, // Project Showcase
                'title' => 'Portfolio Proyek Unggulan',
                'subtitle' => 'Kepercayaan Klien, Hasil Berkualitas',
                'description' => 'Telah menyelesaikan ratusan proyek dengan tingkat kepuasan klien 98%. Lihat portfolio lengkap kami.',
                'button_text' => 'Lihat Proyek',
                'button_link' => '/projects',
                'display_order' => 1,
                'start_date' => now(),
                'end_date' => now()->addMonths(12),
            ],
            
            // Certification
            [
                'banner_category_id' => 4, // Certification
                'title' => 'Tersertifikasi & Terpercaya',
                'subtitle' => 'ISO 9001, ISO 45001, SBU Besar',
                'description' => 'Memiliki berbagai sertifikasi nasional dan internasional yang menjamin kualitas dan keamanan dalam setiap proyek.',
                'button_text' => 'Lihat Sertifikat',
                'button_link' => '/certifications',
                'display_order' => 1,
                'start_date' => now(),
                'end_date' => now()->addMonths(12),
            ],
        ];

        foreach ($banners as $banner) {
            DB::table('banners')->insert(array_merge($banner, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}