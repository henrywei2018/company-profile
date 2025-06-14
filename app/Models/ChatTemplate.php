<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatTemplate extends Model
{
    protected $fillable = [
        'name',
        'trigger',
        'message',
        'type',
        'conditions',
        'is_active',
        'usage_count',
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper methods
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function matchesTrigger(string $message): bool
    {
        if (!$this->trigger) {
            return false;
        }

        return str_contains(strtolower($message), strtolower($this->trigger));
    }
    public static function getTemplatesByCategory(): array
    {
        $templates = self::where('is_active', true)
            ->orderBy('type')
            ->orderBy('usage_count', 'desc')
            ->get();

        return [
            'greeting' => $templates->where('type', 'greeting'),
            'quick_reply' => $templates->where('type', 'quick_reply'),
            'auto_response' => $templates->where('type', 'auto_response'),
            'offline' => $templates->where('type', 'offline'),
        ];
    }

    /**
     * Get popular templates (most used)
     */
    public static function getPopularTemplates(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('is_active', true)
            ->where('type', 'quick_reply')
            ->orderBy('usage_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Search templates by trigger or content
     */
    public static function searchByTrigger(string $trigger): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('is_active', true)
            ->where('trigger', 'like', "%{$trigger}%")
            ->orderBy('usage_count', 'desc')
            ->get();
    }

    /**
     * Get template suggestions based on context
     */
    public static function getSuggestions(string $context = ''): \Illuminate\Database\Eloquent\Collection
    {
        $keywords = explode(' ', strtolower($context));
        
        return self::where('is_active', true)
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    if (strlen($keyword) > 2) {
                        $query->orWhere('trigger', 'like', "%{$keyword}%")
                              ->orWhere('name', 'like', "%{$keyword}%");
                    }
                }
            })
            ->orderBy('usage_count', 'desc')
            ->limit(5)
            ->get();
    }
}