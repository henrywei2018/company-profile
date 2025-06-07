<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\FilterableTrait;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    use HasFactory, FilterableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'short_description',
        'client_id',
        'quotation_id',
        'project_category_id',
        'service_id',
        'status',
        'priority',
        'year',
        'start_date',
        'end_date',
        'estimated_completion_date',
        'actual_completion_date',
        'budget',
        'actual_cost',
        'progress_percentage',
        'featured',
        'is_active',
        'location',
        'challenge',
        'solution',
        'results',
        'technologies_used',
        'team_members',
        'client_feedback',
        'lessons_learned',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'services_used',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'estimated_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'featured' => 'boolean',
        'is_active' => 'boolean',
        'budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'progress_percentage' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The filterable attributes for the model.
     *
     * @var array
     */
    protected $filterable = [
        'status',
        'priority',
        'category_id',
        'client_id',
        'featured',
        'search',
    ];

    /**
     * The searchable attributes for the model.
     *
     * @var array
     */
    protected $searchable = [
        'title',
        'description',
        'short_description',
        'location',
        'challenge',
        'solution',
        'results',
    ];

    /**
     * Status constants
     */
    const STATUS_PLANNING = 'planning';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_ON_HOLD = 'on_hold';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Priority constants
     */
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    public function getServicesUsedAttribute($value)
{
    if (empty($value)) {
        return [];
    }
    
    if (is_string($value)) {
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        // If not valid JSON, return as single item array
        return [$value];
    }
    
    return is_array($value) ? $value : [];
}

/**
 * Get technologies used as array for views
 */
public function getTechnologiesUsedAttribute($value)
{
    if (empty($value)) {
        return [];
    }
    
    if (is_string($value)) {
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        return [$value];
    }
    
    return is_array($value) ? $value : [];
}

/**
 * Get team members as array for views
 */
