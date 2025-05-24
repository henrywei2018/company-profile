<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'title' => 'Pembangunan Gedung Perkantoran PT Maju Bersama',
                'slug' => 'pembangunan-gedung-perkantoran-pt-maju-bersama',
                'description' => '<p>Proyek pembangunan gedung perkantoran modern 8 lantai dengan total luas 12.000 m² yang mengadopsi konsep green building dan smart building technology.</p><p>Gedung ini dilengkapi dengan sistem automasi canggih, energy efficient lighting, dan water recycling system yang ramah lingkungan.</p>',
                'category_id' => 1, // Bangunan Gedung
                'client_id' => 5, // PT Maju Bersama
                'client_name' => 'PT Maju Bersama',
                'location' => 'Jakarta Selatan',
                'year' => 2023,
                'status' => 'completed',
                'value' => 'Rp 25.000.000.000',
                'featured' => true,
                'start_date' => '2022-01-15',
                'end_date' => '2023-07-30',
                'challenge' => 'Pembangunan di area padat dengan akses terbatas dan requirement green building certification.',
                'solution' => 'Implementasi modular construction technique dan penggunaan eco-friendly materials dengan sistem manajemen limbah yang efisien.',
                'result' => 'Gedung selesai tepat waktu dengan sertifikasi GBCI dan penghematan energi 40% dibanding gedung konvensional.',
                'services_used' => json_encode(['Konstruksi Gedung Perkantoran', 'Green Building Consultation', 'Project Management']),
            ],
            [
                'title' => 'Renovasi Kompleks Perkantoran CV Berkah Sejahtera',
                'slug' => 'renovasi-kompleks-perkantoran-cv-berkah-sejahtera',
                'description' => '<p>Proyek renovasi total kompleks perkantoran lama menjadi modern workspace dengan konsep open office dan collaborative space.</p><p>Meliputi upgrade sistem MEP, interior design, dan landscape area untuk menciptakan environment kerja yang produktif.</p>',
                'category_id' => 4, // Renovasi & Pemeliharaan
                'client_id' => 6, // CV Berkah Sejahtera
                'client_name' => 'CV Berkah Sejahtera',
                'location' => 'Bandung',
                'year' => 2023,
                'status' => 'completed',
                'value' => 'Rp 8.500.000.000',
                'featured' => true,
                'start_date' => '2023-03-01',
                'end_date' => '2023-09-15',
                'challenge' => 'Renovasi harus dilakukan tanpa mengganggu operasional kantor yang sedang berjalan.',
                'solution' => 'Perencanaan phasing yang detail dan pelaksanaan bertahap dengan jadwal kerja malam dan weekend.',
                'result' => 'Renovasi selesai sesuai jadwal dengan peningkatan produktivitas karyawan sebesar 30%.',
                'services_used' => json_encode(['Renovasi & Upgrade', 'Interior Design', 'MEP System Upgrade']),
            ],
            [
                'title' => 'Pembangunan Jalan Akses Kawasan Industri Cikarang',
                'slug' => 'pembangunan-jalan-akses-kawasan-industri-cikarang',
                'description' => '<p>Proyek pembangunan jalan akses sepanjang 3.2 km dengan lebar 12 meter menuju kawasan industri baru di Cikarang.</p><p>Meliputi pekerjaan earthwork, perkerasan aspal, drainage system, dan street lighting untuk mendukung mobilitas industri.</p>',
                'category_id' => 2, // Jalan & Jembatan
                'client_id' => null,
                'client_name' => 'Pemerintah Kabupaten Bekasi',
                'location' => 'Cikarang, Bekasi',
                'year' => 2022,
                'status' => 'completed',
                'value' => 'Rp 15.750.000.000',
                'featured' => true,
                'start_date' => '2022-05-10',
                'end_date' => '2022-12-20',
                'challenge' => 'Kondisi tanah lembek dengan musim hujan yang panjang mempengaruhi progress pekerjaan.',
                'solution' => 'Implementasi soil improvement technique dan penjadwalan ulang dengan contingency plan untuk cuaca.',
                'result' => 'Jalan selesai dengan kualitas premium dan mampu menahan beban heavy truck sesuai spesifikasi.',
                'services_used' => json_encode(['Pembangunan Jalan Raya', 'Drainage System', 'Street Lighting']),
            ],
            [
                'title' => 'Konstruksi Jembatan Penghubung Desa Sukamaju',
                'slug' => 'konstruksi-jembatan-penghubung-desa-sukamaju',
                'description' => '<p>Pembangunan jembatan beton bertulang dengan panjang 45 meter untuk menghubungkan dua desa yang terpisah sungai.</p><p>Jembatan dirancang dengan kapasitas beban 40 ton dan dilengkapi pedestrian walkway untuk keamanan pejalan kaki.</p>',
                'category_id' => 2, // Jalan & Jembatan
                'client_id' => null,
                'client_name' => 'Dinas PU Kabupaten Bogor',
                'location' => 'Bogor, Jawa Barat',
                'year' => 2022,
                'status' => 'completed',
                'value' => 'Rp 12.300.000.000',
                'featured' => false,
                'start_date' => '2022-08-15',
                'end_date' => '2023-02-28',
                'challenge' => 'Kondisi sungai dengan debit air tinggi dan medan yang sulit diakses oleh alat berat.',
                'solution' => 'Menggunakan teknologi precast concrete dan crane khusus untuk area terbatas.',
                'result' => 'Jembatan berhasil dibangun dengan standar keamanan tinggi dan mempermudah akses masyarakat.',
                'services_used' => json_encode(['Konstruksi Jembatan', 'Structural Engineering', 'Foundation Work']),
            ],
            [
                'title' => 'Pembangunan Warehouse & Distribution Center',
                'slug' => 'pembangunan-warehouse-distribution-center',
                'description' => '<p>Proyek pembangunan warehouse modern dengan luas 8.000 m² dilengkapi dengan system racking otomatis dan loading dock.</p><p>Fasilitas ini dirancang untuk optimasi logistik dan dilengkapi dengan fire suppression system serta security system terintegrasi.</p>',
                'category_id' => 5, // Industrial
                'client_id' => null,
                'client_name' => 'PT Logistik Nusantara',
                'location' => 'Cibitung, Bekasi',
                'year' => 2023,
                'status' => 'in_progress',
                'value' => 'Rp 18.900.000.000',
                'featured' => true,
                'start_date' => '2023-09-01',
                'end_date' => '2024-03-15',
                'challenge' => 'Requirement untuk automated system yang terintegrasi dengan existing WMS client.',
                'solution' => 'Kolaborasi dengan system integrator dan extensive testing phase untuk memastikan compatibility.',
                'result' => 'Progress 75% dengan sistem automation berhasil diimplementasi sesuai spesifikasi.',
                'services_used' => json_encode(['Industrial Construction', 'Automated System Integration', 'Fire Protection System']),
            ],
            [
                'title' => 'Renovasi dan Upgrade Fasilitas Rumah Sakit',
                'slug' => 'renovasi-upgrade-fasilitas-rumah-sakit',
                'description' => '<p>Proyek upgrade fasilitas rumah sakit meliputi penambahan ruang ICU, upgrade medical gas system, dan renovasi emergency room.</p><p>Semua pekerjaan dilakukan dengan standar medis internasional dan tanpa mengganggu operasional rumah sakit.</p>',
                'category_id' => 4, // Renovasi & Pemeliharaan
                'client_id' => null,
                'client_name' => 'RS Umum Sehat Sentosa',
                'location' => 'Jakarta Timur',
                'year' => 2023,
                'status' => 'completed',
                'value' => 'Rp 6.750.000.000',
                'featured' => false,
                'start_date' => '2023-01-10',
                'end_date' => '2023-06-30',
                'challenge' => 'Pekerjaan di lingkungan medis dengan requirement steril dan operational continuity.',
                'solution' => 'Implementasi negative pressure isolation dan penjadwalan bertahap dengan protokol medis ketat.',
                'result' => 'Upgrade selesai dengan sertifikasi medical facility dan peningkatan kapasitas 50%.',
                'services_used' => json_encode(['Medical Facility Renovation', 'Medical Gas System', 'Clean Room Construction']),
            ],
        ];

        foreach ($projects as $project) {
            $projectId = DB::table('projects')->insertGetId(array_merge($project, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // Add sample project images
            $sampleImages = [
                ['image_path' => "/images/projects/project-{$projectId}-1.jpg", 'alt_text' => 'Progress pembangunan', 'is_featured' => true, 'sort_order' => 1],
                ['image_path' => "/images/projects/project-{$projectId}-2.jpg", 'alt_text' => 'Detail konstruksi', 'is_featured' => false, 'sort_order' => 2],
                ['image_path' => "/images/projects/project-{$projectId}-3.jpg", 'alt_text' => 'Hasil akhir', 'is_featured' => false, 'sort_order' => 3],
            ];

            foreach ($sampleImages as $image) {
                DB::table('project_images')->insert(array_merge($image, [
                    'project_id' => $projectId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }

            // Add sample milestones for ongoing projects
            if ($project['status'] === 'in_progress') {
                $milestones = [
                    ['title' => 'Site Preparation', 'description' => 'Land clearing dan foundation work', 'status' => 'completed', 'progress_percent' => 100, 'completed_date' => now()->subMonths(2)],
                    ['title' => 'Structure Work', 'description' => 'Konstruksi struktur utama bangunan', 'status' => 'completed', 'progress_percent' => 100, 'completed_date' => now()->subMonth()],
                    ['title' => 'MEP Installation', 'description' => 'Instalasi sistem MEP dan utilitas', 'status' => 'in_progress', 'progress_percent' => 70, 'due_date' => now()->addMonth()],
                    ['title' => 'Finishing Work', 'description' => 'Pekerjaan finishing dan landscaping', 'status' => 'pending', 'progress_percent' => 0, 'due_date' => now()->addMonths(2)],
                ];

                foreach ($milestones as $milestone) {
                    DB::table('project_milestones')->insert(array_merge($milestone, [
                        'project_id' => $projectId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]));
                }
            }
        }
    }
}