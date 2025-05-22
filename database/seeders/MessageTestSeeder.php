<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Message;
use Carbon\Carbon;

class MessageTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate([
            'email' => 'admin@usahaprimalestari.com'
        ], [
            'name' => 'Admin',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create client users
        $client1 = User::firstOrCreate([
            'email' => 'client1@example.com'
        ], [
            'name' => 'Client One',
            'password' => bcrypt('password'),
            'role' => 'client',
            'email_verified_at' => now(),
        ]);

        $client2 = User::firstOrCreate([
            'email' => 'client2@example.com'
        ], [
            'name' => 'Client Two',
            'password' => bcrypt('password'),
            'role' => 'client',
            'email_verified_at' => now(),
        ]);

        // Create different combinations of messages to test filters
        $statuses = [
            ['is_read' => false, 'is_replied' => false],
            ['is_read' => true, 'is_replied' => false],
            ['is_read' => false, 'is_replied' => true],
            ['is_read' => true, 'is_replied' => true],
        ];

        $types = ['contact_form', 'client_to_admin'];

        foreach ($types as $type) {
            foreach ($statuses as $i => $status) {
                Message::create([
                    'user_id' => $type === 'client_to_admin' ? $client1->id : null,
                    'name' => $type === 'client_to_admin' ? $client1->name : 'Visitor ' . $i,
                    'email' => $type === 'client_to_admin' ? $client1->email : "visitor{$i}@example.com",
                    'phone' => '+62 812 0000 000' . $i,
                    'company' => 'Company ' . $i,
                    'subject' => strtoupper($type) . ' Message #' . ($i + 1),
                    'message' => 'This is a test message to check ' . $type . ' logic.',
                    'type' => $type,
                    'is_read' => $status['is_read'],
                    'is_replied' => $status['is_replied'],
                    'created_at' => Carbon::now()->subDays($i + 1),
                    'read_at' => $status['is_read'] ? Carbon::now()->subDays($i + 1) : null,
                    'replied_at' => $status['is_replied'] ? Carbon::now()->subDays($i) : null,
                    'replied_by' => $status['is_replied'] ? $admin->id : null,
                ]);
            }
        }

        // Admin replies to simulate a conversation thread
        $parent = Message::create([
            'user_id' => $client2->id,
            'name' => $client2->name,
            'email' => $client2->email,
            'phone' => '+62 821 9999 9999',
            'company' => 'Client Co',
            'subject' => 'Follow-up Discussion',
            'message' => 'Please follow up this request.',
            'type' => 'client_to_admin',
            'is_read' => true,
            'is_replied' => true,
            'created_at' => now()->subDay(),
            'read_at' => now()->subDay(),
            'replied_at' => now()->subHours(20),
            'replied_by' => $admin->id,
        ]);

        Message::create([
            'type' => 'admin_to_client',
            'name' => $admin->name,
            'email' => $admin->email,
            'subject' => 'RE: Follow-up Discussion',
            'message' => 'Thanks for your message. We will take action immediately.',
            'user_id' => $client2->id,
            'is_read' => true,
            'is_replied' => false,
            'parent_id' => $parent->id,
            'read_at' => now(),
            'replied_by' => $admin->id,
            'created_at' => now()->subHours(12),
        ]);
    }
}