public function getTeamMembersAttribute($value)
{
    if (empty($value)) {
        return [];
    }
    
    if (is_string($value)) {
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        return [$value];
    }
    
    return is_array($value) ? $value : [];
}
    /**
     * Get all available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PLANNING => 'Planning',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_ON_HOLD => 'On Hold',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }
    
    /**
     * Get all available priorities
     */
    public static function getPriorities(): array
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_NORMAL => 'Normal',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }

            if (empty($project->status)) {
                $project->status = self::STATUS_PLANNING;
            }

            if (empty($project->priority)) {
                $project->priority = self::PRIORITY_NORMAL;
            }

            // Ensure array fields are properly set
            $project->technologies_used = $project->technologies_used ?? [];
            $project->team_members = $project->team_members ?? [];
            $project->services_used = $project->services_used ?? [];
        });

        static::updating(function ($project) {
            if ($project->isDirty('title') && empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }

            // Ensure array fields are properly handled
            if ($project->isDirty('technologies_used') && !is_array($project->technologies_used)) {
                $project->technologies_used = is_string($project->technologies_used) 
                    ? json_decode($project->technologies_used, true) ?? []
                    : [];
            }

            if ($project->isDirty('team_members') && !is_array($project->team_members)) {
                $project->team_members = is_string($project->team_members) 
                    ? json_decode($project->team_members, true) ?? []
                    : [];
            }

            if ($project->isDirty('services_used') && !is_array($project->services_used)) {
                $project->services_used = is_string($project->services_used) 
                    ? json_decode($project->services_used, true) ?? []
                    : [];
            }
        });
    }

    /**
     * Set technologies used attribute - ensure it's always an array
     */
    public function setTechnologiesUsedAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['technologies_used'] = json_encode(
                array_filter(explode(',', $value), function($item) {
                    return !empty(trim($item));
                })
            );
        } elseif (is_array($value)) {
            $this->attributes['technologies_used'] = json_encode(array_filter($value));
        } else {
            $this->attributes['technologies_used'] = json_encode([]);
        }
    }

    /**
     * Set team members attribute - ensure it's always an array
     */
    public function setTeamMembersAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['team_members'] = json_encode(
                array_filter(explode(',', $value), function($item) {
                    return !empty(trim($item));
                })
            );
        } elseif (is_array($value)) {
            $this->attributes['team_members'] = json_encode(array_filter($value));
        } else {
            $this->attributes['team_members'] = json_encode([]);
        }
    }

    /**
     * Set services used attribute - ensure it's always an array
     */
    public function setServicesUsedAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['services_used'] = json_encode(
                array_filter(explode(',', $value), function($item) {
                    return !empty(trim($item));
                })
            );
        } elseif (is_array($value)) {
            $this->attributes['services_used'] = json_encode(array_filter($value));
        } else {
            $this->attributes['services_used'] = json_encode([]);
        }
    }

    /**
     * Get the messages for this project.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get unread messages for this project.
     */
    public function unreadMessages()
    {
        return $this->hasMany(Message::class)->where('is_read', false);
    }

    /**
     * Get urgent messages for this project.
     */
    public function urgentMessages()
    {
        return $this->hasMany(Message::class)->where('priority', 'urgent');
    }

    /**
     * Get the latest message for this project.
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the client that owns the project.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the quotation that originated this project.
     */
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    /**
     * Get the project category.
     */
    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    /**
     * Get the related service.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the project images.
     */
    public function images()
    {
        return $this->hasMany(ProjectImage::class);
    }

    /**
     * Get the project files.
     */
    public function files()
    {
        return $this->hasMany(ProjectFile::class);
    }

    /**
     * Alias for files() - for backward compatibility
     * This fixes the "attachments" relationship error
     */
    public function attachments()
    {
        return $this->files();
    }

    /**
     * Get the project milestones.
     */
    public function milestones()
    {
        return $this->hasMany(ProjectMilestone::class);
    }

    public function testimonials()
    {
        return $this->hasMany(\App\Models\Testimonial::class);
    }

    /**
     * Get active testimonials for this project.
     */
    public function activeTestimonials()
    {
        return $this->hasMany(\App\Models\Testimonial::class)->where('is_active', true);
    }

    /**
     * Get featured testimonials for this project.
     */
    public function featuredTestimonials()
    {
        return $this->hasMany(\App\Models\Testimonial::class)
            ->where('featured', true)
            ->where('is_active', true);
    }

    /**
     * Get project updates/logs (if you have this feature)
     */
    public function updates()
    {
        return $this->hasMany(ProjectUpdate::class)->latest();
    }

    /**
     * Scope queries to only include active projects.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope queries to only include featured projects.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope queries to only include completed projects.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope queries to only include in-progress projects.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope queries to filter by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('project_category_id', $categoryId);
    }

    /**
     * Scope queries to filter by service.
     */
    public function scopeByService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    /**
     * Get the featured image URL.
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        $image = $this->images()->where('is_featured', true)->first() ?:
            $this->images()->orderBy('sort_order')->first();

        if ($image && $image->image_path && Storage::disk('public')->exists($image->image_path)) {
            return Storage::url($image->image_path);
        }

        return null;
    }

    /**
     * Get the status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PLANNING => 'blue',
            self::STATUS_IN_PROGRESS => 'yellow',
            self::STATUS_ON_HOLD => 'orange',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_CANCELLED => 'red',
            default => 'gray'
        };
    }

    /**
     * Get the priority badge color.
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority ?? 'normal') {
            self::PRIORITY_LOW => 'gray',
            self::PRIORITY_NORMAL => 'blue',
            self::PRIORITY_HIGH => 'orange',
            self::PRIORITY_URGENT => 'red',
            default => 'gray'
        };
    }

    /**
     * Get formatted status.
     */
    public function getFormattedStatusAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Get formatted priority.
     */
    public function getFormattedPriorityAttribute(): string
    {
        return self::getPriorities()[$this->priority ?? 'normal'] ?? ucfirst($this->priority ?? 'normal');
    }

    /**
     * Check if the project is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->end_date &&
            $this->end_date->isPast() &&
            $this->status !== self::STATUS_COMPLETED;
    }

    /**
     * Get the project's main image.
     */
    public function getMainImageAttribute()
    {
        return $this->images()->where('is_featured', true)->first() ?:
            $this->images()->orderBy('sort_order')->first();
    }

    /**
     * Get the project's URL.
     */
    public function getUrlAttribute(): string
    {
        return route('portfolio.show', $this->slug);
    }

    /**
     * Get the admin URL for this project.
     */
    public function getAdminUrlAttribute(): string
    {
        return route('admin.projects.show', $this);
    }

    /**
     * Get the completion percentage as a formatted string.
     */
    public function getCompletionPercentageAttribute(): string
    {
        return ($this->progress_percentage ?? 0) . '%';
    }

    /**
     * Check if project was created from a quotation.
     */
    public function hasQuotation(): bool
    {
        return !is_null($this->quotation_id);
    }

    /**
     * Get the project duration in days.
     */
    public function getDurationInDaysAttribute(): ?int
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }

        return $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Get the project's budget utilization percentage.
     */
    public function getBudgetUtilizationAttribute(): ?float
    {
        if (!$this->budget || $this->budget <= 0) {
            return null;
        }

        return ($this->actual_cost / $this->budget) * 100;
    }

    /**
     * Get technologies used as formatted string - SAFE GETTER
     */
    public function getTechnologiesUsedFormattedAttribute(): string
    {
        $technologies = $this->technologies_used;
        
        if (!$technologies || !is_array($technologies)) {
            return 'None specified';
        }
        
        return implode(', ', array_filter($technologies));
    }

    /**
     * Get team members as formatted string - SAFE GETTER
     */
    public function getTeamMembersFormattedAttribute(): string
    {
        $members = $this->team_members;
        
        if (!$members || !is_array($members)) {
            return 'Not assigned';
        }
        
        return implode(', ', array_filter($members));
    }

    /**
     * Get services used as formatted string - SAFE GETTER
     */
    public function getServicesUsedFormattedAttribute(): string
    {
        $services = $this->services_used;
        
        if (!$services || !is_array($services)) {
            return 'None specified';
        }
        
        return implode(', ', array_filter($services));
    }
}