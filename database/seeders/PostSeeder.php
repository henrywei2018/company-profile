<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Menerapkan Teknologi BIM dalam Proyek Konstruksi',
                'slug' => 'menerapkan-teknologi-bim-dalam-proyek-konstruksi',
                'excerpt' => 'Building Information Modeling (BIM) revolutionizing cara kerja industri konstruksi dengan meningkatkan efisiensi dan akurasi perencanaan.',
                'content' => '<p>Building Information Modeling (BIM) telah menjadi standar baru dalam industri konstruksi modern. Teknologi ini memungkinkan visualisasi 3D yang detail dan integrasi data proyek secara real-time.</p><p>Keuntungan penerapan BIM:</p><ul><li>Perencanaan yang lebih akurat</li><li>Koordinasi antar tim yang lebih baik</li><li>Deteksi clash detection sebelum konstruksi</li><li>Penghematan biaya dan waktu</li></ul><p>CV Usaha Prima Lestari telah mengadopsi teknologi BIM untuk semua proyek besar, memastikan kualitas dan efisiensi terbaik untuk klien.</p>',
                'user_id' => 3, // Junaidi as editor
                'featured_image' => '/images/blog/bim-technology.jpg',
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'featured' => true,
            ],
            [
                'title' => 'Pentingnya K3 dalam Proyek Konstruksi',
                'slug' => 'pentingnya-k3-dalam-proyek-konstruksi',
                'excerpt' => 'Kesehatan dan Keselamatan Kerja (K3) adalah prioritas utama dalam setiap proyek konstruksi untuk melindungi pekerja dan meningkatkan produktivitas.',
                'content' => '<p>Keselamatan kerja merupakan aspek yang tidak dapat ditawar dalam industri konstruksi. Implementasi sistem K3 yang baik tidak hanya melindungi pekerja, tetapi juga meningkatkan efisiensi proyek.</p><p>Program K3 kami meliputi:</p><ul><li>Safety induction untuk semua pekerja</li><li>Penggunaan APD (Alat Pelindung Diri) standar</li><li>Inspeksi rutin area kerja</li><li>Emergency response plan</li><li>Safety meeting berkala</li></ul><p>Dengan sertifikasi ISO 45001, kami memastikan standar K3 tertinggi dalam setiap proyek.</p>',
                'user_id' => 2, // Robinson
                'featured_image' => '/images/blog/safety-construction.jpg',
                'status' => 'published',
                'published_at' => now()->subDays(12),
                'featured' => true,
            ],
            [
                'title' => 'Proyek Pembangunan Gedung Perkantoran PT Maju Bersama',
                'slug' => 'proyek-pembangunan-gedung-perkantoran-pt-maju-bersama',
                'excerpt' => 'Menyelesaikan proyek pembangunan gedung perkantoran 8 lantai dengan teknologi green building dan smart building system.',
                'content' => '<p>Kami dengan bangga mengumumkan penyelesaian proyek pembangunan gedung perkantoran PT Maju Bersama yang berlokasi di Jakarta Selatan. Proyek ini merupakan salah satu pencapaian terbesar kami dalam implementasi konsep green building.</p><p>Fitur unggulan gedung:</p><ul><li>Energy efficient lighting system</li><li>Water recycling system</li><li>Smart building automation</li><li>HEPA air filtration</li><li>Earthquake resistant structure</li></ul><p>Proyek ini selesai tepat waktu dalam 18 bulan dengan standar kualitas yang melampaui ekspektasi klien.</p>',
                'user_id' => 1, // Super Admin
                'featured_image' => '/images/blog/project-maju-bersama.jpg',
                'status' => 'published',
                'published_at' => now()->subDays(8),
                'featured' => false,
            ],
            [
                'title' => 'Tips Memilih Material Konstruksi Berkualitas',
                'slug' => 'tips-memilih-material-konstruksi-berkualitas',
                'excerpt' => 'Panduan lengkap dalam memilih material konstruksi yang tepat untuk memastikan kualitas dan durabilitas bangunan.',
                'content' => '<p>Pemilihan material yang tepat adalah kunci kesuksesan proyek konstruksi. Material berkualitas tidak hanya mempengaruhi kekuatan struktur, tetapi juga biaya maintenance jangka panjang.</p><p>Faktor yang perlu dipertimbangkan:</p><ul><li>Sertifikasi dan standar material</li><li>Kesesuaian dengan kondisi lingkungan</li><li>Analisis cost-benefit jangka panjang</li><li>Reputasi supplier</li><li>Ketersediaan dan kontinuitas supply</li></ul><p>Tim procurement kami berpengalaman dalam seleksi material terbaik dengan harga kompetitif.</p>',
                'user_id' => 4, // Hindra
                'featured_image' => '/images/blog/construction-materials.jpg',
                'status' => 'published',
                'published_at' => now()->subDays(15),
                'featured' => false,
            ],
            [
                'title' => 'Tren Konstruksi Berkelanjutan di Indonesia',
                'slug' => 'tren-konstruksi-berkelanjutan-di-indonesia',
                'excerpt' => 'Industri konstruksi Indonesia semakin mengarah pada praktik berkelanjutan dengan adopsi green building dan eco-friendly materials.',
                'content' => '<p>Kesadaran akan lingkungan mendorong transformasi industri konstruksi menuju praktik yang lebih berkelanjutan. Indonesia mulai mengadopsi konsep green building dan sustainable construction.</p><p>Tren yang berkembang:</p><ul><li>Penggunaan material daur ulang</li><li>Energy efficient building design</li><li>Water conservation system</li><li>Carbon footprint reduction</li><li>LEED dan GBCI certification</li></ul><p>Kami berkomitmen menjadi pionir dalam konstruksi berkelanjutan di Indonesia.</p>',
                'user_id' => 3, // Junaidi
                'status' => 'draft',
                'featured' => false,
            ],
        ];

        foreach ($posts as $post) {
            $postId = DB::table('posts')->insertGetId(array_merge($post, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // Assign categories to posts
            $categoryMappings = [
                1 => [4], // BIM -> Teknologi Konstruksi
                2 => [5], // K3 -> Keselamatan Kerja
                3 => [1, 2], // Project -> Berita Perusahaan, Proyek Terbaru
                4 => [3], // Tips -> Tips & Tutorial
                5 => [4], // Tren -> Teknologi Konstruksi
            ];

            if (isset($categoryMappings[$postId])) {
                foreach ($categoryMappings[$postId] as $categoryId) {
                    DB::table('post_post_category')->insert([
                        'post_id' => $postId,
                        'post_category_id' => $categoryId,
                    ]);
                }
            }
        }
    }
}