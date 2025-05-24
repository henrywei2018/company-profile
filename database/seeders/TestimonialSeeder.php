<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'project_id' => 1, // Gedung PT Maju Bersama
                'client_name' => 'Ir. Budi Santoso',
                'client_position' => 'Project Director',
                'client_company' => 'PT Maju Bersama',
                'content' => 'CV Usaha Prima Lestari menunjukkan profesionalisme tinggi dalam menangani proyek gedung kantor kami. Kualitas konstruksi sangat baik, tepat waktu, dan sesuai budget. Tim mereka sangat responsif dan komunikatif sepanjang proyek. Sangat merekomendasikan untuk proyek konstruksi lainnya.',
                'rating' => 5,
                'featured' => true,
            ],
            [
                'project_id' => 2, // Renovasi CV Berkah Sejahtera
                'client_name' => 'Maya Sari Dewi',
                'client_position' => 'General Manager',
                'client_company' => 'CV Berkah Sejahtera',
                'content' => 'Proses renovasi kantor kami berjalan sangat lancar tanpa mengganggu operasional. Tim UPL sangat terorganisir dalam perencanaan dan pelaksanaan. Hasil renovasi melebihi ekspektasi dengan design modern yang meningkatkan produktivitas karyawan. Pelayanan after sales juga excellent.',
                'rating' => 5,
                'featured' => true,
            ],
            [
                'project_id' => 3, // Jalan Cikarang
                'client_name' => 'Drs. Ahmad Firdaus',
                'client_position' => 'Kepala Dinas PU',
                'client_company' => 'Pemerintah Kabupaten Bekasi',
                'content' => 'Pembangunan jalan akses kawasan industri dikerjakan dengan standar yang sangat baik. Meskipun menghadapi tantangan cuaca, tim UPL mampu menyelesaikan proyek tepat waktu dengan kualitas yang memuaskan. Professional dan dapat diandalkan.',
                'rating' => 4,
                'featured' => true,
            ],
            [
                'project_id' => 4, // Jembatan Sukamaju
                'client_name' => 'Ir. Suherman',
                'client_position' => 'Kepala Seksi Jalan dan Jembatan',
                'client_company' => 'Dinas PU Kabupaten Bogor',
                'content' => 'Konstruksi jembatan dilakukan dengan teknologi yang tepat mengingat kondisi medan yang challenging. Hasil konstruksi sangat solid dan aman. Apresiasi tinggi untuk tim engineering yang professional dan berpengalaman.',
                'rating' => 5,
                'featured' => false,
            ],
            [
                'project_id' => 6, // Rumah Sakit
                'client_name' => 'Dr. Fitri Handayani',
                'client_position' => 'Direktur Utama',
                'client_company' => 'RS Umum Sehat Sentosa',
                'content' => 'Renovasi fasilitas medis memerlukan ketelitian dan standar tinggi. Tim UPL sangat memahami requirement medis dan berhasil menyelesaikan upgrade tanpa mengganggu pelayanan pasien. Kualitas workmanship sangat memuaskan.',
                'rating' => 5,
                'featured' => false,
            ],
            [
                'project_id' => null, // General testimonial
                'client_name' => 'Bambang Wijaya',
                'client_position' => 'Property Developer',
                'client_company' => 'PT Wijaya Property',
                'content' => 'Sudah beberapa kali bekerja sama dengan CV Usaha Prima Lestari untuk berbagai proyek property. Selalu puas dengan hasil kerja mereka. Tim yang solid, manajemen proyek yang baik, dan commitment terhadap kualitas. Recommended contractor.',
                'rating' => 4,
                'featured' => false,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            DB::table('testimonials')->insert(array_merge($testimonial, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}