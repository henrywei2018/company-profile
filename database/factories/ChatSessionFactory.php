<?php

namespace Database\Factories;

use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChatSession>
 */
class ChatSessionFactory extends Factory
{
    protected $model = ChatSession::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = $this->faker->dateTimeBetween('-7 days', 'now');
        $status = $this->faker->randomElement(['active', 'waiting', 'closed']);
        
        return [
            'session_id' => Str::uuid(),
            'user_id' => User::factory(),
            'visitor_info' => null, // Will be filled if no user_id
            'status' => $status,
            'assigned_operator_id' => $status === 'active' ? User::factory() : null,
            'priority' => $this->faker->randomElement(['low', 'normal', 'high', 'urgent']),
            'source' => $this->faker->randomElement(['website', 'mobile', 'api']),
            'started_at' => $startedAt,
            'last_activity_at' => $this->faker->dateTimeBetween($startedAt, 'now'),
            'ended_at' => $status === 'closed' ? $this->faker->dateTimeBetween($startedAt, 'now') : null,
            'summary' => $status === 'closed' ? $this->faker->sentence() : null,
            'metadata' => [
                'browser' => $this->faker->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge']),
                'os' => $this->faker->randomElement(['Windows', 'MacOS', 'Linux', 'iOS', 'Android']),
                'referrer' => $this->faker->url(),
            ],
        ];
    }

    /**
     * Indicate that the session is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'assigned_operator_id' => User::factory(),
            'ended_at' => null,
            'summary' => null,
        ]);
    }

    /**
     * Indicate that the session is waiting.
     */
    public function waiting(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'waiting',
            'assigned_operator_id' => null,
            'ended_at' => null,
            'summary' => null,
        ]);
    }

    /**
     * Indicate that the session is closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
            'ended_at' => $this->faker->dateTimeBetween($attributes['started_at'], 'now'),
            'summary' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate that the session is for a guest (no user_id).
     */
    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'visitor_info' => [
                'name' => $this->faker->name(),
                'email' => $this->faker->email(),
                'phone' => $this->faker->phoneNumber(),
            ],
        ]);
    }

    /**
     * Indicate that the session has high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }

    /**
     * Indicate that the session is urgent.
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgent',
        ]);
    }

    /**
     * Create session with messages.
     */
    public function withMessages(int $count = 5): static
    {
        return $this->afterCreating(function (ChatSession $session) use ($count) {
            \App\Models\ChatMessage::factory($count)->create([
                'chat_session_id' => $session->id,
            ]);
        });
    }
}