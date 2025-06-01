<?php

namespace Database\Factories;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChatMessage>
 */
class ChatMessageFactory extends Factory
{
    protected $model = ChatMessage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $senderType = $this->faker->randomElement(['visitor', 'operator', 'bot', 'system']);
        
        return [
            'chat_session_id' => ChatSession::factory(),
            'sender_type' => $senderType,
            'sender_id' => $senderType === 'visitor' ? null : User::factory(),
            'message' => $this->getMessageBySenderType($senderType),
            'message_type' => $this->faker->randomElement(['text', 'system', 'template']),
            'metadata' => [],
            'is_read' => $this->faker->boolean(70), // 70% chance of being read
            'read_at' => $this->faker->boolean(70) ? $this->faker->dateTimeThisMonth() : null,
        ];
    }

    /**
     * Get message content based on sender type.
     */
    private function getMessageBySenderType(string $senderType): string
    {
        return match($senderType) {
            'visitor' => $this->faker->randomElement([
                'Hi, I need help with my project',
                'Can you tell me about your services?',
                'I have a question about pricing',
                'When will my project be completed?',
                'Thank you for your assistance',
                'Is there any update on my quotation?',
                'I would like to discuss my requirements',
                'Can we schedule a meeting?',
            ]),
            'operator' => $this->faker->randomElement([
                'Hello! How can I help you today?',
                'Sure, I can assist you with that',
                'Let me check your project status',
                'I will forward this to our team',
                'Thank you for contacting us',
                'Is there anything else I can help you with?',
                'Your project is progressing well',
                'I will get back to you shortly',
            ]),
            'bot' => $this->faker->randomElement([
                'Welcome to our chat support!',
                'I am connecting you with an operator',
                'Thank you for your message',
                'An operator will be with you shortly',
                'Please hold while we find an available agent',
            ]),
            'system' => $this->faker->randomElement([
                'Chat session started',
                'Operator joined the chat',
                'Chat session ended',
                'Message delivery failed',
                'Connection restored',
            ]),
        };
    }

    /**
     * Indicate that the message is from a visitor.
     */
    public function fromVisitor(): static
    {
        return $this->state(fn (array $attributes) => [
            'sender_type' => 'visitor',
            'sender_id' => null,
            'message' => $this->faker->randomElement([
                'Hi, I need help with my project',
                'Can you tell me about your services?',
                'I have a question about pricing',
                'When will my project be completed?',
            ]),
        ]);
    }

    /**
     * Indicate that the message is from an operator.
     */
    public function fromOperator(): static
    {
        return $this->state(fn (array $attributes) => [
            'sender_type' => 'operator',
            'sender_id' => User::factory(),
            'message' => $this->faker->randomElement([
                'Hello! How can I help you today?',
                'Sure, I can assist you with that',
                'Let me check your project status',
                'Thank you for contacting us',
            ]),
        ]);
    }

    /**
     * Indicate that the message is from a bot.
     */
    public function fromBot(): static
    {
        return $this->state(fn (array $attributes) => [
            'sender_type' => 'bot',
            'sender_id' => null,
            'message' => $this->faker->randomElement([
                'Welcome to our chat support!',
                'I am connecting you with an operator',
                'An operator will be with you shortly',
            ]),
        ]);
    }

    /**
     * Indicate that the message is a system message.
     */
    public function system(): static
    {
        return $this->state(fn (array $attributes) => [
            'sender_type' => 'system',
            'sender_id' => null,
            'message_type' => 'system',
            'message' => $this->faker->randomElement([
                'Chat session started',
                'Operator joined the chat',
                'Chat session ended',
            ]),
        ]);
    }

    /**
     * Indicate that the message is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Indicate that the message is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
            'read_at' => $this->faker->dateTimeThisMonth(),
        ]);
    }
}