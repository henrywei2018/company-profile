<?php

namespace Database\Seeders;

use App\Models\ChatTemplate;
use Illuminate\Database\Seeder;

class ChatTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // Greeting Templates
            [
                'name' => 'Default Greeting',
                'trigger' => null,
                'message' => "Hello! 👋 Welcome to CV Usaha Prima Lestari. How can I help you today?\n\nI can assist you with:\n• 🏗️ Construction Services\n• 💰 Request a Quote\n• 📞 Contact Information\n• 📋 Project Portfolio\n\nJust type your question!",
                'type' => 'greeting',
                'conditions' => null,
                'is_active' => true,
            ],
            
            // Auto-Response Templates
            [
                'name' => 'Quotation Request',
                'trigger' => 'quote',
                'message' => "I'd be happy to help you with a quotation! 💰\n\nFor the most accurate quote, I'll connect you with our sales team. You can also fill out our detailed quotation form: " . url('/quotation') . "\n\nWhat type of project are you planning?",
                'type' => 'auto_response',
                'conditions' => json_encode(['keywords' => ['quote', 'quotation', 'price', 'cost', 'estimate']]),
                'is_active' => true,
            ],
            
            [
                'name' => 'Services Information',
                'trigger' => 'service',
                'message' => "We offer comprehensive construction and infrastructure services! 🏗️\n\nOur main services include:\n• Building Construction\n• Road & Bridge Construction\n• Infrastructure Development\n• General Supplier Services\n• Project Management\n\nWhich service interests you most?",
                'type' => 'auto_response',
                'conditions' => json_encode(['keywords' => ['service', 'services', 'construction', 'what do you do']]),
                'is_active' => true,
            ],
            
            [
                'name' => 'Contact Information',
                'trigger' => 'contact',
                'message' => "Here's how you can reach us: 📞\n\n📱 Phone: " . settings('contact_phone', '+62 XXX XXXX XXXX') . "\n📧 Email: " . settings('contact_email', 'info@usahaprimaestari.com') . "\n📍 Address: " . settings('contact_address', 'Jakarta, Indonesia') . "\n🌐 Website: " . config('app.url') . "\n\nWe're available Monday-Friday, 8:00 AM - 5:00 PM WIB",
                'type' => 'auto_response',
                'conditions' => json_encode(['keywords' => ['contact', 'phone', 'email', 'address', 'location']]),
                'is_active' => true,
            ],
            
            [
                'name' => 'Portfolio Information',
                'trigger' => 'portfolio',
                'message' => "We're proud of our completed projects! 📋\n\nYou can view our portfolio of construction and infrastructure projects at: " . url('/projects') . "\n\nOur projects include office buildings, residential complexes, roads, and bridges across Indonesia.\n\nWould you like to know about any specific type of project?",
                'type' => 'auto_response',
                'conditions' => json_encode(['keywords' => ['portfolio', 'projects', 'work', 'examples', 'previous']]),
                'is_active' => true,
            ],
            
            // Offline Templates
            [
                'name' => 'Default Offline Message',
                'trigger' => null,
                'message' => "Thank you for contacting us! 🙏\n\nOur team is currently offline, but I'm here to help. I've received your message and our team will respond within 2 hours during business hours.\n\nFor urgent matters, you can:\n📞 Call us at " . settings('contact_phone', '+62 XXX XXXX XXXX') . "\n📧 Email us at " . settings('admin_email', 'admin@usahaprimaestari.com') . "\n\nBusiness Hours: Monday-Friday, 8:00 AM - 5:00 PM WIB",
                'type' => 'offline',
                'conditions' => null,
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            ChatTemplate::updateOrCreate(
                ['name' => $template['name']],
                $template
            );
        }

        $this->command->info('Chat templates seeded successfully!');
    }
}