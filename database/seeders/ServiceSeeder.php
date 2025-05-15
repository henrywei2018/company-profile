<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get category IDs
        $constructionCategory = ServiceCategory::where('name', 'Construction Services')->first()->id;
        $supplierCategory = ServiceCategory::where('name', 'General Supplier')->first()->id;
        $maintenanceCategory = ServiceCategory::where('name', 'Building Maintenance')->first()->id;
        $projectMgmtCategory = ServiceCategory::where('name', 'Project Management')->first()->id;
        $renovationCategory = ServiceCategory::where('name', 'Renovation')->first()->id;

        $services = [
            // Construction Services
            [
                'title' => 'Commercial Building Construction',
                'short_description' => 'Professional construction services for office buildings, retail spaces, and other commercial properties.',
                'description' => '<p>Our commercial building construction services are designed to meet the unique needs of businesses in various industries. We offer comprehensive solutions from initial design to final construction, ensuring that every aspect of your commercial property meets the highest standards of quality and functionality.</p>
                <h3>What We Offer:</h3>
                <ul>
                    <li>Office buildings and corporate headquarters</li>
                    <li>Retail spaces and shopping centers</li>
                    <li>Restaurants and hospitality venues</li>
                    <li>Healthcare facilities and medical offices</li>
                    <li>Educational institutions</li>
                </ul>
                <p>With our experienced team of architects, engineers, and construction professionals, we ensure that your commercial project is completed on time, within budget, and to your complete satisfaction.</p>',
                'category_id' => $constructionCategory,
                'featured' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Residential Construction',
                'short_description' => 'High-quality construction services for houses, apartment buildings, and residential complexes.',
                'description' => '<p>Our residential construction services cater to homeowners, property developers, and real estate investors looking to build quality residential properties. From single-family homes to multi-unit apartment complexes, we bring expertise and attention to detail to every project.</p>
                <h3>Our Residential Construction Services:</h3>
                <ul>
                    <li>Custom home building</li>
                    <li>Apartment and condominium construction</li>
                    <li>Residential complex development</li>
                    <li>Luxury home construction</li>
                    <li>Eco-friendly and sustainable housing</li>
                </ul>
                <p>We work closely with our clients throughout the construction process, providing regular updates and ensuring that all specifications and requirements are met. Our goal is to deliver beautiful, functional, and durable homes that exceed our clients\' expectations.</p>',
                'category_id' => $constructionCategory,
                'featured' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Industrial Construction',
                'short_description' => 'Specialized construction services for factories, warehouses, and industrial facilities.',
                'description' => '<p>Our industrial construction services are designed to meet the specific requirements of manufacturing plants, warehouses, and other industrial facilities. We understand the unique challenges of industrial construction and provide tailored solutions that prioritize functionality, efficiency, and safety.</p>
                <h3>Industrial Construction Expertise:</h3>
                <ul>
                    <li>Manufacturing plants and factories</li>
                    <li>Warehouses and distribution centers</li>
                    <li>Industrial parks and complexes</li>
                    <li>Processing facilities</li>
                    <li>Storage facilities</li>
                </ul>
                <p>Our team has extensive experience in designing and constructing industrial buildings that optimize workflow, maximize space utilization, and incorporate advanced technology for improved operational efficiency. We also ensure compliance with all relevant industry regulations and safety standards.</p>',
                'category_id' => $constructionCategory,
                'featured' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            
            // General Supplier
            [
                'title' => 'Construction Materials Supply',
                'short_description' => 'Supply of quality construction materials including cement, steel, lumber, and more.',
                'description' => '<p>As a leading supplier of construction materials, we provide a wide range of high-quality products to meet the diverse needs of construction projects. Our extensive inventory includes essential building materials from trusted manufacturers at competitive prices.</p>
                <h3>Materials We Supply:</h3>
                <ul>
                    <li>Cement, concrete, and aggregates</li>
                    <li>Structural steel and reinforcement bars</li>
                    <li>Lumber and timber products</li>
                    <li>Bricks, blocks, and masonry supplies</li>
                    <li>Roofing materials</li>
                    <li>Insulation products</li>
                    <li>Doors, windows, and glass</li>
                    <li>Plumbing and electrical supplies</li>
                </ul>
                <p>We ensure timely delivery and maintain quality control measures to guarantee that all materials meet industry standards. Our supply chain management system allows us to fulfill large orders efficiently while providing flexible solutions for projects of any size.</p>',
                'category_id' => $supplierCategory,
                'featured' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Construction Equipment Rental',
                'short_description' => 'Rental services for construction equipment and machinery for projects of all sizes.',
                'description' => '<p>Our construction equipment rental service provides cost-effective solutions for contractors and project managers who need specialized machinery without the commitment of purchase. We maintain a diverse fleet of well-maintained equipment ready for immediate deployment to your construction site.</p>
                <h3>Equipment Available for Rent:</h3>
                <ul>
                    <li>Excavators and loaders</li>
                    <li>Bulldozers and graders</li>
                    <li>Cranes and lifting equipment</li>
                    <li>Concrete mixers and pumps</li>
                    <li>Scaffolding and formwork</li>
                    <li>Compressors and generators</li>
                    <li>Welding and cutting tools</li>
                    <li>Specialized construction tools</li>
                </ul>
                <p>We offer flexible rental terms, competitive rates, and prompt delivery and pickup services. Our technical team is available to provide guidance on equipment selection and operation, ensuring that you have the right tools for your specific project requirements.</p>',
                'category_id' => $supplierCategory,
                'featured' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
            
            // Building Maintenance
            [
                'title' => 'Preventive Maintenance',
                'short_description' => 'Regular maintenance services to prevent costly repairs and extend building lifespan.',
                'description' => '<p>Our preventive maintenance services are designed to identify and address potential issues before they develop into major problems. Through regular inspections and maintenance activities, we help property owners protect their investment, ensure building safety, and avoid costly emergency repairs.</p>
                <h3>Preventive Maintenance Services Include:</h3>
                <ul>
                    <li>Scheduled building inspections</li>
                    <li>HVAC system maintenance</li>
                    <li>Electrical system checks and upgrades</li>
                    <li>Plumbing system maintenance</li>
                    <li>Roof inspections and maintenance</li>
                    <li>Structural integrity assessments</li>
                    <li>Fire safety system testing</li>
                    <li>Exterior maintenance (facades, windows, doors)</li>
                </ul>
                <p>We develop customized maintenance plans based on the specific needs of your property, taking into account factors such as building age, usage patterns, and environmental conditions. Our proactive approach helps extend the lifespan of building components and systems while maintaining optimal performance.</p>',
                'category_id' => $maintenanceCategory,
                'featured' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Emergency Repairs',
                'short_description' => 'Quick response services for emergency building repairs and maintenance issues.',
                'description' => '<p>Our emergency repair service provides rapid response to urgent building issues that require immediate attention. We understand that building emergencies can occur at any time and can significantly disrupt operations and compromise safety if not addressed promptly.</p>
                <h3>Emergency Services We Provide:</h3>
                <ul>
                    <li>Water damage and leak repairs</li>
                    <li>Electrical failures and safety hazards</li>
                    <li>Structural damage assessment and temporary solutions</li>
                    <li>HVAC system failures</li>
                    <li>Plumbing emergencies</li>
                    <li>Storm damage repairs</li>
                    <li>Security system failures</li>
                    <li>Emergency boarding and tarping</li>
                </ul>
                <p>Our emergency response team is available 24/7 and equipped to handle a wide range of building emergencies. We prioritize safety and work efficiently to mitigate damage, implement immediate repairs, and restore normal building operations as quickly as possible.</p>',
                'category_id' => $maintenanceCategory,
                'featured' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
            
            // Project Management
            [
                'title' => 'Construction Project Management',
                'short_description' => 'Comprehensive project management services to ensure successful construction project delivery.',
                'description' => '<p>Our construction project management service provides end-to-end oversight of construction projects, ensuring successful delivery on time, within budget, and to the required quality standards. We act as your representative throughout the project lifecycle, managing all aspects of the construction process.</p>
                <h3>Project Management Services Include:</h3>
                <ul>
                    <li>Project planning and scheduling</li>
                    <li>Budget development and cost control</li>
                    <li>Contractor selection and management</li>
                    <li>Quality assurance and control</li>
                    <li>Risk management and mitigation</li>
                    <li>Progress monitoring and reporting</li>
                    <li>Contract administration</li>
                    <li>Health and safety compliance</li>
                    <li>Change management</li>
                </ul>
                <p>Our experienced project managers utilize industry-leading methodologies and digital tools to streamline processes, enhance communication, and provide real-time visibility into project status. We proactively identify and address potential issues, keeping your project on track and minimizing disruptions.</p>',
                'category_id' => $projectMgmtCategory,
                'featured' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Construction Consulting',
                'short_description' => 'Expert consultation services for construction projects, feasibility studies, and technical advice.',
                'description' => '<p>Our construction consulting services provide expert guidance and technical advice to support decision-making throughout the construction project lifecycle. We help clients navigate complex challenges, optimize project outcomes, and achieve their strategic objectives.</p>
                <h3>Consulting Services We Offer:</h3>
                <ul>
                    <li>Feasibility studies and project viability assessment</li>
                    <li>Design review and value engineering</li>
                    <li>Construction method analysis</li>
                    <li>Cost estimation and budget planning</li>
                    <li>Schedule optimization</li>
                    <li>Risk assessment and management strategies</li>
                    <li>Contract review and advice</li>
                    <li>Dispute resolution consultation</li>
                    <li>Sustainability and green building strategies</li>
                </ul>
                <p>Drawing on our extensive industry experience and technical expertise, we provide impartial advice tailored to the specific needs of each project. Our consulting services can be engaged at any stage of the project, from initial concept development to completion and handover.</p>',
                'category_id' => $projectMgmtCategory,
                'featured' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            
            // Renovation
            [
                'title' => 'Commercial Renovation',
                'short_description' => 'Renovation services for offices, retail spaces, and commercial properties.',
                'description' => '<p>Our commercial renovation services help businesses transform and upgrade their commercial spaces to improve functionality, aesthetics, and energy efficiency. Whether you\'re looking to modernize an outdated office, expand a retail space, or completely repurpose a commercial property, our team has the expertise to bring your vision to life.</p>
                <h3>Commercial Renovation Services:</h3>
                <ul>
                    <li>Office renovations and modernizations</li>
                    <li>Retail space remodeling</li>
                    <li>Restaurant and hospitality venue renovations</li>
                    <li>Healthcare facility upgrades</li>
                    <li>Warehouse and industrial space conversions</li>
                    <li>Façade improvements and exterior renovations</li>
                    <li>Accessibility upgrades and ADA compliance</li>
                    <li>Energy efficiency retrofits</li>
                </ul>
                <p>We understand that commercial renovations often need to be completed with minimal disruption to ongoing business operations. Our team works closely with clients to develop phased renovation plans, implement appropriate safety measures, and schedule work during off-hours when necessary.</p>',
                'category_id' => $renovationCategory,
                'featured' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Residential Renovation',
                'short_description' => 'Quality renovation services for homes, apartments, and residential properties.',
                'description' => '<p>Our residential renovation services help homeowners update, expand, or completely transform their living spaces to enhance comfort, functionality, and value. From simple room makeovers to comprehensive home renovations, we bring creativity, craftsmanship, and attention to detail to every project.</p>
                <h3>Residential Renovation Specialties:</h3>
                <ul>
                    <li>Kitchen remodeling</li>
                    <li>Bathroom renovations</li>
                    <li>Home additions and extensions</li>
                    <li>Basement finishing</li>
                    <li>Interior reconfiguration and space optimization</li>
                    <li>Exterior renovations and façade improvements</li>
                    <li>Historic home restoration</li>
                    <li>Energy-efficient upgrades</li>
                    <li>Smart home technology integration</li>
                </ul>
                <p>We work closely with homeowners throughout the renovation process, from initial design concepts to final finishing touches. Our team respects your home and family life, implementing strategies to minimize disruption and maintain clean, safe work areas. We also handle all necessary permits and inspections, ensuring that your renovation complies with local building codes and regulations.</p>',
                'category_id' => $renovationCategory,
                'featured' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
        ];

        foreach ($services as $service) {
            Service::create([
                'title' => $service['title'],
                'slug' => Str::slug($service['title']),
                'short_description' => $service['short_description'],
                'description' => $service['description'],
                'category_id' => $service['category_id'],
                'featured' => $service['featured'],
                'is_active' => $service['is_active'],
                'sort_order' => $service['sort_order'],
            ]);
        }
    }
}