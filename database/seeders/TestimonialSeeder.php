<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use App\Models\Project;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get project IDs
        $projectIds = Project::where('status', 'completed')->pluck('id')->toArray();
        
        $testimonials = [
            [
                'client_name' => 'John Smith',
                'client_position' => 'Project Manager',
                'client_company' => 'PT Maju Bersama',
                'content' => 'Working with CV Usaha Prima Lestari was an excellent experience. Their team is professional, responsive, and committed to delivering high-quality results. The Harmony Office Tower project was completed ahead of schedule and exceeded our expectations in terms of quality and finish. I would highly recommend their services to anyone looking for a reliable construction partner.',
                'rating' => 5,
                'is_active' => true,
                'featured' => true,
            ],
            [
                'client_name' => 'Sarah Johnson',
                'client_position' => 'Director',
                'client_company' => 'PT Harmoni Sentosa',
                'content' => 'We are extremely satisfied with the construction services provided by CV Usaha Prima Lestari. They completed our Green Valley Residences project on time and within budget, exceeding our expectations. Their attention to detail and commitment to quality is truly impressive. The team was responsive to our needs and made the entire construction process smooth and transparent.',
                'rating' => 5,
                'is_active' => true,
                'featured' => true,
            ],
            [
                'client_name' => 'Michael Wong',
                'client_position' => 'Facilities Manager',
                'client_company' => 'CV Karya Makmur',
                'content' => 'The team at CV Usaha Prima Lestari demonstrated exceptional expertise and attention to detail throughout our manufacturing plant project. Their commitment to quality and customer satisfaction is truly commendable. They understood our specialized requirements and delivered a facility that perfectly meets our operational needs. I would not hesitate to work with them again on future projects.',
                'rating' => 5,
                'is_active' => true,
                'featured' => true,
            ],
            [
                'client_name' => 'David Chen',
                'client_position' => 'CEO',
                'client_company' => 'PT Retail Developments',
                'content' => 'The Skyline Shopping Mall project was a complex undertaking, but CV Usaha Prima Lestari managed it flawlessly. Their expertise in commercial construction is evident in the high-quality finish and attention to detail throughout the mall. The project team was professional, communicative, and solutions-oriented. The mall has been a huge success since opening, and we credit much of that to the excellent construction work.',
                'rating' => 5,
                'is_active' => true,
                'featured' => false,
            ],
            [
                'client_name' => 'Amanda Rodriguez',
                'client_position' => 'Operations Director',
                'client_company' => 'Grand Meridian Hotels Group',
                'content' => 'The renovation of our hotel was a challenging project that required working in an operational environment. CV Usaha Prima Lestari handled this challenge expertly, minimizing disruption to our guests while delivering excellent quality work. Their team was considerate, professional, and highly skilled. The renovation has significantly enhanced our hotel\'s appeal and guest satisfaction ratings.',
                'rating' => 4,
                'is_active' => true,
                'featured' => false,
            ],
            [
                'client_name' => 'Robert Tanuwijaya',
                'client_position' => 'Project Director',
                'client_company' => 'Ministry of Public Works',
                'content' => 'The Coastal Highway Bridge project was completed to the highest standards despite challenging site conditions. CV Usaha Prima Lestari\'s technical expertise and project management capabilities ensured that this complex infrastructure project was delivered successfully. Their ability to overcome obstacles and find innovative solutions was particularly impressive.',
                'rating' => 5,
                'is_active' => true,
                'featured' => false,
            ],
            [
                'client_name' => 'Dr. Siti Rahayu',
                'client_position' => 'Executive Director',
                'client_company' => 'Metro Healthcare Foundation',
                'content' => 'The expansion of our hospital required a construction partner who understood the unique requirements of healthcare facilities. CV Usaha Prima Lestari exceeded our expectations, delivering a state-of-the-art medical facility while maintaining strict infection control protocols. Their attention to detail in the specialized construction requirements for medical equipment was particularly valuable.',
                'rating' => 5,
                'is_active' => true,
                'featured' => false,
            ],
            [
                'client_name' => 'Prof. Bambang Suryanto',
                'client_position' => 'Dean',
                'client_company' => 'National Institute of Technology',
                'content' => 'The Tech Innovation Campus project was executed brilliantly by CV Usaha Prima Lestari. Their understanding of our requirements for flexible educational spaces and advanced technology infrastructure was excellent. The campus has transformed our teaching and research capabilities, and the sustainable design features have significantly reduced our operational costs.',
                'rating' => 5,
                'is_active' => true,
                'featured' => false,
            ],
            [
                'client_name' => 'Lisa Tanaka',
                'client_position' => 'Development Director',
                'client_company' => 'Sunrise Luxury Resorts',
                'content' => 'The construction of our villa resort in a remote location presented many challenges, but CV Usaha Prima Lestari handled them all expertly. The finished resort perfectly balances luxury amenities with traditional Indonesian architecture. Their attention to detail and quality craftsmanship is evident throughout the property, and our guests frequently comment on the beautiful construction.',
                'rating' => 4,
                'is_active' => true,
                'featured' => false,
            ],
        ];

        foreach ($testimonials as $index => $testimonial) {
            // Associate with projects if available
            $project_id = null;
            if (isset($projectIds[$index])) {
                $project_id = $projectIds[$index];
            }
            
            Testimonial::create([
                'project_id' => $project_id,
                'client_name' => $testimonial['client_name'],
                'client_position' => $testimonial['client_position'],
                'client_company' => $testimonial['client_company'],
                'content' => $testimonial['content'],
                'rating' => $testimonial['rating'],
                'is_active' => $testimonial['is_active'],
                'featured' => $testimonial['featured'],
            ]);
        }
    }
}