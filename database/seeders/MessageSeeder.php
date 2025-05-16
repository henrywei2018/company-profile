<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some client users
        $clients = User::role('client')->pluck('id')->toArray();
        
        $messages = [
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@example.com',
                'phone' => '+62 812 3456 7890',
                'company' => 'PT Sejahtera Mandiri',
                'subject' => 'Request for construction consultation',
                'message' => 'Hello, I am interested in getting a consultation for an office building construction project in Jakarta. We are planning to start the project in the next 6 months and would like to discuss the possibilities with your team. Please let me know when we can schedule a meeting.',
                'type' => 'contact_form',
                'is_read' => false,
            ],
            [
                'name' => 'Dewi Susanti',
                'email' => 'dewi.susanti@example.com',
                'phone' => '+62 813 9876 5432',
                'company' => 'PT Harapan Jaya',
                'subject' => 'Materials supply inquiry',
                'message' => 'We are currently working on a residential project and looking for a reliable supplier of construction materials. I would like to know more about your product range, pricing, and delivery terms. Our project is located in Bandung and we need various materials including cement, steel, and bricks.',
                'type' => 'contact_form',
                'is_read' => true,
                'read_at' => now()->subDays(2),
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'phone' => '+62 811 222 3333',
                'company' => 'CV Makmur Abadi',
                'subject' => 'Building maintenance service',
                'message' => 'We have a commercial building that requires regular maintenance services. I would like to inquire about your maintenance packages, the scope of services included, and the pricing structure. The building is a 10-story office building located in central Jakarta with approximately 10,000 square meters of space.',
                'type' => 'contact_form',
                'is_read' => true,
                'read_at' => now()->subDays(5),
            ],
            [
                'name' => 'Siti Rahma',
                'email' => 'siti.rahma@example.com',
                'phone' => '+62 822 1111 2222',
                'company' => 'Personal',
                'subject' => 'Home renovation project',
                'message' => 'I am looking to renovate my two-story house in Surabaya. The renovation will include kitchen remodeling, bathroom upgrades, and adding an extension to the house. Could you please provide information on your residential renovation services and possibly arrange for an on-site assessment?',
                'type' => 'contact_form',
                'is_read' => false,
            ],
            [
                'name' => 'Agus Wijaya',
                'email' => 'agus.wijaya@example.com',
                'phone' => '+62 855 6666 7777',
                'company' => 'PT Karya Unggul',
                'subject' => 'Industrial facility construction',
                'message' => 'Our company is planning to build a new manufacturing facility in an industrial area near Jakarta. We are looking for a construction company with experience in industrial projects. I would like to discuss our requirements with your team and learn more about your approach to such projects.',
                'type' => 'contact_form',
                'is_read' => false,
            ],
        ];

        // Create messages
        foreach ($messages as $index => $messageData) {
            // Randomly associate some messages with clients
            $user_id = null;
            if (rand(0, 1) && !empty($clients)) {
                $user_id = $clients[array_rand($clients)];
            }
            
            Message::create([
                'name' => $messageData['name'],
                'email' => $messageData['email'],
                'phone' => $messageData['phone'],
                'company' => $messageData['company'],
                'subject' => $messageData['subject'],
                'message' => $messageData['message'],
                'type' => $messageData['type'],
                'is_read' => $messageData['is_read'],
                'user_id' => $user_id,
                'read_at' => $messageData['is_read'] ? ($messageData['read_at'] ?? now()->subDays(rand(1, 7))) : null,
            ]);
        }
        
        // Create client support messages
        foreach ($clients as $clientId) {
            Message::create([
                'name' => 'Client Support',
                'email' => 'support@usahaprimalestari.com',
                'subject' => 'Project Update',
                'message' => 'Dear valued client, this is an update on your current project. We are making good progress and are on schedule. Please let us know if you have any questions or concerns.',
                'type' => 'client_message',
                'is_read' => rand(0, 1),
                'user_id' => $clientId,
                'read_at' => rand(0, 1) ? now()->subDays(rand(1, 5)) : null,
            ]);
        }
    }
}