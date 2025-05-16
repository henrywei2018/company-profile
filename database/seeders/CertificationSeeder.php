<?php

namespace Database\Seeders;

use App\Models\Certification;
use Illuminate\Database\Seeder;

class CertificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $certifications = [
            [
                'name' => 'ISO 9001:2015',
                'issuer' => 'International Organization for Standardization',
                'description' => 'Quality Management System certification demonstrating our commitment to consistently providing products and services that meet customer and regulatory requirements.',
                'issue_date' => '2020-06-15',
                'expiry_date' => '2023-06-14',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'ISO 14001:2015',
                'issuer' => 'International Organization for Standardization',
                'description' => 'Environmental Management System certification showing our dedication to environmental responsibility and sustainable practices in construction.',
                'issue_date' => '2021-03-22',
                'expiry_date' => '2024-03-21',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'OHSAS 18001',
                'issuer' => 'Occupational Health and Safety Assessment Series',
                'description' => 'Occupational Health and Safety Management certification confirming our commitment to providing a safe and healthy workplace.',
                'issue_date' => '2019-11-10',
                'expiry_date' => '2022-11-09',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Green Building Certification',
                'issuer' => 'Green Building Council Indonesia',
                'description' => 'Recognition of our expertise in constructing environmentally sustainable buildings that meet green building standards.',
                'issue_date' => '2021-09-05',
                'expiry_date' => '2024-09-04',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Professional Contractor License - Class A',
                'issuer' => 'Indonesian Construction Services Development Board',
                'description' => 'License to undertake large-scale construction projects in Indonesia, demonstrating our technical capabilities and financial strength.',
                'issue_date' => '2018-04-30',
                'expiry_date' => '2023-04-29',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'LEED Accredited Professional',
                'issuer' => 'U.S. Green Building Council',
                'description' => 'Certification for professionals who have demonstrated expertise in sustainable building practices and the LEED rating system.',
                'issue_date' => '2022-01-18',
                'expiry_date' => '2025-01-17',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Project Management Professional (PMP)',
                'issuer' => 'Project Management Institute',
                'description' => 'Globally recognized certification for project management professionals, demonstrating our expertise in leading complex construction projects.',
                'issue_date' => '2020-10-12',
                'expiry_date' => '2023-10-11',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Industry Safety Excellence Award',
                'issuer' => 'Indonesian Construction Safety Association',
                'description' => 'Recognition of our outstanding safety record and commitment to maintaining high safety standards on all projects.',
                'issue_date' => '2022-05-20',
                'expiry_date' => null,
                'is_active' => true,
                'sort_order' => 8,
            ],
        ];

        foreach ($certifications as $certification) {
            Certification::create([
                'name' => $certification['name'],
                'issuer' => $certification['issuer'],
                'description' => $certification['description'],
                'issue_date' => $certification['issue_date'],
                'expiry_date' => $certification['expiry_date'],
                'is_active' => $certification['is_active'],
                'sort_order' => $certification['sort_order'],
            ]);
        }
    }
}