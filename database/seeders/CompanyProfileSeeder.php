<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use Illuminate\Database\Seeder;

class CompanyProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompanyProfile::create([
            'company_name' => 'CV Usaha Prima Lestari',
            'tagline' => 'Building Excellence, Crafting Quality',
            'about' => '<p>CV Usaha Prima Lestari was established in 2008 with a vision to become a leading construction and general supplier company in Indonesia. We started as a small team with big dreams, and over the years, we have grown into a trusted name in the industry.</p>
                      <p>With over 250+ successful projects completed and a team of 50+ skilled professionals, we continue to deliver excellence in every project we undertake. Our commitment to quality, innovation, and customer satisfaction has been the cornerstone of our success.</p>
                      <p>We specialize in various construction services including building construction, infrastructure development, renovation, and general supply of construction materials. Our experienced team ensures that all projects are completed with the highest standards of quality and safety.</p>',
            'vision' => '<p>To become the most trusted and preferred construction and general supplier company in Indonesia, recognized for excellence, innovation, and sustainable development practices.</p>',
            'mission' => '<p>To deliver high-quality construction and supply services that exceed customer expectations, while adhering to the highest standards of safety, integrity, and professionalism.</p>',
            'history' => '<p>Founded in 2008, CV Usaha Prima Lestari began operations in Jakarta with just 5 employees. The company initially focused on small residential projects and gradually expanded its services to include commercial and industrial constructions.</p>
                        <p>In 2012, we expanded our operations to include general supplies for construction materials, which allowed us to provide more comprehensive services to our clients. By 2015, we had established ourselves as a reliable name in the construction industry in the Jakarta metropolitan area.</p>
                        <p>In 2018, we celebrated our 10th anniversary by opening a new office in Bandung to serve clients in West Java. Today, we continue to grow and expand our services to meet the evolving needs of the construction industry.</p>',
            'values' => json_encode([
                'Integrity: We conduct our business with honesty, transparency, and ethical practices.',
                'Excellence: We strive for the highest standards in all aspects of our work.',
                'Innovation: We embrace new technologies and approaches to deliver better solutions.',
                'Safety: We prioritize the well-being of our employees, clients, and communities.',
                'Collaboration: We value teamwork and partnerships to achieve shared goals.'
            ]),
            'phone' => '+62 21 7654 3210',
            'email' => 'info@usahaprimalestari.com',
            'address' => 'Jl. Raya Bogor No. 123',
            'city' => 'Jakarta',
            'postal_code' => '13710',
            'country' => 'Indonesia',
            'facebook' => 'https://facebook.com/usahaprimalestari',
            'twitter' => 'https://twitter.com/usahaprimalestari',
            'instagram' => 'https://instagram.com/usahaprimalestari',
            'linkedin' => 'https://linkedin.com/company/usahaprimalestari',
            'youtube' => 'https://youtube.com/usahaprimalestari',
            'whatsapp' => '+6281234567890',
            'latitude' => '-6.2088',
            'longitude' => '106.8456',
        ]);
    }
}