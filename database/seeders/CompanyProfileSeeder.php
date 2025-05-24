<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyProfileSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('company_profile')->insert([
            'company_name' => 'CV Usaha Prima Lestari',
            'tagline' => 'Solusi Konstruksi Terpercaya & Berkualitas',
            'about' => 'CV Usaha Prima Lestari adalah perusahaan konstruksi yang berkomitmen memberikan layanan terbaik dalam bidang konstruksi bangunan gedung, jalan, jembatan, dan infrastruktur lainnya. Dengan pengalaman bertahun-tahun dan tim profesional yang berpengalaman, kami mengutamakan kualitas, ketepatan waktu, dan kepuasan pelanggan.',
            'vision' => 'Menjadi perusahaan konstruksi terdepan di Indonesia yang dikenal dengan kualitas superior, inovasi teknologi, dan komitmen terhadap keberlanjutan lingkungan.',
            'mission' => 'Memberikan solusi konstruksi berkualitas tinggi dengan menggunakan teknologi terkini, tenaga ahli profesional, dan standar keselamatan kerja yang tinggi untuk membangun masa depan yang lebih baik.',
            'history' => 'Didirikan pada tahun 2010, CV Usaha Prima Lestari telah berkembang menjadi salah satu kontraktor terpercaya di Indonesia. Dengan dimulai dari proyek-proyek kecil, kini kami telah menangani berbagai proyek besar baik untuk sektor swasta maupun pemerintah.',
            'values' => json_encode([
                'Kualitas' => 'Mengutamakan kualitas dalam setiap aspek pekerjaan',
                'Integritas' => 'Berkomitmen pada kejujuran dan transparansi',
                'Inovasi' => 'Selalu menggunakan teknologi dan metode terkini',
                'Keselamatan' => 'Menerapkan standar K3 yang tinggi',
                'Kepuasan Pelanggan' => 'Mengutamakan kepuasan dan kepercayaan klien'
            ]),
            'phone' => '+62 21 1234567',
            'email' => 'info@usahaprimaestari.com',
            'address' => 'Jl. Raya Konstruksi No. 123, Kompleks Industri',
            'city' => 'Jakarta',
            'postal_code' => '12345',
            'country' => 'Indonesia',
            'facebook' => 'https://facebook.com/usahaprimaestari',
            'instagram' => 'https://instagram.com/usahaprimaestari',
            'linkedin' => 'https://linkedin.com/company/usahaprimaestari',
            'youtube' => 'https://youtube.com/@usahaprimaestari',
            'whatsapp' => '+62 812 3456789',
            'latitude' => '-6.2088',
            'longitude' => '106.8456',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}