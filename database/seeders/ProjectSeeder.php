<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get client IDs for reference
        $clients = User::role('client')->get();
        
        $projects = [
            [
                'title' => 'Harmony Office Tower',
                'description' => '<p>The Harmony Office Tower is a modern 15-story office building located in the Central Business District of Jakarta. This project showcases our expertise in commercial construction, featuring state-of-the-art facilities, energy-efficient systems, and premium office spaces designed for productivity and comfort.</p>
                <p>The building includes a grand lobby, high-speed elevators, dedicated parking levels, retail spaces on the ground floor, and a rooftop garden for tenants. The exterior features a glass curtain wall design that maximizes natural light while maintaining energy efficiency.</p>',
                'challenge' => '<p>The main challenges in this project included:</p>
                <ul>
                    <li>Limited construction space in a densely populated business district</li>
                    <li>Tight timeline of 24 months for completion</li>
                    <li>Integration of smart building technology with traditional building systems</li>
                    <li>Meeting stringent energy efficiency requirements</li>
                </ul>',
                'solution' => '<p>Our approach to addressing these challenges:</p>
                <ul>
                    <li>Implemented a detailed logistics plan for material delivery and storage</li>
                    <li>Utilized prefabricated components to accelerate construction</li>
                    <li>Employed a specialized team for smart building systems integration</li>
                    <li>Collaborated with energy consultants to optimize building performance</li>
                </ul>',
                'result' => '<p>The project was successfully completed two weeks ahead of schedule and within budget. Key achievements:</p>
                <ul>
                    <li>Achieved LEED Gold certification for sustainable design and construction</li>
                    <li>Incorporated 30% energy savings compared to conventional office buildings</li>
                    <li>Pre-leased 85% of office space before completion</li>
                    <li>Zero significant safety incidents throughout the construction period</li>
                </ul>',
                'category' => 'Commercial',
                'client_id' => $clients[0]->id,
                'client_name' => 'PT Maju Bersama',
                'location' => 'Jakarta',
                'year' => 2023,
                'status' => 'completed',
                'value' => 'Rp 120 Billion',
                'start_date' => '2021-03-15',
                'end_date' => '2023-02-28',
                'featured' => true,
                'services_used' => json_encode(['Commercial Building Construction', 'Construction Project Management', 'Construction Materials Supply']),
            ],
            [
                'title' => 'Green Valley Residences',
                'description' => '<p>Green Valley Residences is a premium residential complex consisting of 5 apartment towers with a total of 350 units. The development features modern architectural design with a focus on sustainable living and community spaces.</p>
                <p>The project includes extensive landscaping, community facilities like swimming pools, gym, playground, and community hall, as well as integrated security systems and underground parking.</p>',
                'challenge' => '<p>Major challenges in this residential development included:</p>
                <ul>
                    <li>Complex phased construction to allow for staged completion and sales</li>
                    <li>Balancing premium features with budget constraints</li>
                    <li>Implementing sustainable technologies in a residential setting</li>
                    <li>Managing the expectations of multiple stakeholders</li>
                </ul>',
                'solution' => '<p>Our solutions included:</p>
                <ul>
                    <li>Developed a detailed phasing plan that aligned with sales strategy</li>
                    <li>Value engineering to optimize costs without compromising quality</li>
                    <li>Partnered with sustainability experts for renewable energy integration</li>
                    <li>Regular stakeholder meetings and progress updates</li>
                </ul>',
                'result' => '<p>The project outcomes exceeded expectations:</p>
                <ul>
                    <li>Completed all phases on schedule over a 36-month period</li>
                    <li>Achieved 40% reduction in water consumption through rainwater harvesting</li>
                    <li>Solar panels provide 20% of common area energy needs</li>
                    <li>All units sold within 6 months of completion</li>
                    <li>Won "Best Residential Development" award from Indonesia Property Awards</li>
                </ul>',
                'category' => 'Residential',
                'client_id' => $clients[1]->id,
                'client_name' => 'PT Harmoni Sentosa',
                'location' => 'Bandung',
                'year' => 2022,
                'status' => 'completed',
                'value' => 'Rp 200 Billion',
                'start_date' => '2019-05-10',
                'end_date' => '2022-04-30',
                'featured' => true,
                'services_used' => json_encode(['Residential Construction', 'Construction Project Management']),
            ],
            [
                'title' => 'Sentosa Manufacturing Plant',
                'description' => '<p>The Sentosa Manufacturing Plant is a modern industrial facility designed for electronics manufacturing. The 25,000 square meter facility includes production areas, warehousing, quality control labs, office spaces, and employee amenities.</p>
                <p>The project required specialized construction techniques to accommodate heavy machinery, clean room environments, and specific technical requirements for electronics manufacturing.</p>',
                'challenge' => '<p>Key challenges in this industrial project:</p>
                <ul>
                    <li>Meeting strict technical specifications for clean room environments</li>
                    <li>Installing specialized utility systems (compressed air, process cooling, etc.)</li>
                    <li>Ensuring uninterrupted power supply with redundant systems</li>
                    <li>Coordinating with equipment suppliers for installation timing</li>
                </ul>',
                'solution' => '<p>Our approach included:</p>
                <ul>
                    <li>Partnering with clean room specialists for design and construction</li>
                    <li>Bringing in specialized subcontractors for complex utility systems</li>
                    <li>Implementing a comprehensive commissioning plan for all systems</li>
                    <li>Creating a detailed timeline for equipment installation and testing</li>
                </ul>',
                'result' => '<p>The project delivered exceptional results:</p>
                <ul>
                    <li>Facility passed all ISO certification requirements on first inspection</li>
                    <li>Production commenced within 2 weeks of handover</li>
                    <li>Energy-efficient design reduced operational costs by 25%</li>
                    <li>Expandable design allows for future production increases</li>
                </ul>',
                'category' => 'Industrial',
                'client_id' => $clients[2]->id,
                'client_name' => 'CV Karya Makmur',
                'location' => 'Surabaya',
                'year' => 2024,
                'status' => 'completed',
                'value' => 'Rp 150 Billion',
                'start_date' => '2022-08-20',
                'end_date' => '2024-01-15',
                'featured' => true,
                'services_used' => json_encode(['Industrial Construction', 'Construction Project Management', 'Construction Materials Supply']),
            ],
            [
                'title' => 'Skyline Shopping Mall',
                'description' => '<p>Skyline Shopping Mall is a modern retail development featuring 120 retail spaces across 4 floors, a food court, cinema complex, and entertainment zones. The 45,000 square meter development combines contemporary design with practical retail functionality.</p>
                <p>The project included extensive parking facilities, advanced HVAC systems, integrated security, and digital wayfinding technologies to enhance the shopping experience.</p>',
                'challenge' => '<p>The project presented several challenges:</p>
                <ul>
                    <li>Complex structural design for large open spaces and atriums</li>
                    <li>Coordination of multiple tenant fit-outs alongside base building works</li>
                    <li>High-volume public areas requiring specialized safety and crowd management considerations</li>
                    <li>Integration of digital systems for modern retail experience</li>
                </ul>',
                'solution' => '<p>Our solutions included:</p>
                <ul>
                    <li>Collaboration with structural specialists for innovative design solutions</li>
                    <li>Development of a tenant coordination program to manage fit-out schedules</li>
                    <li>Implementation of BIM (Building Information Modeling) for complex system coordination</li>
                    <li>Early engagement with technology vendors for seamless integration</li>
                </ul>',
                'result' => '<p>The completed project achieved:</p>
                <ul>
                    <li>Opening day with 90% occupancy rate</li>
                    <li>Award-winning architectural design</li>
                    <li>State-of-the-art digital infrastructure for retail analytics</li>
                    <li>Efficient energy systems reducing operational costs by 30%</li>
                    <li>Became the most visited shopping destination in the region</li>
                </ul>',
                'category' => 'Commercial',
                'client_id' => null,
                'client_name' => 'PT Retail Developments',
                'location' => 'Jakarta',
                'year' => 2021,
                'status' => 'completed',
                'value' => 'Rp 180 Billion',
                'start_date' => '2019-02-10',
                'end_date' => '2021-06-30',
                'featured' => false,
                'services_used' => json_encode(['Commercial Building Construction', 'Construction Project Management']),
            ],
            [
                'title' => 'Grand Meridian Hotel Renovation',
                'description' => '<p>This project involved the comprehensive renovation of the Grand Meridian Hotel, a 4-star hotel with 200 rooms. The renovation encompassed all guest rooms, public areas, restaurants, conference facilities, and back-of-house areas.</p>
                <p>The hotel remained partially operational during renovation, requiring careful planning and execution to minimize disruption to guests while maintaining the hotel\'s reputation for luxury and comfort.</p>',
                'challenge' => '<p>Major challenges in this renovation project:</p>
                <ul>
                    <li>Phased renovation while maintaining hotel operations</li>
                    <li>Upgrading building systems without extended shutdowns</li>
                    <li>Noise and dust control in an operational hotel environment</li>
                    <li>Coordinating with hotel staff and adjusting to booking schedules</li>
                </ul>',
                'solution' => '<p>Our approach to these challenges:</p>
                <ul>
                    <li>Created a floor-by-floor renovation plan coordinated with booking systems</li>
                    <li>Implemented strict noise control hours and dust containment procedures</li>
                    <li>Utilized prefabricated bathroom pods to reduce on-site construction time</li>
                    <li>Night work for major systems upgrades to minimize operational impact</li>
                </ul>',
                'result' => '<p>The renovation resulted in:</p>
                <ul>
                    <li>Completed 3 weeks ahead of schedule</li>
                    <li>Minimal guest complaints during construction (less than 2%)</li>
                    <li>Increased room rates by 30% post-renovation</li>
                    <li>Upgrade of hotel rating from 4-star to 5-star</li>
                    <li>50% improvement in energy efficiency</li>
                </ul>',
                'category' => 'Renovation',
                'client_id' => null,
                'client_name' => 'Grand Meridian Hotels Group',
                'location' => 'Bali',
                'year' => 2022,
                'status' => 'completed',
                'value' => 'Rp 85 Billion',
                'start_date' => '2021-04-15',
                'end_date' => '2022-07-30',
                'featured' => false,
                'services_used' => json_encode(['Commercial Renovation', 'Construction Project Management', 'Construction Materials Supply']),
            ],
            [
                'title' => 'Coastal Highway Bridge',
                'description' => '<p>The Coastal Highway Bridge is a 500-meter span connecting two sections of the coastal highway. This infrastructure project features a modern cable-stayed design with two main towers and a dual carriageway.</p>
                <p>The project included approach roads, lighting systems, drainage, safety barriers, and monitoring systems to ensure long-term structural integrity in a coastal environment.</p>',
                'challenge' => '<p>Key challenges for this infrastructure project:</p>
                <ul>
                    <li>Construction in a challenging marine environment</li>
                    <li>Complex foundation work in poor soil conditions</li>
                    <li>Coordination with existing road networks and traffic management</li>
                    <li>Weather-dependent construction activities</li>
                </ul>',
                'solution' => '<p>Our solutions included:</p>
                <ul>
                    <li>Specialized marine construction techniques and equipment</li>
                    <li>Deep pile foundations with extensive testing program</li>
                    <li>Detailed traffic management plan developed with local authorities</li>
                    <li>Flexible scheduling with built-in contingency for weather delays</li>
                </ul>',
                'result' => '<p>The completed bridge achieved:</p>
                <ul>
                    <li>Opening to traffic on schedule despite 20 days of weather delays</li>
                    <li>Enhanced connectivity reducing travel time by 45 minutes</li>
                    <li>Design life of 100+ years with appropriate maintenance</li>
                    <li>Recognition for engineering excellence from the Indonesian Society of Civil Engineers</li>
                </ul>',
                'category' => 'Infrastructure',
                'client_id' => null,
                'client_name' => 'Ministry of Public Works',
                'location' => 'Makassar',
                'year' => 2023,
                'status' => 'completed',
                'value' => 'Rp 250 Billion',
                'start_date' => '2020-11-05',
                'end_date' => '2023-05-20',
                'featured' => false,
                'services_used' => json_encode(['Infrastructure Development', 'Construction Project Management']),
            ],
            [
                'title' => 'Metro Hospital Expansion',
                'description' => '<p>This project involved the expansion of Metro Hospital, adding a new 5-story wing with specialized departments including emergency services, imaging center, outpatient clinics, and 100 additional inpatient beds.</p>
                <p>The construction required seamless integration with the existing hospital building and systems while maintaining full hospital operations throughout the construction period.</p>',
                'challenge' => '<p>Critical challenges in this healthcare project:</p>
                <ul>
                    <li>Maintaining stringent infection control during construction</li>
                    <li>Integration with existing hospital systems without disruption</li>
                    <li>Specialized construction for medical equipment installation</li>
                    <li>Working within a constrained site adjacent to active hospital areas</li>
                </ul>',
                'solution' => '<p>Our approaches included:</p>
                <ul>
                    <li>Implementation of hospital-grade infection control barriers and protocols</li>
                    <li>Detailed phasing plan for systems integration with redundancies</li>
                    <li>Coordination with medical equipment suppliers for precise requirements</li>
                    <li>Just-in-time delivery system to manage materials in limited space</li>
                </ul>',
                'result' => '<p>The expansion project achieved:</p>
                <ul>
                    <li>Zero infection incidents related to construction</li>
                    <li>Seamless transition of hospital systems to expanded facility</li>
                    <li>30% increase in hospital capacity</li>
                    <li>New departments operational within 2 weeks of handover</li>
                    <li>Compliance with all healthcare facility regulations on first inspection</li>
                </ul>',
                'category' => 'Healthcare',
                'client_id' => null,
                'client_name' => 'Metro Healthcare Foundation',
                'location' => 'Medan',
                'year' => 2023,
                'status' => 'completed',
                'value' => 'Rp 110 Billion',
                'start_date' => '2021-07-10',
                'end_date' => '2023-08-15',
                'featured' => false,
                'services_used' => json_encode(['Commercial Building Construction', 'Construction Project Management']),
            ],
            [
                'title' => 'Tech Innovation Campus',
                'description' => '<p>The Tech Innovation Campus is a modern educational facility designed for technology education and research. The campus includes classrooms, laboratories, research facilities, collaborative spaces, auditoriums, and administrative offices across 3 buildings and 30,000 square meters.</p>
                <p>The project emphasized sustainable design, advanced technology infrastructure, and flexible spaces that can adapt to evolving educational approaches.</p>',
                'challenge' => '<p>Primary challenges included:</p>
                <ul>
                    <li>Creating flexible spaces adaptable for different educational purposes</li>
                    <li>Installing complex technological infrastructure throughout the campus</li>
                    <li>Meeting ambitious sustainability goals within budget constraints</li>
                    <li>Coordinating with academic schedule for phased occupancy</li>
                </ul>',
                'solution' => '<p>Our solutions included:</p>
                <ul>
                    <li>Modular design approach for adaptable learning environments</li>
                    <li>Engagement of specialized technology consultants early in design phase</li>
                    <li>Lifecycle cost analysis to justify sustainable technology investments</li>
                    <li>Detailed occupancy phasing aligned with academic semesters</li>
                </ul>',
                'result' => '<p>The completed campus delivered:</p>
                <ul>
                    <li>LEED Platinum certification for all buildings</li>
                    <li>Net-zero energy performance for common areas</li>
                    <li>State-of-the-art technology infrastructure supporting advanced research</li>
                    <li>15% under budget through value engineering without compromising quality</li>
                    <li>Increased enrollment by 40% in technology programs</li>
                </ul>',
                'category' => 'Educational',
                'client_id' => null,
                'client_name' => 'National Institute of Technology',
                'location' => 'Yogyakarta',
                'year' => 2022,
                'status' => 'completed',
                'value' => 'Rp 175 Billion',
                'start_date' => '2020-01-15',
                'end_date' => '2022-12-20',
                'featured' => false,
                'services_used' => json_encode(['Commercial Building Construction', 'Construction Project Management']),
            ],
            [
                'title' => 'Sunrise Villa Resort',
                'description' => '<p>Sunrise Villa Resort is a luxury hospitality development featuring 25 private villas, a main building with restaurants and spa facilities, and extensive landscaped grounds. Located on beachfront property, the resort combines traditional Indonesian architectural elements with modern luxury amenities.</p>
                <p>Each villa includes private pools, outdoor living areas, and premium finishes, creating an exclusive retreat experience for guests.</p>',
                'challenge' => '<p>Key challenges in this resort project:</p>
                <ul>
                    <li>Construction in a remote coastal location with limited infrastructure</li>
                    <li>Balancing traditional architectural elements with modern building systems</li>
                    <li>Environmental protection of sensitive beachfront ecosystem</li>
                    <li>Sourcing and transporting specialty materials and finishes</li>
                </ul>',
                'solution' => '<p>Our approach included:</p>
                <ul>
                    <li>Establishment of temporary construction facilities and housing</li>
                    <li>Collaboration with local artisans for authentic architectural details</li>
                    <li>Implementation of strict environmental protection protocols</li>
                    <li>Advanced logistics planning for material delivery to remote location</li>
                </ul>',
                'result' => '<p>The resort project achieved:</p>
                <ul>
                    <li>Opening in time for peak tourist season despite logistical challenges</li>
                    <li>Authentic integration of local architectural traditions with luxury amenities</li>
                    <li>Environmental certification for sustainable construction practices</li>
                    <li>Won "Best New Resort" award from Indonesia Tourism Board</li>
                    <li>100% booking rate for first six months of operation</li>
                </ul>',
                'category' => 'Hospitality',
                'client_id' => null,
                'client_name' => 'Sunrise Luxury Resorts',
                'location' => 'Lombok',
                'year' => 2023,
                'status' => 'completed',
                'value' => 'Rp 130 Billion',
                'start_date' => '2021-06-10',
                'end_date' => '2023-07-30',
                'featured' => false,
                'services_used' => json_encode(['Commercial Building Construction', 'Construction Project Management', 'Construction Materials Supply']),
            ],
            [
                'title' => 'Central City Apartments',
                'description' => '<p>Currently in progress, this project involves the construction of a mixed-use development featuring 150 apartment units, ground floor retail spaces, and resident amenities including a pool, gym, and community spaces.</p>
                <p>The 20-story building is designed with a focus on urban living, offering modern apartments with smart home features and energy-efficient systems in a prime downtown location.</p>',
                'category' => 'Residential',
                'client_id' => $clients[0]->id,
                'client_name' => 'PT Maju Bersama',
                'location' => 'Jakarta',
                'year' => 2025,
                'status' => 'in_progress',
                'value' => 'Rp 160 Billion',
                'start_date' => '2023-10-15',
                'end_date' => null,
                'featured' => true,
                'services_used' => json_encode(['Residential Construction', 'Construction Project Management', 'Construction Materials Supply']),
            ],
        ];

        foreach ($projects as $project) {
            Project::create([
                'title' => $project['title'],
                'slug' => Str::slug($project['title']),
                'description' => $project['description'],
                'challenge' => $project['challenge'] ?? null,
                'solution' => $project['solution'] ?? null,
                'result' => $project['result'] ?? null,
                'category' => $project['category'],
                'client_id' => $project['client_id'],
                'client_name' => $project['client_name'],
                'location' => $project['location'],
                'year' => $project['year'],
                'status' => $project['status'],
                'value' => $project['value'],
                'start_date' => $project['start_date'],
                'end_date' => $project['end_date'],
                'featured' => $project['featured'],
                'services_used' => $project['services_used'],
            ]);
        }
    }
}