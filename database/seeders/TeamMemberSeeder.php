<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TeamMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teamMembers = [
            [
                'name' => 'Budi Santoso',
                'position' => 'Director',
                'bio' => '<p>Budi Santoso is the founding Director of CV Usaha Prima Lestari with over 25 years of experience in the construction industry. He has led the company from its humble beginnings to become one of the leading construction and general supplier companies in Indonesia.</p>
                <p>With a strong background in civil engineering and business management, Budi provides strategic direction for the company and oversees major projects. His vision for excellence, innovation, and client satisfaction has been the driving force behind the company\'s success.</p>
                <p>Budi holds a Master\'s degree in Civil Engineering from Institut Teknologi Bandung and an MBA from the University of Indonesia. He is an active member of several industry associations and regularly speaks at construction industry conferences.</p>',
                'email' => 'budi.santoso@usahaprimalestari.com',
                'phone' => '+62 21 5678 9010',
                'linkedin' => 'https://www.linkedin.com/in/budisantoso/',
                'department' => 'Management',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Siti Rahayu',
                'position' => 'Operations Director',
                'bio' => '<p>Siti Rahayu serves as the Operations Director of CV Usaha Prima Lestari, bringing over 20 years of experience in construction project management and operations. She oversees all operational aspects of the company, ensuring efficient project execution and resource allocation.</p>
                <p>Siti is known for her meticulous attention to detail, strong problem-solving skills, and commitment to operational excellence. Under her leadership, the company has successfully delivered numerous complex projects on time and within budget.</p>
                <p>She holds a Bachelor\'s degree in Civil Engineering and a Master\'s degree in Construction Management. Siti is certified as a Professional Project Manager and is an advocate for women in construction, mentoring young female professionals in the industry.</p>',
                'email' => 'siti.rahayu@usahaprimalestari.com',
                'phone' => '+62 21 5678 9011',
                'linkedin' => 'https://www.linkedin.com/in/sitirahayu/',
                'department' => 'Management',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Ahmad Fauzi',
                'position' => 'Technical Director',
                'bio' => '<p>Ahmad Fauzi is the Technical Director of CV Usaha Prima Lestari, responsible for overseeing all technical aspects of the company\'s projects. With over 18 years of experience in structural engineering and construction technology, Ahmad ensures that all projects meet the highest technical standards and incorporate innovative solutions.</p>
                <p>His expertise spans various construction sectors including commercial, residential, industrial, and infrastructure projects. Ahmad is particularly passionate about integrating sustainable technologies into construction projects and has led several award-winning green building initiatives.</p>
                <p>Ahmad holds a Ph.D. in Structural Engineering and is a certified Professional Engineer. He regularly contributes to industry publications and has been recognized for his contributions to advancing construction technology in Indonesia.</p>',
                'email' => 'ahmad.fauzi@usahaprimalestari.com',
                'phone' => '+62 21 5678 9012',
                'linkedin' => 'https://www.linkedin.com/in/ahmadfauzi/',
                'department' => 'Management',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Hendra Wijaya',
                'position' => 'Project Director',
                'bio' => '<p>Ir. Hendra Wijaya recently joined CV Usaha Prima Lestari as Project Director, bringing over 20 years of experience in the construction industry. He oversees the company\'s portfolio of construction projects, with a focus on enhancing project delivery methodologies and quality management.</p>
                <p>Throughout his career, Hendra has successfully managed major commercial, residential, and infrastructure projects. His achievements include leading the construction of a 40-story mixed-use development in Jakarta and managing the delivery of a major infrastructure project completed 3 months ahead of schedule.</p>
                <p>Hendra holds a Master\'s degree in Civil Engineering from Institut Teknologi Bandung and is a certified Professional Engineer. He is committed to implementing advanced project management technologies and processes while mentoring the next generation of project managers.</p>',
                'email' => 'hendra.wijaya@usahaprimalestari.com',
                'phone' => '+62 21 5678 9013',
                'linkedin' => 'https://www.linkedin.com/in/hendrawijaya/',
                'department' => 'Project Management',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Dewi Susanti',
                'position' => 'Finance Manager',
                'bio' => '<p>Dewi Susanti serves as the Finance Manager of CV Usaha Prima Lestari, overseeing all financial operations including accounting, budgeting, financial reporting, and cash flow management. With 15 years of experience in financial management, Dewi ensures the company maintains strong financial health and compliance.</p>
                <p>She has implemented robust financial systems that have improved the company\'s financial efficiency and transparency. Dewi works closely with project teams to develop accurate project budgets and monitor financial performance throughout the project lifecycle.</p>
                <p>Dewi holds a Master\'s degree in Accounting and is a Certified Public Accountant. She is known for her analytical thinking, attention to detail, and ability to translate complex financial information into actionable insights for decision-making.</p>',
                'email' => 'dewi.susanti@usahaprimalestari.com',
                'phone' => '+62 21 5678 9014',
                'linkedin' => 'https://www.linkedin.com/in/dewisusanti/',
                'department' => 'Finance & Administration',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 5,
            ],
            [
                'name' => 'Rudi Hartono',
                'position' => 'Procurement Manager',
                'bio' => '<p>Rudi Hartono is the Procurement Manager at CV Usaha Prima Lestari, responsible for managing the company\'s procurement processes, supplier relationships, and material logistics. With 12 years of experience in procurement and supply chain management, Rudi ensures that all projects have access to quality materials at competitive prices.</p>
                <p>He has developed an efficient procurement system that has reduced material costs by 15% while maintaining quality standards. Rudi works closely with project teams to plan material requirements and ensure timely delivery to construction sites.</p>
                <p>Rudi holds a Bachelor\'s degree in Supply Chain Management and is certified in Procurement Management. He is particularly skilled in negotiation, supplier relationship management, and inventory optimization.</p>',
                'email' => 'rudi.hartono@usahaprimalestari.com',
                'phone' => '+62 21 5678 9015',
                'linkedin' => 'https://www.linkedin.com/in/rudihartono/',
                'department' => 'Procurement',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 6,
            ],
            [
                'name' => 'Anita Wijaya',
                'position' => 'HR & Administration Manager',
                'bio' => '<p>Anita Wijaya serves as the HR & Administration Manager at CV Usaha Prima Lestari, overseeing all aspects of human resources and administrative functions. With 10 years of experience in HR management, Anita is responsible for recruitment, training, performance management, and organizational development.</p>
                <p>She has implemented effective HR strategies that have improved employee engagement, reduced turnover, and enhanced the company\'s ability to attract top talent. Anita is passionate about creating a positive work environment and fostering professional growth opportunities for all employees.</p>
                <p>Anita holds a Master\'s degree in Human Resource Management and is certified as a Professional HR Manager. She is known for her strong interpersonal skills, empathy, and ability to align HR practices with business objectives.</p>',
                'email' => 'anita.wijaya@usahaprimalestari.com',
                'phone' => '+62 21 5678 9016',
                'linkedin' => 'https://www.linkedin.com/in/anitawijaya/',
                'department' => 'Finance & Administration',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 7,
            ],
            [
                'name' => 'Bambang Suryanto',
                'position' => 'Senior Project Manager',
                'bio' => '<p>Bambang Suryanto is a Senior Project Manager at CV Usaha Prima Lestari with 15 years of experience in construction project management. He has successfully led numerous complex projects including commercial buildings, residential developments, and industrial facilities.</p>
                <p>Known for his strong leadership and technical expertise, Bambang consistently delivers projects on time and within budget while maintaining the highest quality standards. He excels at team coordination, risk management, and client communication.</p>
                <p>Bambang holds a Bachelor\'s degree in Civil Engineering and is certified as a Professional Project Manager. He is passionate about adopting innovative project management methodologies and mentoring junior project managers.</p>',
                'email' => 'bambang.suryanto@usahaprimalestari.com',
                'phone' => '+62 21 5678 9017',
                'linkedin' => 'https://www.linkedin.com/in/bambangsuryanto/',
                'department' => 'Project Management',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 8,
            ],
            [
                'name' => 'Kartika Sari',
                'position' => 'Quality Control Manager',
                'bio' => '<p>Kartika Sari serves as the Quality Control Manager at CV Usaha Prima Lestari, responsible for ensuring that all projects meet the company\'s high quality standards and client specifications. With 12 years of experience in quality management, Kartika has developed robust quality control systems that have significantly improved construction outcomes.</p>
                <p>She leads a team of quality inspectors who conduct regular site inspections and material testing. Kartika works closely with project teams to identify and address quality issues early, preventing costly rework and ensuring client satisfaction.</p>
                <p>Kartika holds a Bachelor\'s degree in Civil Engineering and is certified in Quality Management Systems. She is known for her meticulous attention to detail, problem-solving abilities, and commitment to continuous improvement.</p>',
                'email' => 'kartika.sari@usahaprimalestari.com',
                'phone' => '+62 21 5678 9018',
                'linkedin' => 'https://www.linkedin.com/in/kartikasari/',
                'department' => 'Technical',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 9,
            ],
            [
                'name' => 'Agus Pranoto',
                'position' => 'Safety Manager',
                'bio' => '<p>Agus Pranoto is the Safety Manager at CV Usaha Prima Lestari, responsible for developing and implementing safety programs across all construction sites. With 14 years of experience in occupational health and safety, Agus ensures that all projects maintain the highest safety standards and comply with regulations.</p>
                <p>Under his leadership, the company has achieved an outstanding safety record with zero major incidents over the past five years. Agus conducts regular safety training sessions, site inspections, and risk assessments to promote a strong safety culture.</p>
                <p>Agus holds a Bachelor\'s degree in Occupational Health and Safety and multiple safety certifications. He is passionate about creating a safe working environment and believes that safety and productivity go hand in hand.</p>',
                'email' => 'agus.pranoto@usahaprimalestari.com',
                'phone' => '+62 21 5678 9019',
                'linkedin' => 'https://www.linkedin.com/in/aguspranoto/',
                'department' => 'QHSE',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 10,
            ],
        ];

        foreach ($teamMembers as $member) {
            TeamMember::create([
                'name' => $member['name'],
                'slug' => Str::slug($member['name']),
                'position' => $member['position'],
                'bio' => $member['bio'],
                'email' => $member['email'],
                'phone' => $member['phone'],
                'linkedin' => $member['linkedin'],
                'department' => $member['department'],
                'is_active' => $member['is_active'],
                'is_featured' => $member['is_featured'],
                'sort_order' => $member['sort_order'],
            ]);
        }
    }
}