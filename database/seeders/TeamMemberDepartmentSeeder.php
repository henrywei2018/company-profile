<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamMemberDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Manajemen & Direksi',
                'slug' => 'manajemen-direksi',
                'description' => 'Jajaran direksi dan manajemen senior perusahaan',
                'sort_order' => 1,
            ],
            [
                'name' => 'Teknik & Proyek',
                'slug' => 'teknik-proyek',
                'description' => 'Tim teknik dan pengelola proyek konstruksi',
                'sort_order' => 2,
            ],
            [
                'name' => 'Pemasaran',
                'slug' => 'pemasaran',
                'description' => 'Tim pemasaran dan pengembangan bisnis',
                'sort_order' => 3,
            ],
            [
                'name' => 'Operasional',
                'slug' => 'operasional',
                'description' => 'Tim operasional dan pelaksanaan proyek',
                'sort_order' => 4,
            ],
            [
                'name' => 'Administrasi',
                'slug' => 'administrasi',
                'description' => 'Tim administrasi dan support',
                'sort_order' => 5,
            ],
            [
                'name' => 'Keuangan',
                'slug' => 'keuangan',
                'description' => 'Tim keuangan dan akuntansi',
                'sort_order' => 6,
            ],
            [
                'name' => 'HRD',
                'slug' => 'hrd',
                'description' => 'Tim human resource development',
                'sort_order' => 7,
            ],
            [
                'name' => 'IT',
                'slug' => 'it',
                'description' => 'Tim teknologi informasi',
                'sort_order' => 8,
            ],
        ];

        foreach ($departments as $department) {
            DB::table('team_member_departments')->insert(array_merge($department, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}