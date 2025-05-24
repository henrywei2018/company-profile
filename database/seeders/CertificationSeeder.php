<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CertificationSeeder extends Seeder
{
    public function run(): void
    {
        $certifications = [
            [
                'name' => 'ISO 9001:2015',
                'issuer' => 'Badan Sertifikasi Internasional',
                'description' => 'Sertifikasi Sistem Manajemen Mutu yang menjamin konsistensi kualitas layanan dan produk konstruksi sesuai standar internasional.',
                'issue_date' => '2022-03-15',
                'expiry_date' => '2025-03-15',
                'sort_order' => 1,
            ],
            [
                'name' => 'ISO 45001:2018',
                'issuer' => 'Badan Sertifikasi K3',
                'description' => 'Sertifikasi Sistem Manajemen Kesehatan dan Keselamatan Kerja (K3) yang memastikan keselamatan dalam setiap proyek konstruksi.',
                'issue_date' => '2022-06-20',
                'expiry_date' => '2025-06-20',
                'sort_order' => 2,
            ],
            [
                'name' => 'Sertifikat Badan Usaha Konstruksi Kualifikasi Besar',
                'issuer' => 'Lembaga Pengembangan Jasa Konstruksi (LPJK)',
                'description' => 'Sertifikasi resmi sebagai kontraktor berkualifikasi besar untuk menangani proyek-proyek konstruksi skala besar.',
                'issue_date' => '2021-09-10',
                'expiry_date' => '2024-09-10',
                'sort_order' => 3,
            ],
            [
                'name' => 'Keanggotaan Asosiasi Kontraktor Indonesia (AKI)',
                'issuer' => 'Asosiasi Kontraktor Indonesia',
                'description' => 'Keanggotaan aktif dalam asosiasi profesi kontraktor yang menunjukkan komitmen terhadap standar industri.',
                'issue_date' => '2020-01-15',
                'expiry_date' => null, // Permanent membership
                'sort_order' => 4,
            ],
            [
                'name' => 'Sertifikat Kepatuhan SNI',
                'issuer' => 'Badan Standardisasi Nasional',
                'description' => 'Sertifikasi kepatuhan terhadap Standar Nasional Indonesia dalam pelaksanaan konstruksi.',
                'issue_date' => '2022-11-30',
                'expiry_date' => '2025-11-30',
                'sort_order' => 5,
            ],
        ];

        foreach ($certifications as $certification) {
            DB::table('certifications')->insert(array_merge($certification, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}