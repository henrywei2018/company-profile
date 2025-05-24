<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        $teamMembers = [
            // Manajemen & Direksi
            [
                'name' => 'Arif Sudarwan',
                'slug' => 'arif-sudarwan',
                'position' => 'Wakil Direktur',
                'department_id' => 1, // Manajemen & Direksi
                'bio' => 'Wakil Direktur dengan pengalaman lebih dari 15 tahun di industri konstruksi. Memimpin berbagai proyek besar dan bertanggung jawab atas strategi bisnis perusahaan.',
                'email' => 'sudarwanarif@gmail.com',
                'phone' => '+62 812 3456789',
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Robinson Totong',
                'slug' => 'robinson-totong',
                'position' => 'Manager Administrasi',
                'department_id' => 5, // Administrasi
                'bio' => 'Manager Administrasi yang berpengalaman dalam mengelola sistem administrasi perusahaan dan mendukung operasional harian.',
                'email' => 'robinsonjuventino@gmail.com',
                'phone' => '+62 813 4567890',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Junaidi',
                'slug' => 'junaidi',
                'position' => 'Manager Pemasaran',
                'department_id' => 3, // Pemasaran
                'bio' => 'Manager Pemasaran dengan keahlian dalam strategi marketing dan pengembangan bisnis. Bertanggung jawab atas branding dan komunikasi perusahaan.',
                'email' => 'junaidi01091983@gmail.com',
                'phone' => '+62 814 5678901',
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Hindra',
                'slug' => 'hindra',
                'position' => 'Senior Project Engineer',
                'department_id' => 2, // Teknik & Proyek
                'bio' => 'Senior Project Engineer dengan spesialisasi dalam perencanaan dan pelaksanaan proyek konstruksi. Berpengalaman dalam menangani proyek-proyek kompleks.',
                'email' => 'Kakigunung14@gmail.com',
                'phone' => '+62 815 6789012',
                'is_featured' => false,
                'sort_order' => 4,
            ],
            
            // Additional team members
            [
                'name' => 'Ir. Bambang Sutrisno',
                'slug' => 'bambang-sutrisno',
                'position' => 'Chief Technical Officer',
                'department_id' => 2, // Teknik & Proyek
                'bio' => 'Insinyur sipil berpengalaman dengan spesialisasi dalam structural engineering dan project management. Memimpin tim teknik dalam berbagai proyek konstruksi.',
                'email' => 'bambang.sutrisno@usahaprimaestari.com',
                'phone' => '+62 816 7890123',
                'linkedin' => 'https://linkedin.com/in/bambang-sutrisno',
                'is_featured' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'slug' => 'siti-nurhaliza',
                'position' => 'Finance Manager',
                'department_id' => 6, // Keuangan
                'bio' => 'Manager Keuangan yang mengelola aspek finansial perusahaan dengan pengalaman dalam akuntansi konstruksi dan budgeting proyek.',
                'email' => 'siti.nurhaliza@usahaprimaestari.com',
                'phone' => '+62 817 8901234',
                'is_featured' => false,
                'sort_order' => 6,
            ],
            [
                'name' => 'Ahmad Fauzi',
                'slug' => 'ahmad-fauzi',
                'position' => 'Site Manager',
                'department_id' => 4, // Operasional
                'bio' => 'Site Manager berpengalaman dalam mengelola pelaksanaan proyek di lapangan dengan fokus pada kualitas, keselamatan, dan ketepatan waktu.',
                'email' => 'ahmad.fauzi@usahaprimaestari.com',
                'phone' => '+62 818 9012345',
                'is_featured' => false,
                'sort_order' => 7,
            ],
            [
                'name' => 'Maya Sari',
                'slug' => 'maya-sari',
                'position' => 'HR Manager',
                'department_id' => 7, // HRD
                'bio' => 'HR Manager yang bertanggung jawab atas pengembangan SDM, recruitment, dan welfare karyawan untuk mendukung pertumbuhan perusahaan.',
                'email' => 'maya.sari@usahaprimaestari.com',
                'phone' => '+62 819 0123456',
                'is_featured' => false,
                'sort_order' => 8,
            ],
        ];

        foreach ($teamMembers as $member) {
            DB::table('team_members')->insert(array_merge($member, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}