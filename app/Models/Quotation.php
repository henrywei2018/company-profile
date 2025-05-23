<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterableTrait;
use Carbon\Carbon;

class Quotation extends Model
{
    use HasFactory, FilterableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'service_id',
        'project_type',
        'location',
        'requirements',
        'budget_range',
        'start_date',
        'status',
        'priority',
        'source',
        'client_id',
        'admin_notes',
        'internal_notes',
        'additional_info',
        'client_approved',
        'client_decline_reason',
        'client_approved_at',
        'estimated_cost',
        'estimated_timeline',
        'reviewed_at',
        'approved_at',
        'last_communication_at',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'client_approved' => 'boolean',
        'client_approved_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'last_communication_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'project_created' => 'boolean',
        'project_created_at' => 'datetime',
        
    ];
    
    /**
     * The filterable attributes for the model.
     *
     * @var array
     */
    protected $filterable = [
        'status',
        'priority',
        'service_id',
        'client_id',
        'source',
        'search',
        'project_created',
        'project_created_at',
    ];
    
    /**
     * The searchable attributes for the model.
     *
     * @var array
     */
    protected $searchable = [
        'name',
        'email',
        'company',
        'project_type',
        'location',
        'requirements',
    ];
    
    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    
    /**
     * Priority constants
     */
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';
    
    /**
     * Source constants
     */
    const SOURCE_WEBSITE = 'website';
    const SOURCE_PHONE = 'phone';
    const SOURCE_EMAIL = 'email';
    const SOURCE_REFERRAL = 'referral';
    const SOURCE_SOCIAL_MEDIA = 'social_media';
    
    /**
     * Get all available statuses
     *
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending Review',
            self::STATUS_REVIEWED => 'Under Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    public function attachments()
    {
        return $this->hasMany(QuotationAttachment::class);
    }
    
    /**
     * Get all available priorities
     *
     * @return array
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
     * Get all available sources
     *
     * @return array
     */
    public static function getSources(): array
    {
        return [
            self::SOURCE_WEBSITE => 'Website Form',
            self::SOURCE_PHONE => 'Phone Call',
            self::SOURCE_EMAIL => 'Email',
            self::SOURCE_REFERRAL => 'Referral',
            self::SOURCE_SOCIAL_MEDIA => 'Social Media',
        ];
    }
    
    /**
     * Get the service associated with the quotation.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    
    /**
     * Get the client associated with the quotation.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    
    /**
     * Get the project created from this quotation
     */
    public function project()
    {
        return $this->hasOne(Project::class, 'quotation_id');
    }
    
    /**
     * Scope a query to only include pending quotations.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
    
    /**
     * Scope a query to only include reviewed quotations.
     */
    public function scopeReviewed($query)
    {
        return $query->where('status', self::STATUS_REVIEWED);
    }
    
    /**
     * Scope a query to only include approved quotations.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }
    
    /**
     * Scope a query to only include rejected quotations.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }
    
    /**
     * Scope a query to only include high priority quotations.
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', [self::PRIORITY_HIGH, self::PRIORITY_URGENT]);
    }
    
    /**
     * Scope a query to only include urgent quotations.
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', self::PRIORITY_URGENT);
    }
    
    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }
    
    /**
     * Scope a query to filter by this month.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
    }
    
    /**
     * Scope a query to filter by this week.
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }
    
    /**
     * Scope a query to filter by recent quotations (last 7 days).
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays(7));
    }
    
    /**
     * Check if quotation is overdue (pending for more than X days)
     */
    public function isOverdue($days = 3): bool
    {
        return $this->status === self::STATUS_PENDING && 
               $this->created_at->diffInDays(Carbon::now()) > $days;
    }
    
    /**
     * Check if quotation needs attention (high priority or overdue)
     */
    public function needsAttention(): bool
    {
        return $this->priority === self::PRIORITY_URGENT || 
               $this->priority === self::PRIORITY_HIGH || 
               $this->isOverdue();
    }
    
    /**
     * Get the status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_REVIEWED => 'blue',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            default => 'gray'
        };
    }
    
    /**
     * Get the priority badge color
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'gray',
            self::PRIORITY_NORMAL => 'blue',
            self::PRIORITY_HIGH => 'orange',
            self::PRIORITY_URGENT => 'red',
            default => 'gray'
        };
    }
    
    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? ucfirst($this->status);
    }
    
    /**
     * Get formatted priority
     */
    public function getFormattedPriorityAttribute(): string
    {
        return self::getPriorities()[$this->priority] ?? ucfirst($this->priority);
    }
    
    /**
     * Get response time in business days
     */
    public function getResponseTimeAttribute(): ?int
    {
        if (!$this->reviewed_at) {
            return null;
        }
        
        return $this->created_at->diffInWeekdays($this->reviewed_at);
    }
    
    /**
     * Get approval time in business days
     */
    public function getApprovalTimeAttribute(): ?int
    {
        if (!$this->approved_at) {
            return null;
        }
        
        return $this->created_at->diffInWeekdays($this->approved_at);
    }
    
    /**
     * Check if quotation has been converted to project
     */
    public function hasProject(): bool
    {
        return $this->project_created || $this->project()->exists();
    }
    public function canCreateProject(): bool
    {
        return $this->status === 'approved' && !$this->hasProject();
    }
    
    /**
     * Get days since creation
     */
    public function getDaysSinceCreationAttribute(): int
    {
        return $this->created_at->diffInDays(Carbon::now());
    }
    
    /**
     * Auto-set source from website if not provided
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($quotation) {
            if (empty($quotation->source)) {
                $quotation->source = self::SOURCE_WEBSITE;
            }
            
            if (empty($quotation->priority)) {
                $quotation->priority = self::PRIORITY_NORMAL;
            }
        });
        
        static::updating(function ($quotation) {
            // Auto-set timestamps when status changes
            if ($quotation->isDirty('status')) {
                $newStatus = $quotation->getAttributes()['status'];
                $oldStatus = $quotation->getOriginal('status');
                
                if ($newStatus === self::STATUS_REVIEWED && $oldStatus !== self::STATUS_REVIEWED) {
                    $quotation->reviewed_at = Carbon::now();
                }
                
                if ($newStatus === self::STATUS_APPROVED && $oldStatus !== self::STATUS_APPROVED) {
                    $quotation->approved_at = Carbon::now();
                }
            }
        });
    }
}