<?php

namespace Database\Seeders;

use App\Models\ChatTemplate;
use Illuminate\Database\Seeder;

class ChatTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            // Greeting templates
            [
                'name' => 'Welcome Greeting',
                'trigger' => null,
                'message' => 'Hello! Welcome to CV Usaha Prima Lestari. How can I assist you today?',
                'type' => 'greeting',
                'conditions' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Business Hours Greeting',
                'trigger' => null,
                'message' => 'Hi there! Thank you for contacting CV Usaha Prima Lestari. Our team is here to help you with your construction and supply needs.',
                'type' => 'greeting',
                'conditions' => ['business_hours' => true],
                'is_active' => true,
            ],
            
            // Auto response templates
            [
                'name' => 'Quick Response - Services',
                'trigger' => 'services',
                'message' => 'We offer a wide range of construction and supply services including building materials, project management, and consultation. Would you like to know more about any specific service?',
                'type' => 'auto_response',
                'conditions' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Quick Response - Pricing',
                'trigger' => 'price',
                'message' => 'Our pricing depends on various factors including project scope, materials, and timeline. Would you like to request a detailed quotation? I can help you get started with that.',
                'type' => 'auto_response',
                'conditions' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Quick Response - Contact',
                'trigger' => 'contact',
                'message' => 'You can reach us at:\n📞 Phone: +62 XXX-XXXX-XXXX\n📧 Email: info@cvusahaprimaestari.com\n📍 Address: [Your Address]\n\nOr continue chatting here for immediate assistance!',
                'type' => 'auto_response',
                'conditions' => null,
                'is_active' => true,
            ],
            
            // Quick reply templates
            [
                'name' => 'Thank You',
                'trigger' => null,
                'message' => 'Thank you for your inquiry! I\'ll be happy to help you with that.',
                'type' => 'quick_reply',
                'conditions' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Please Wait',
                'trigger' => null,
                'message' => 'Please give me a moment to check that information for you.',
                'type' => 'quick_reply',
                'conditions' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Transfer to Team',
                'trigger' => null,
                'message' => 'Let me connect you with one of our specialists who can better assist you with this matter.',
                'type' => 'quick_reply',
                'conditions' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Project Status',
                'trigger' => null,
                'message' => 'I can help you check your project status. Could you please provide your project ID or reference number?',
                'type' => 'quick_reply',
                'conditions' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Quotation Request',
                'trigger' => null,
                'message' => 'I\'d be happy to help you with a quotation request. To provide you with an accurate quote, I\'ll need some details about your project. Would you like me to guide you through the process?',
                'type' => 'quick_reply',
                'conditions' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Technical Support',
                'trigger' => null,
                'message' => 'For technical support, I\'ll connect you with our technical team. They\'ll be able to provide detailed assistance with your technical questions.',
                'type' => 'quick_reply',
                'conditions' => null,
                'is_active' => true,
            ],
            
            // Offline templates
            [
                'name' => 'After Hours Message',
                'trigger' => null,
                'message' => 'Thank you for contacting CV Usaha Prima Lestari. Our office hours are Monday-Friday 8:00 AM - 5:00 PM. Your message is important to us, and we\'ll respond first thing in the morning.',
                'type' => 'offline',
                'conditions' => ['outside_business_hours' => true],
                'is_active' => true,
            ],
            [
                'name' => 'Weekend Message',
                'trigger' => null,
                'message' => 'Hi! Thanks for reaching out during the weekend. While our office is closed, your message has been received and our team will get back to you on Monday morning.',
                'type' => 'offline',
                'conditions' => ['weekend' => true],
                'is_active' => true,
            ],
            [
                'name' => 'Holiday Message',
                'trigger' => null,
                'message' => 'Thank you for contacting us! Please note that we\'re currently closed for the holiday. We\'ll respond to your message when we return to the office.',
                'type' => 'offline',
                'conditions' => ['holiday' => true],
                'is_active' => false, // Enable when needed
            ],
            
            // Industry-specific templates
            [
                'name' => 'Construction Inquiry',
                'trigger' => 'construction',
                'message' => 'Great! We specialize in construction projects of all sizes. Whether you need residential, commercial, or industrial construction services, we can help. What type of construction project are you planning?',
                'type' => 'auto_response',
                'conditions' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Material Supply',
                'trigger' => 'materials',
                'message' => 'We supply a wide range of construction materials including cement, steel, aggregates, and finishing materials. Are you looking for materials for a specific project? I can help you get the right materials at competitive prices.',
                'type' => 'auto_response',
                'conditions' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Project Timeline',
                'trigger' => 'timeline',
                'message' => 'Project timelines depend on several factors including scope, complexity, and weather conditions. Once we understand your requirements, we can provide a detailed project schedule. Would you like to discuss your project requirements?',
                'type' => 'auto_response',
                'conditions' => null,
                'is_active' => true,
            ],
            
            // Follow-up templates
            [
                'name' => 'Follow Up',
                'trigger' => null,
                'message' => 'Is there anything else I can help you with today? We\'re here to make your construction project as smooth as possible.',
                'type' => 'quick_reply',
                'conditions' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Session Closing',
                'trigger' => null,
                'message' => 'Thank you for choosing CV Usaha Prima Lestari! If you have any more questions, feel free to start a new chat or contact us directly. Have a great day!',
                'type' => 'quick_reply',
                'conditions' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Feedback Request',
                'trigger' => null,
                'message' => 'We hope we were able to help you today! Your feedback is valuable to us. If you have a moment, we\'d appreciate any comments about your experience.',
                'type' => 'quick_reply',
                'conditions' => null,
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            ChatTemplate::create($template);
        }

        $this->command->info('✅ Chat templates created successfully!');
    }
}