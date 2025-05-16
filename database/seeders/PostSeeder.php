<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin and staff users for post authors
        $authors = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'staff']);
        })->pluck('id')->toArray();
        
        // Get category IDs
        $categories = PostCategory::pluck('id', 'name')->toArray();
        
        $posts = [
            [
                'title' => 'The Future of Sustainable Construction in Indonesia',
                'excerpt' => 'Exploring the growing trend of sustainable construction practices in Indonesia and how they are shaping the future of the industry.',
                'content' => '<p>Sustainable construction is rapidly gaining momentum in Indonesia as awareness of environmental issues grows and regulations become more stringent. This shift towards greener building practices is not just a trend but a necessary evolution in how we approach construction in our country.</p>
                <h2>Key Sustainable Construction Trends in Indonesia</h2>
                <p>Several key trends are defining the future of sustainable construction in Indonesia:</p>
                <ul>
                    <li><strong>Energy-Efficient Designs</strong>: Buildings are increasingly being designed to minimize energy consumption through proper orientation, natural ventilation, and high-performance building envelopes.</li>
                    <li><strong>Renewable Energy Integration</strong>: Solar panels, wind turbines, and other renewable energy sources are being incorporated into building designs to reduce reliance on the national grid and decrease carbon footprints.</li>
                    <li><strong>Water Conservation</strong>: Rainwater harvesting systems, greywater recycling, and water-efficient fixtures are becoming standard features in sustainable buildings.</li>
                    <li><strong>Local and Sustainable Materials</strong>: The use of locally sourced, renewable, and recycled materials is growing, reducing transportation emissions and supporting local economies.</li>
                </ul>
                <h2>Challenges and Opportunities</h2>
                <p>While the benefits of sustainable construction are clear, there are still challenges to widespread adoption in Indonesia:</p>
                <ul>
                    <li><strong>Initial Costs</strong>: The perception that sustainable construction is more expensive remains a barrier, despite evidence of long-term cost savings.</li>
                    <li><strong>Knowledge Gap</strong>: There is still a need for more education and training in sustainable construction techniques among industry professionals.</li>
                    <li><strong>Supply Chain Issues</strong>: Access to sustainable materials and technologies can be limited in some regions.</li>
                </ul>
                <p>However, these challenges also present opportunities for innovation and growth in the industry. Companies that can provide sustainable solutions at competitive prices will be well-positioned for success in the evolving market.</p>
                <h2>The Role of Government and Industry</h2>
                <p>Both government regulations and industry initiatives are driving the shift towards sustainable construction:</p>
                <ul>
                    <li>The Indonesian government has introduced green building regulations and incentives for sustainable projects.</li>
                    <li>Industry associations are developing certification systems and standards for sustainable construction.</li>
                    <li>Leading construction companies are investing in research and development of sustainable technologies and practices.</li>
                </ul>
                <h2>Conclusion</h2>
                <p>The future of construction in Indonesia is undoubtedly green. As environmental concerns become more pressing and the benefits of sustainable construction become more widely recognized, we can expect to see continued growth in this sector. By embracing sustainable practices, the construction industry can contribute significantly to Indonesia\'s environmental goals while creating healthier, more efficient buildings for all.</p>',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(5),
                'featured' => true,
                'categories' => ['Sustainable Construction', 'Industry Insights'],
            ],
            [
                'title' => 'How to Choose the Right Construction Materials for Your Project',
                'excerpt' => 'A comprehensive guide to selecting the best materials for your construction project based on budget, sustainability, and performance requirements.',
                'content' => '<p>Selecting the right materials is one of the most critical decisions in any construction project. The choices you make will impact not only the initial cost but also the long-term performance, maintenance requirements, and environmental footprint of your building. This guide will help you navigate the complex world of construction materials.</p>
                <h2>Key Factors to Consider</h2>
                <p>When selecting construction materials, several factors should influence your decision:</p>
                <h3>1. Structural Requirements</h3>
                <p>The structural demands of your project should be your primary consideration. Different materials offer varying levels of:</p>
                <ul>
                    <li>Strength and load-bearing capacity</li>
                    <li>Durability and resistance to environmental factors</li>
                    <li>Fire resistance</li>
                    <li>Seismic performance</li>
                </ul>
                <p>Consulting with a structural engineer is essential to ensure your material choices meet the specific requirements of your project.</p>
                <h3>2. Budget Constraints</h3>
                <p>Your budget will significantly influence your material selection. Consider not only the initial purchase cost but also:</p>
                <ul>
                    <li>Installation costs and labor requirements</li>
                    <li>Long-term maintenance expenses</li>
                    <li>Energy efficiency and potential savings</li>
                    <li>Lifespan and replacement costs</li>
                </ul>
                <p>Sometimes investing in higher-quality materials initially can lead to significant savings over the life of the building.</p>
                <h3>3. Environmental Impact</h3>
                <p>Sustainable construction is increasingly important. Consider these aspects of environmental performance:</p>
                <ul>
                    <li>Embodied energy (energy used in production and transportation)</li>
                    <li>Recycled content and recyclability</li>
                    <li>Local sourcing to reduce transportation emissions</li>
                    <li>Toxicity and indoor air quality impacts</li>
                    <li>Biodegradability and end-of-life disposal</li>
                </ul>
                <h3>4. Aesthetic Requirements</h3>
                <p>The visual appeal of your building is also important. Materials should align with your design vision in terms of:</p>
                <ul>
                    <li>Color, texture, and finish options</li>
                    <li>Architectural style compatibility</li>
                    <li>Aging and weathering characteristics</li>
                </ul>
                <h2>Common Construction Materials: Pros and Cons</h2>
                <h3>Concrete</h3>
                <p><strong>Pros:</strong> Excellent compressive strength, durability, fire resistance, and versatility.</p>
                <p><strong>Cons:</strong> High carbon footprint, heavy weight, and poor tensile strength without reinforcement.</p>
                <h3>Steel</h3>
                <p><strong>Pros:</strong> High strength-to-weight ratio, ductility, precision, speed of construction, and recyclability.</p>
                <p><strong>Cons:</strong> Susceptibility to corrosion, high thermal conductivity, and high embodied energy.</p>
                <h3>Timber</h3>
                <p><strong>Pros:</strong> Renewable resource, good insulation properties, low embodied energy, and aesthetic appeal.</p>
                <p><strong>Cons:</strong> Vulnerability to moisture and insects, lower fire resistance, and dimensional stability issues.</p>
                <h2>Conclusion</h2>
                <p>Choosing the right construction materials requires balancing multiple factors including performance requirements, budget constraints, environmental considerations, and aesthetic goals. By carefully evaluating these factors and understanding the properties of different materials, you can make informed decisions that will contribute to the success of your construction project. When in doubt, consult with professionals who can provide expert guidance based on your specific needs.</p>',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(12),
                'featured' => false,
                'categories' => ['Construction Trends', 'Industry Insights'],
            ],
            [
                'title' => 'CV Usaha Prima Lestari Completes Harmony Office Tower Project Ahead of Schedule',
                'excerpt' => 'We are proud to announce the successful completion of the Harmony Office Tower project, a 15-story commercial building in Jakarta\'s business district.',
                'content' => '<p>CV Usaha Prima Lestari is pleased to announce the successful completion of the Harmony Office Tower, a state-of-the-art 15-story office building located in Jakarta\'s Central Business District. The project was completed two weeks ahead of schedule and within the allocated budget, demonstrating our team\'s commitment to excellence and efficiency.</p>
                <h2>Project Overview</h2>
                <p>The Harmony Office Tower features:</p>
                <ul>
                    <li>15 floors of premium office space with flexible layouts</li>
                    <li>A grand lobby with 24/7 security and visitor management systems</li>
                    <li>High-speed elevators with destination control</li>
                    <li>Dedicated parking levels with EV charging stations</li>
                    <li>Retail spaces on the ground floor</li>
                    <li>A rooftop garden for tenants</li>
                    <li>Energy-efficient building systems including a glass curtain wall that maximizes natural light while maintaining thermal efficiency</li>
                </ul>
                <h2>Overcoming Challenges</h2>
                <p>The project presented several challenges, including limited construction space in the densely populated business district, a tight timeline, and the need to integrate smart building technology with traditional building systems. Our team implemented innovative solutions to address these challenges:</p>
                <ul>
                    <li>A detailed logistics plan for material delivery and storage in the limited space</li>
                    <li>Utilization of prefabricated components to accelerate construction</li>
                    <li>Deployment of a specialized team for seamless integration of smart building systems</li>
                    <li>Collaboration with energy consultants to optimize building performance</li>
                </ul>
                <h2>Sustainable Achievement</h2>
                <p>We are particularly proud that the Harmony Office Tower has achieved LEED Gold certification for sustainable design and construction. The building incorporates numerous green features resulting in approximately 30% energy savings compared to conventional office buildings.</p>
                <h2>Client Satisfaction</h2>
                <p>"Working with CV Usaha Prima Lestari has been an excellent experience," said John Smith, Project Manager at PT Maju Bersama. "Their team is professional, responsive, and committed to delivering high-quality results. The Harmony Office Tower project was completed ahead of schedule and exceeded our expectations in terms of quality and finish."</p>
                <h2>Commercial Success</h2>
                <p>The commercial success of the project is already evident, with 85% of the office space pre-leased before completion. The building is now operational and has been well-received by tenants and visitors alike.</p>
                <p>This achievement reinforces CV Usaha Prima Lestari\'s reputation as a leading construction company in Indonesia, capable of delivering complex projects to the highest standards of quality, safety, and sustainability.</p>
                <p>We would like to thank all team members, subcontractors, and partners who contributed to the successful completion of this landmark project.</p>',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(20),
                'featured' => true,
                'categories' => ['Company News', 'Project Highlights'],
            ],
            [
                'title' => 'Top Construction Trends to Watch in 2025',
                'excerpt' => 'Stay ahead of the curve with these emerging trends that are shaping the future of the construction industry in Indonesia and beyond.',
                'content' => '<p>The construction industry is constantly evolving, with new technologies, methodologies, and priorities reshaping how projects are conceptualized, designed, and executed. As we move through 2025, several significant trends are emerging that will define the future of construction. Understanding these trends is crucial for companies looking to maintain their competitive edge and adapt to changing market demands.</p>
                <h2>1. Modular and Prefabricated Construction</h2>
                <p>Modular and prefabricated construction techniques continue to gain momentum, offering significant advantages in terms of:</p>
                <ul>
                    <li>Reduced construction time (up to 50% faster than traditional methods)</li>
                    <li>Lower costs through standardization and efficient factory production</li>
                    <li>Improved quality control in controlled factory environments</li>
                    <li>Reduced construction waste</li>
                    <li>Minimized on-site disruption and environmental impact</li>
                </ul>
                <p>In Indonesia, where rapid urbanization creates demand for quick and efficient construction, modular methods are increasingly being adopted for both residential and commercial projects.</p>
                <h2>2. Digital Transformation and BIM</h2>
                <p>Building Information Modeling (BIM) and other digital tools are moving from being competitive advantages to industry standards. Key developments include:</p>
                <ul>
                    <li>Integration of BIM with virtual and augmented reality for immersive project visualization</li>
                    <li>Cloud-based collaboration platforms enabling real-time coordination between global teams</li>
                    <li>Digital twins providing live data for building management and maintenance</li>
                    <li>Mobile applications bringing digital tools directly to the construction site</li>
                </ul>
                <p>These technologies are revolutionizing how buildings are designed, constructed, and maintained throughout their lifecycle.</p>
                <h2>3. Sustainable and Net-Zero Construction</h2>
                <p>Environmental concerns are driving a strong push toward sustainability in construction:</p>
                <ul>
                    <li>Net-zero energy buildings that produce as much energy as they consume</li>
                    <li>Carbon-neutral construction methods focused on reducing embodied carbon</li>
                    <li>Integration of renewable energy systems as standard building components</li>
                    <li>Circular economy approaches that prioritize material reuse and recycling</li>
                    <li>Biophilic design principles incorporating natural elements into buildings</li>
                </ul>
                <p>In Indonesia, where climate resilience is particularly important, sustainable construction practices are becoming increasingly valued by both clients and regulators.</p>
                <h2>4. Robotics and Automation</h2>
                <p>Automation is addressing labor shortages and improving efficiency across the construction industry:</p>
                <ul>
                    <li>Autonomous equipment for earthmoving and material handling</li>
                    <li>Bricklaying and concrete-placing robots increasing precision and speed</li>
                    <li>Drones for site surveying, monitoring, and inspection</li>
                    <li>3D printing technologies for components and even entire structures</li>
                </ul>
                <p>While initial investment costs are high, the long-term benefits in productivity and quality are driving adoption.</p>
                <h2>5. Advanced Materials</h2>
                <p>Innovative materials are changing what\'s possible in construction:</p>
                <ul>
                    <li>Self-healing concrete that can repair its own cracks</li>
                    <li>Transparent aluminum offering the strength of metal with the transparency of glass</li>
                    <li>Mass timber enabling taller wooden buildings with lower carbon footprints</li>
                    <li>Aerogels and other super-insulating materials improving energy efficiency</li>
                    <li>Graphene-enhanced materials with superior strength and conductivity</li>
                </ul>
                <p>These materials are enabling more ambitious, efficient, and sustainable designs.</p>
                <h2>Conclusion</h2>
                <p>The construction industry is at a pivotal point of transformation. Companies that embrace these emerging trends will be better positioned to meet the evolving needs of clients, address environmental challenges, and overcome persistent industry issues such as productivity and labor shortages. At CV Usaha Prima Lestari, we are committed to staying at the forefront of these developments, incorporating innovative technologies and approaches into our projects to deliver maximum value to our clients.</p>',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(30),
                'featured' => false,
                'categories' => ['Construction Trends', 'Industry Insights'],
            ],
            [
                'title' => 'The Impact of Smart Building Technology on Modern Construction',
                'excerpt' => 'Exploring how smart building technologies are revolutionizing construction projects and creating more efficient, comfortable, and sustainable buildings.',
                'content' => '<p>Smart building technology is transforming the construction industry, offering unprecedented levels of control, efficiency, and functionality in modern buildings. From automated systems to IoT sensors, these innovations are changing how buildings are designed, constructed, and operated. This article explores the major impacts of smart building technology on the construction industry.</p>
                <h2>What Makes a Building "Smart"?</h2>
                <p>A smart building uses automated processes to control various building operations including heating, ventilation, air conditioning, lighting, security, and other systems. These buildings leverage:</p>
                <ul>
                    <li><strong>IoT Sensors</strong>: Devices that collect and transmit data about building conditions and usage patterns</li>
                    <li><strong>Integrated Systems</strong>: Connected platforms that allow different building systems to communicate and coordinate</li>
                    <li><strong>Data Analytics</strong>: Software that analyzes building data to optimize performance and predict maintenance needs</li>
                    <li><strong>User Interfaces</strong>: Dashboards and apps that allow users to monitor and control building functions</li>
                </ul>
                <h2>Key Benefits of Smart Building Technology</h2>
                <h3>1. Enhanced Energy Efficiency</h3>
                <p>Smart buildings can significantly reduce energy consumption through:</p>
                <ul>
                    <li>Automated lighting that adjusts based on occupancy and natural light levels</li>
                    <li>Intelligent HVAC systems that optimize operation based on occupancy, weather conditions, and time of day</li>
                    <li>Smart meters that provide real-time energy usage data</li>
                    <li>Automated blinds and shading systems that optimize natural light and reduce thermal gain</li>
                </ul>
                <p>These systems can reduce energy consumption by 20-30% compared to conventional buildings.</p>
                <h3>2. Improved Occupant Comfort and Productivity</h3>
                <p>Smart technologies enhance the user experience through:</p>
                <ul>
                    <li>Personalized environmental controls for individual workspaces</li>
                    <li>Optimal lighting conditions that support human circadian rhythms</li>
                    <li>Improved air quality monitoring and management</li>
                    <li>Touchless access and navigation systems</li>
                </ul>
                <p>Studies show that improved indoor environmental quality can increase productivity by 8-11% in commercial buildings.</p>
                <h3>3. Proactive Maintenance</h3>
                <p>Smart building systems enable:</p>
                <ul>
                    <li>Predictive maintenance based on performance data rather than fixed schedules</li>
                    <li>Early detection of equipment issues before they cause failures</li>
                    <li>Automated service requests when systems need attention</li>
                    <li>Remote diagnostics that allow many issues to be resolved without on-site visits</li>
                </ul>
                <p>This approach can reduce maintenance costs by up to 30% while extending equipment lifespan.</p>
                <h3>4. Enhanced Safety and Security</h3>
                <p>Smart buildings offer advanced safety features including:</p>
                <ul>
                    <li>Integrated security systems with biometric access control</li>
                    <li>Automated emergency response systems</li>
                    <li>Real-time occupancy tracking for emergency management</li>
                    <li>Advanced fire detection and suppression systems</li>
                </ul>
                <h2>Implementation Challenges</h2>
                <p>Despite the benefits, implementing smart building technology presents several challenges:</p>
                <ul>
                    <li><strong>Integration Complexity</strong>: Ensuring different systems work together seamlessly</li>
                    <li><strong>Initial Costs</strong>: Higher upfront investment compared to conventional buildings</li>
                    <li><strong>Cybersecurity Concerns</strong>: Protecting connected systems from potential breaches</li>
                    <li><strong>Technical Expertise</strong>: Requiring specialized knowledge for installation and maintenance</li>
                </ul>
                <h2>The Future of Smart Buildings</h2>
                <p>The smart building market is projected to grow at a CAGR of 11.33% between 2023 and 2030. Future developments will likely include:</p>
                <ul>
                    <li>Greater integration with smart city infrastructure</li>
                    <li>More sophisticated AI for building management</li>
                    <li>Increased use of digital twins for building monitoring and simulation</li>
                    <li>Further development of occupant-centered technologies</li>
                </ul>
                <h2>Conclusion</h2>
                <p>Smart building technology is reshaping the construction industry, offering benefits that extend from the design phase through the entire building lifecycle. While challenges exist, the long-term advantages in terms of efficiency, comfort, and sustainability make smart building technologies a crucial consideration for modern construction projects. As these technologies continue to evolve, we can expect to see even more innovative applications that further enhance building performance and user experience.</p>',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(45),
                'featured' => false,
                'categories' => ['Building Technology', 'Construction Trends'],
            ],
            [
                'title' => 'Green Valley Residences Project Showcase: Sustainable Living in Bandung',
                'excerpt' => 'A detailed look at our recently completed Green Valley Residences project, highlighting the sustainable features and community-focused design.',
                'content' => '<p>CV Usaha Prima Lestari is proud to showcase our recently completed Green Valley Residences, a premium residential complex in Bandung that sets new standards for sustainable living. This landmark project combines modern architectural design with eco-friendly features and community-focused spaces.</p>
                <h2>Project Overview</h2>
                <p>Green Valley Residences consists of 5 apartment towers with a total of 350 units. The development features:</p>
                <ul>
                    <li>A mix of 1, 2, and 3-bedroom apartments with modern, energy-efficient designs</li>
                    <li>Extensive landscaped gardens and green spaces covering over 40% of the site area</li>
                    <li>Community facilities including swimming pools, gym, playground, and multipurpose hall</li>
                    <li>Integrated security systems and 24/7 surveillance</li>
                    <li>Underground parking with EV charging stations</li>
                </ul>
                <h2>Sustainable Features</h2>
                <p>Sustainability was a core focus of this project, with numerous features incorporated to minimize environmental impact and reduce operational costs:</p>
                <h3>Water Conservation</h3>
                <ul>
                    <li>Rainwater harvesting system that captures and reuses rainwater for landscape irrigation</li>
                    <li>Water-efficient fixtures in all apartments, reducing water consumption by up to 40%</li>
                    <li>Grey water recycling system for landscape irrigation and toilet flushing</li>
                </ul>
                <h3>Energy Efficiency</h3>
                <ul>
                    <li>Solar panels providing 20% of common area energy needs</li>
                    <li>Energy-efficient LED lighting throughout the complex</li>
                    <li>Smart home systems that optimize energy usage in individual units</li>
                    <li>High-performance building envelope with superior insulation and low-e glass</li>
                </ul>
                <h3>Materials and Construction</h3>
                <ul>
                    <li>Use of locally sourced materials to reduce transportation emissions</li>
                    <li>Low-VOC paints and finishes for improved indoor air quality</li>
                    <li>Construction waste management plan that diverted 75% of waste from landfills</li>
                    <li>Sustainable timber from certified sources</li>
                </ul>
                <h2>Project Execution</h2>
                <p>The project presented several challenges, including:</p>
                <ul>
                    <li>Complex phased construction to allow for staged completion and sales</li>
                    <li>Balancing premium features with budget constraints</li>
                    <li>Implementing sustainable technologies in a residential setting</li>
                    <li>Managing the expectations of multiple stakeholders</li>
                </ul>
                <p>Our team addressed these challenges through:</p>
                <ul>
                    <li>Developing a detailed phasing plan aligned with the sales strategy</li>
                    <li>Value engineering to optimize costs without compromising quality</li>
                    <li>Partnering with sustainability experts for renewable energy integration</li>
                    <li>Regular stakeholder meetings and progress updates</li>
                </ul>
                <h2>Client Testimonial</h2>
                <p>"We are extremely satisfied with the construction services provided by CV Usaha Prima Lestari," said Sarah Johnson, Director at PT Harmoni Sentosa. "They completed our Green Valley Residences project on time and within budget, exceeding our expectations. Their attention to detail and commitment to quality is truly impressive. The team was responsive to our needs and made the entire construction process smooth and transparent."</p>
                <h2>Results and Recognition</h2>
                <p>The project outcomes exceeded expectations:</p>
                <ul>
                    <li>Completed all phases on schedule over a 36-month period</li>
                    <li>Achieved 40% reduction in water consumption through sustainable systems</li>
                    <li>All units sold within 6 months of completion</li>
                    <li>Won "Best Residential Development" award from Indonesia Property Awards</li>
                </ul>
                <h2>Conclusion</h2>
                <p>Green Valley Residences demonstrates CV Usaha Prima Lestari\'s commitment to sustainable construction and our ability to deliver high-quality residential projects. The success of this development has established a new benchmark for sustainable living in Bandung and reinforces our position as a leader in innovative construction solutions.</p>',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(52),
                'featured' => false,
                'categories' => ['Project Highlights', 'Sustainable Construction'],
            ],
            [
                'title' => 'CV Usaha Prima Lestari Announces New Certification in Green Building Standards',
                'excerpt' => 'We are pleased to announce that CV Usaha Prima Lestari has received Green Building Certification from the Green Building Council Indonesia.',
                'content' => '<p>CV Usaha Prima Lestari is proud to announce our recent certification from the Green Building Council Indonesia, recognizing our expertise and commitment to sustainable construction practices. This certification marks an important milestone in our company\'s ongoing efforts to promote environmentally responsible building methods and technologies.</p>
                <h2>About the Certification</h2>
                <p>The Green Building Certification is awarded to companies that demonstrate proficiency in designing and constructing buildings that meet stringent environmental standards. The certification process involved a comprehensive assessment of our:</p>
                <ul>
                    <li>Technical capabilities in sustainable construction</li>
                    <li>Project portfolio showcasing green building implementations</li>
                    <li>Staff qualifications and training in sustainable building practices</li>
                    <li>Quality management systems for ensuring environmental performance</li>
                    <li>Commitment to continuous improvement in sustainability</li>
                </ul>
                <h2>Why This Matters</h2>
                <p>This certification is more than just recognition of our capabilities; it represents our commitment to contributing to a more sustainable built environment in Indonesia. Green buildings offer numerous benefits including:</p>
                <ul>
                    <li>Reduced environmental impact through lower energy and water consumption</li>
                    <li>Improved indoor environmental quality and occupant health</li>
                    <li>Lower operational costs for building owners</li>
                    <li>Enhanced building value and marketability</li>
                    <li>Contribution to Indonesia\'s climate goals and sustainable development targets</li>
                </ul>
                <h2>Our Sustainable Projects</h2>
                <p>Several of our recent projects have incorporated green building principles, including:</p>
                <ul>
                    <li><strong>Harmony Office Tower</strong>: Achieved LEED Gold certification with 30% energy savings compared to conventional office buildings</li>
                    <li><strong>Green Valley Residences</strong>: Features rainwater harvesting, solar power, and extensive green spaces</li>
                    <li><strong>Tech Innovation Campus</strong>: Designed to LEED Platinum standards with net-zero energy performance for common areas</li>
                </ul>
                <h2>Looking Forward</h2>
                <p>With this certification, we are expanding our capabilities and service offerings in sustainable construction. Our future initiatives include:</p>
                <ul>
                    <li>Additional staff training and certification in specialized areas of green building</li>
                    <li>Expanded consulting services for clients seeking to achieve green building certifications</li>
                    <li>Research and development into innovative sustainable construction techniques</li>
                    <li>Partnerships with suppliers of environmentally friendly building materials</li>
                    <li>Community education programs on the benefits of sustainable buildings</li>
                </ul>
                <h2>Leadership Statement</h2>
                <p>"This certification represents an important step in our company\'s journey toward more sustainable construction practices," said Budi Santoso, Director of CV Usaha Prima Lestari. "We are committed to not only meeting current environmental standards but pushing the boundaries of what\'s possible in green building. Our goal is to make sustainable construction the norm rather than the exception in Indonesia\'s building industry."</p>
                <h2>Conclusion</h2>
                <p>The achievement of Green Building Certification enhances our ability to serve clients who prioritize sustainability in their construction projects. We look forward to applying our expertise to create buildings that not only meet functional and aesthetic requirements but also contribute to a healthier planet.</p>
                <p>For more information about our sustainable construction services, please contact our office or visit our services page.</p>',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(60),
                'featured' => false,
                'categories' => ['Company News', 'Sustainable Construction'],
            ],
            [
                'title' => 'The Importance of Building Maintenance: Preserving Value and Performance',
                'excerpt' => 'Regular maintenance is essential for maximizing the lifespan, performance, and value of buildings. This article explores key maintenance strategies and their benefits.',
                'content' => '<p>Buildings represent significant investments, whether they\'re commercial properties, industrial facilities, or residential complexes. Like any investment, they require proper care to maintain their value and functionality over time. Regular building maintenance is not just about fixing problems as they arise; it\'s a strategic approach to preserving assets, ensuring safety, and optimizing performance.</p>
                <h2>The True Cost of Deferred Maintenance</h2>
                <p>Many building owners and managers are tempted to delay maintenance to save costs in the short term. However, this approach typically leads to higher expenses in the long run. Studies indicate that every $1 saved in deferred maintenance today results in $4-5 in repair costs later. This is because:</p>
                <ul>
                    <li>Minor issues that could be inexpensively fixed often develop into major problems requiring costly repairs</li>
                    <li>System failures can cause collateral damage to other building components</li>
                    <li>Emergency repairs typically cost 3-4 times more than planned maintenance</li>
                    <li>Building downtime during emergency repairs can result in substantial revenue loss</li>
                </ul>
                <h2>Key Building Maintenance Areas</h2>
                <h3>1. Structural Maintenance</h3>
                <p>Regular inspection and maintenance of a building\'s structural elements is fundamental for safety and longevity:</p>
                <ul>
                    <li>Foundation inspections to identify settling or cracking</li>
                    <li>Roof maintenance to prevent leaks and water damage</li>
                    <li>Exterior wall maintenance including repainting, sealing, and repairs to prevent moisture intrusion</li>
                    <li>Structural assessments following significant events like earthquakes or heavy storms</li>
                </ul>
                <h3>2. Mechanical Systems</h3>
                <p>Building mechanical systems require regular attention to maintain efficiency and prevent failures:</p>
                <ul>
                    <li>HVAC system maintenance including filter changes, coil cleaning, and refrigerant checks</li>
                    <li>Elevator and escalator servicing</li>
                    <li>Plumbing system inspections and maintenance to prevent leaks and water damage</li>
                    <li>Electrical system assessments to ensure safety and code compliance</li>
                    <li>Generator testing and maintenance for emergency power reliability</li>
                </ul>
                <h3>3. Building Envelope</h3>
                <p>The building envelope (exterior walls, windows, doors, and roof) serves as the first line of defense against the elements:</p>
                <ul>
                    <li>Window and door maintenance including weatherstripping and seal inspection</li>
                    <li>Facade cleaning and inspection</li>
                    <li>Roof maintenance including cleaning gutters and inspecting flashings</li>
                    <li>Sealant inspection and replacement as needed</li>
                </ul>
                <h3>4. Interior Finishes</h3>
                <p>Regular attention to interior finishes maintains appearance and prevents deterioration:</p>
                <ul>
                    <li>Floor maintenance appropriate to the material (carpet cleaning, tile regrouting, wood refinishing)</li>
                    <li>Wall repair and repainting</li>
                    <li>Ceiling inspection for water stains or damage</li>
                    <li>Cabinet and countertop maintenance</li>
                </ul>
                <h2>Preventive vs. Reactive Maintenance</h2>
                <p>Building maintenance approaches generally fall into two categories:</p>
                <h3>Reactive Maintenance</h3>
                <p>This "run-to-failure" approach addresses problems only after they occur. While requiring less initial planning, reactive maintenance typically results in:</p>
                <ul>
                    <li>Higher overall costs due to emergency repair premiums</li>
                    <li>Unpredictable budget requirements</li>
                    <li>Increased downtime and disruption</li>
                    <li>Shorter equipment and building system lifespans</li>
                </ul>
                <h3>Preventive Maintenance</h3>
                <p>This proactive approach involves regular scheduled maintenance based on time intervals or usage metrics. Benefits include:</p>
                <ul>
                    <li>Reduced emergency repairs (up to 70% fewer)</li>
                    <li>Extended equipment and building system life (up to 30% longer)</li>
                    <li>Improved energy efficiency (10-20% savings)</li>
                    <li>Predictable maintenance costs for better budgeting</li>
                    <li>Reduced disruption to building occupants</li>
                </ul>
                <h2>The Evolution to Predictive Maintenance</h2>
                <p>Modern technology is enabling a shift toward predictive maintenance, which uses data analytics and IoT sensors to predict when maintenance will be needed based on actual equipment condition rather than fixed schedules. This approach offers:</p>
                <ul>
                    <li>Even greater cost efficiency by performing maintenance only when truly needed</li>
                    <li>Early detection of potential issues before they cause failures</li>
                    <li>Detailed insights into building performance patterns</li>
                    <li>Optimization of maintenance resources</li>
                </ul>
                <h2>Building a Comprehensive Maintenance Program</h2>
                <p>An effective building maintenance program should include:</p>
                <ul>
                    <li><strong>Documentation</strong>: Complete records of all building systems, equipment, and maintenance history</li>
                    <li><strong>Scheduled Inspections</strong>: Regular professional assessments of all building components</li>
                    <li><strong>Maintenance Calendars</strong>: Detailed schedules for routine maintenance tasks</li>
                    <li><strong>Budget Planning</strong>: Adequate financial resources allocated for both routine and capital maintenance</li>
                    <li><strong>Qualified Professionals</strong>: Access to trained maintenance personnel or contractors</li>
                    <li><strong>Emergency Protocols</strong>: Clear procedures for addressing urgent maintenance issues</li>
                </ul>
                <h2>Conclusion</h2>
                <p>Building maintenance is not an optional expense but a critical investment in protecting and preserving valuable assets. A well-planned maintenance program enhances building safety, extends useful life, improves occupant comfort, and maximizes return on investment. By transitioning from reactive to preventive and even predictive maintenance approaches, building owners and managers can ensure their properties remain functional, efficient, and valuable for decades to come.</p>',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(75),
                'featured' => false,
                'categories' => ['Building Maintenance', 'Industry Insights'],
            ],
            [
                'title' => 'CV Usaha Prima Lestari Welcomes New Project Director',
                'excerpt' => 'We are pleased to announce the appointment of Ir. Hendra Wijaya as our new Project Director, bringing over 20 years of construction industry experience to our team.',
                'content' => '<p>CV Usaha Prima Lestari is delighted to announce the appointment of Ir. Hendra Wijaya as our new Project Director. With over 20 years of experience in the construction industry, Ir. Wijaya brings valuable expertise and leadership that will further strengthen our project delivery capabilities and support our continued growth.</p>
                <h2>About Ir. Hendra Wijaya</h2>
                <p>Ir. Wijaya holds a Master\'s degree in Civil Engineering from Institut Teknologi Bandung and is a certified Professional Engineer. His extensive career includes leadership roles at several prominent construction companies in Indonesia, where he successfully managed major commercial, residential, and infrastructure projects.</p>
                <p>His notable achievements include:</p>
                <ul>
                    <li>Leading the construction of a 40-story mixed-use development in Jakarta</li>
                    <li>Managing the delivery of a major infrastructure project completed 3 months ahead of schedule</li>
                    <li>Implementing innovative construction methodologies that reduced project costs by 15%</li>
                    <li>Developing and implementing enhanced quality control systems</li>
                    <li>Mentoring junior engineers and project managers throughout his career</li>
                </ul>
                <h2>Role and Responsibilities</h2>
                <p>As Project Director, Ir. Wijaya will oversee our portfolio of construction projects, with particular focus on:</p>
                <ul>
                    <li>Enhancing project delivery methodologies and quality management</li>
                    <li>Implementing advanced project management technologies and processes</li>
                    <li>Strengthening client relationships and communication protocols</li>
                    <li>Mentoring and developing our project management team</li>
                    <li>Contributing to business development and strategic planning</li>
                </ul>
                <h2>Leadership Statement</h2>
                <p>"We are thrilled to welcome Ir. Hendra Wijaya to our leadership team," said Budi Santoso, Director of CV Usaha Prima Lestari. "His wealth of experience, technical expertise, and leadership skills will be invaluable as we continue to grow and take on increasingly complex projects. Ir. Wijaya shares our commitment to quality, innovation, and client satisfaction, making him a perfect fit for our company culture."</p>
                <h2>A Word from Ir. Wijaya</h2>
                <p>"I am excited to join CV Usaha Prima Lestari and contribute to its continued success," said Ir. Wijaya. "The company has built an impressive reputation for quality construction and client satisfaction, and I look forward to working with the talented team to enhance project delivery capabilities and drive innovation. The construction industry in Indonesia is evolving rapidly, and I am passionate about helping the company stay at the forefront of best practices."</p>
                <h2>Looking Forward</h2>
                <p>The appointment of Ir. Wijaya comes at an exciting time for CV Usaha Prima Lestari as we continue to expand our project portfolio and implement new technologies and methodologies. His leadership will be instrumental in ensuring we maintain our high standards of quality and client satisfaction while pursuing growth opportunities.</p>
                <p>Please join us in welcoming Ir. Hendra Wijaya to the CV Usaha Prima Lestari family.</p>',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(90),
                'featured' => false,
                'categories' => ['Company News'],
            ],
        ];

        foreach ($posts as $postData) {
            // Create the post
            $post = Post::create([
                'title' => $postData['title'],
                'slug' => Str::slug($postData['title']),
                'excerpt' => $postData['excerpt'],
                'content' => $postData['content'],
                'user_id' => $authors[array_rand($authors)],
                'status' => $postData['status'],
                'published_at' => $postData['published_at'],
                'featured' => $postData['featured'],
            ]);
            
        }
    }
}