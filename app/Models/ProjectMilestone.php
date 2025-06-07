<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasSortOrderTrait;
use Carbon\Carbon;

class ProjectMilestone extends Model
{
    use HasFactory, SoftDeletes, HasSortOrderTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'due_date',
        'completed_date',
        'status',
        'progress_percent',
        'estimated_hours',
        'actual_hours',
        'priority',
        'dependencies',
        'notes',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'due_date' => 'date',
        'completed_date' => 'date', 
        'progress_percent' => 'integer',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'dependencies' => 'array',
        'sort_order' => 'integer',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DELAYED = 'delayed';

    /**
     * Priority constants
     */
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_CRITICAL = 'critical';
    public function getDependenciesAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($value) ? $value : [];
    }
    /**
     * Get all available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_DELAYED => 'Delayed',
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
            self::PRIORITY_CRITICAL => 'Critical',
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($milestone) {
            if (empty($milestone->status)) {
                $milestone->status = self::STATUS_PENDING;
            }

            if (empty($milestone->priority)) {
                $milestone->priority = self::PRIORITY_NORMAL;
            }

            if (is_null($milestone->progress_percent)) {
                $milestone->progress_percent = 0;
            }

            // Auto-set sort order
            if (is_null($milestone->sort_order)) {
                $milestone->sort_order = static::where('project_id', $milestone->project_id)
                    ->max('sort_order') + 1;
            }
        });

        static::updating(function ($milestone) {
            // Auto-set completion date when status changes to completed
            if ($milestone->isDirty('status') && 
                $milestone->status === self::STATUS_COMPLETED && 
                !$milestone->completed_date) {
                $milestone->completed_date = now();
                $milestone->progress_percent = 100;
            }

            // Clear completion date when status changes from completed
            if ($milestone->isDirty('status') && 
                $milestone->getOriginal('status') === self::STATUS_COMPLETED &&
                $milestone->status !== self::STATUS_COMPLETED &&
                !$milestone->isDirty('completed_date')) {
                $milestone->completed_date = null;
            }
        });
    }

    /**
     * Get the project that owns the milestone.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get dependent milestones (milestones that depend on this one).
     */
    public function dependentMilestones()
    {
        return ProjectMilestone::where('project_id', $this->project_id)
            ->whereJsonContains('dependencies', $this->id)
            ->get();
    }

    /**
     * Get dependency milestones (milestones this one depends on).
     */
    public function dependencyMilestones()
    {
        if (!$this->dependencies) {
            return collect();
        }

        return ProjectMilestone::whereIn('id', $this->dependencies)->get();
    }

    /**
     * Scope queries to only include completed milestones.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope queries to only include pending milestones.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope queries to only include in-progress milestones.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope queries to only include overdue milestones.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', self::STATUS_COMPLETED)
            ->where('due_date', '<', now());
    }

    /**
     * Scope queries to only include milestones due soon.
     */
    public function scopeDueSoon($query, int $days = 7)
    {
        return $query->where('status', '!=', self::STATUS_COMPLETED)
            ->whereBetween('due_date', [now(), now()->addDays($days)]);
    }

    /**
     * Scope queries by priority.
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope queries to only include high priority milestones.
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', [self::PRIORITY_HIGH, self::PRIORITY_CRITICAL]);
    }

    /**
     * Check if the milestone is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if the milestone is overdue.
     */
    public function isOverdue(): bool
    {
        return !$this->isCompleted() && 
               $this->due_date && 
               $this->due_date->isPast();
    }

    /**
     * Check if the milestone is due soon.
     */
    public function isDueSoon(int $days = 7): bool
    {
        return !$this->isCompleted() && 
               $this->due_date && 
               $this->due_date->between(now(), now()->addDays($days));
    }

    /**
     * Check if all dependencies are completed.
     */
    public function areDependenciesCompleted(): bool
    {
        if (!$this->dependencies || empty($this->dependencies)) {
            return true;
        }

        $dependencyMilestones = $this->dependencyMilestones();
        
        return $dependencyMilestones->every(function ($milestone) {
            return $milestone->isCompleted();
        });
    }

    /**
     * Check if milestone can be started (dependencies completed).
     */
    public function canBeStarted(): bool
    {
        return $this->areDependenciesCompleted() && 
               !$this->isCompleted();
    }

    /**
     * Get the status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_COMPLETED => 'green',
            self::STATUS_IN_PROGRESS => 'blue',
            self::STATUS_DELAYED => 'red',
            self::STATUS_PENDING => 'gray',
            default => 'gray'
        };
    }

    /**
     * Get the priority badge color.
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_CRITICAL => 'red',
            self::PRIORITY_HIGH => 'orange',
            self::PRIORITY_NORMAL => 'blue',
            self::PRIORITY_LOW => 'gray',
            default => 'gray'
        };
    }

    /**
     * Get formatted status.
     */
    public function getFormattedStatusAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get formatted priority.
     */
    public function getFormattedPriorityAttribute(): string
    {
        return self::getPriorities()[$this->priority] ?? ucfirst($this->priority);
    }

    /**
     * Get the completion percentage as a formatted string.
     */
    public function getCompletionPercentageAttribute(): string
    {
        return ($this->progress_percent ?? 0) . '%';
    }

    /**
     * Get days until due date.
     */
    public function getDaysUntilDueAttribute(): ?int
    {
        if (!$this->due_date) {
            return null;
        }

        return now()->diffInDays($this->due_date, false);
    }

    /**
     * Get days overdue (negative if not overdue).
     */
    public function getDaysOverdueAttribute(): int
    {
        if (!$this->due_date || $this->isCompleted()) {
            return 0;
        }

        return now()->diffInDays($this->due_date, false) * -1;
    }

    /**
     * Get the milestone duration in days.
     */
    public function getDurationAttribute(): ?int
    {
        if (!$this->completed_date || !$this->due_date) {
            return null;
        }

        return $this->due_date->diffInDays($this->completed_date);
    }

    /**
     * Get time variance (actual vs estimated hours).
     */
    public function getTimeVarianceAttribute(): ?float
    {
        if (!$this->estimated_hours || !$this->actual_hours) {
            return null;
        }

        return $this->actual_hours - $this->estimated_hours;
    }

    /**
     * Get time variance percentage.
     */
    public function getTimeVariancePercentageAttribute(): ?float
    {
        if (!$this->estimated_hours || !$this->actual_hours) {
            return null;
        }

        return (($this->actual_hours - $this->estimated_hours) / $this->estimated_hours) * 100;
    }

    /**
     * Check if milestone was completed on time.
     */
    public function wasCompletedOnTime(): ?bool
    {
        if (!$this->isCompleted() || !$this->due_date || !$this->completed_date) {
            return null;
        }

        return $this->completed_date->lessThanOrEqualTo($this->due_date);
    }

    /**
     * Get milestone efficiency rating.
     */
    public function getEfficiencyRating(): string
    {
        if (!$this->isCompleted()) {
            return 'pending';
        }

        $onTime = $this->wasCompletedOnTime();
        $timeVariance = $this->time_variance_percentage;

        if ($onTime && ($timeVariance === null || $timeVariance <= 10)) {
            return 'excellent';
        } elseif ($onTime && $timeVariance <= 25) {
            return 'good';
        } elseif ($onTime) {
            return 'fair';
        } else {
            return 'poor';
        }
    }

    /**
     * Get the admin URL for this milestone.
     */
    public function getAdminUrlAttribute(): string
    {
        return route('admin.projects.milestones.edit', [$this->project, $this]);
    }

    /**
     * Get blocked milestones (those waiting for this one).
     */
    public function getBlockedMilestonesAttribute()
    {
        if ($this->isCompleted()) {
            return collect();
        }

        return $this->dependentMilestones();
    }

    /**
     * Check if milestone is blocking other milestones.
     */
    public function isBlockingOthers(): bool
    {
        return !$this->isCompleted() && $this->dependentMilestones()->count() > 0;
    }

    /**
     * Get milestone health status.
     */
    public function getHealthStatus(): string
    {
        if ($this->isCompleted()) {
            return $this->wasCompletedOnTime() ? 'healthy' : 'completed_late';
        }

        if ($this->isOverdue()) {
            return 'overdue';
        }

        if ($this->isDueSoon(3)) {
            return 'due_soon';
        }

        if (!$this->areDependenciesCompleted()) {
            return 'blocked';
        }

        return 'on_track';
    }

    /**
     * Auto-update milestone status based on progress and dependencies.
     */
    public function updateAutomaticStatus(): void
    {
        // Don't auto-update if manually set to completed or delayed
        if (in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_DELAYED])) {
            return;
        }

        // Check if dependencies are completed
        if (!$this->areDependenciesCompleted()) {
            $this->update(['status' => self::STATUS_PENDING]);
            return;
        }

        // Check if overdue
        if ($this->isOverdue()) {
            $this->update(['status' => self::STATUS_DELAYED]);
            return;
        }

        // If progress is started but not completed
        if ($this->progress_percent > 0 && $this->progress_percent < 100) {
            $this->update(['status' => self::STATUS_IN_PROGRESS]);
            return;
        }

        // If progress is 100%, mark as completed
        if ($this->progress_percent >= 100) {
            $this->update([
                'status' => self::STATUS_COMPLETED,
                'completed_date' => $this->completed_date ?? now()
            ]);
            return;
        }
    }

    /**
     * Get related milestones (same project, excluding self).
     */
    public function getRelatedMilestones()
    {
        return static::where('project_id', $this->project_id)
            ->where('id', '!=', $this->id)
            ->orderBy('due_date')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Calculate milestone risk score.
     */
    public function getRiskScore(): int
    {
        if ($this->isCompleted()) {
            return 0;
        }

        $risk = 0;

        // Overdue adds high risk
        if ($this->isOverdue()) {
            $risk += 50;
            $risk += min($this->days_overdue * 5, 50); // Cap at 50
        }

        // Due soon adds medium risk
        if ($this->isDueSoon(7)) {
            $risk += 20;
        }

        // High priority adds risk
        if ($this->priority === self::PRIORITY_CRITICAL) {
            $risk += 25;
        } elseif ($this->priority === self::PRIORITY_HIGH) {
            $risk += 15;
        }

        // Blocking others adds risk
        if ($this->isBlockingOthers()) {
            $risk += 20;
        }

        // Dependencies not completed adds risk
        if (!$this->areDependenciesCompleted()) {
            $risk += 30;
        }

        return min($risk, 100); // Cap at 100
    }

    /**
     * Get risk level based on risk score.
     */
    public function getRiskLevel(): string
    {
        $score = $this->getRiskScore();

        if ($score >= 80) return 'critical';
        if ($score >= 60) return 'high';
        if ($score >= 40) return 'medium';
        if ($score >= 20) return 'low';
        
        return 'minimal';
    }
}