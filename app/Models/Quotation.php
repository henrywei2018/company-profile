<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterableTrait;
use App\Traits\QuotationProjectConversion;
use Carbon\Carbon;

class Quotation extends Model
{
    use HasFactory, FilterableTrait, QuotationProjectConversion;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quotation_number',
        'name',
        'email',
        'phone',
        'company',
        'service_id',
        'project_type',
        'location',
        'requirements',
        'budget_range',
        'estimated_cost',
        'estimated_timeline',
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
        'reviewed_at',
        'approved_at',
        'last_communication_at',
        'project_created',
        'project_created_at',
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

    /**
     * Get quotation attachments
     */
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
     * Uses existing quotation_id field in projects table
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
     * Scope for quotations that have been converted to projects
     */
    public function scopeConvertedToProject($query)
    {
        return $query->where('project_created', true);
    }

    /**
     * Scope for quotations ready for project conversion
     */
    public function scopeReadyForConversion($query)
    {
        return $query->where('status', self::STATUS_APPROVED)
                    ->where('project_created', false);
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
     * Check if this quotation can be converted to a project
     * Using existing fields only
     */
    public function canConvertToProject(): bool
    {
        return $this->status === self::STATUS_APPROVED && 
               !$this->project_created && 
               !$this->hasExistingProject();
    }

    /**
     * Check if there's already a project for this quotation
     * Uses existing quotation_id field in projects table
     */
    public function hasExistingProject(): bool
    {
        return $this->project()->exists();
    }

    /**
     * Get the existing project if it exists
     */
    public function getExistingProject(): ?Project
    {
        return $this->project;
    }

    public function isExpired(int $expireDays = 30): bool
{
    // Only approved quotations can expire
    if ($this->status !== self::STATUS_APPROVED) {
        return false;
    }
    
    // Check if quotation has an approval date
    if (!$this->approved_at) {
        return false;
    }
    
    // Check if quotation has been converted to project
    // If converted, it shouldn't be considered expired
    if ($this->project_created || $this->hasExistingProject()) {
        return false;
    }
    
    // Check if client has already approved
    // If client approved, extend the expiry
    if ($this->client_approved === true) {
        $expireDays = $expireDays + 14; // Add 14 days extension for client-approved quotations
    }
    
    return $this->approved_at->diffInDays(now()) > $expireDays;
}

/**
 * Check if quotation is expiring soon (within warning period)
 * 
 * @param int $warningDays Number of days before expiry to show warning (default: 5)
 * @param int $expireDays Total days until expiry (default: 30)
 * @return bool
 */
public function isExpiringSoon(int $warningDays = 5, int $expireDays = 30): bool
{
    // Only approved quotations can expire
    if ($this->status !== self::STATUS_APPROVED) {
        return false;
    }
    
    // Check if quotation has an approval date
    if (!$this->approved_at) {
        return false;
    }
    
    // Check if quotation has been converted to project
    if ($this->project_created || $this->hasExistingProject()) {
        return false;
    }
    
    // Adjust expiry days if client approved
    if ($this->client_approved === true) {
        $expireDays = $expireDays + 14;
    }
    
    $daysFromApproval = $this->approved_at->diffInDays(now());
    
    return $daysFromApproval > ($expireDays - $warningDays) && $daysFromApproval <= $expireDays;
}

/**
 * Get days until quotation expires
 * 
 * @param int $expireDays Total days until expiry (default: 30)
 * @return int|null Days remaining (null if not applicable)
 */
public function getDaysUntilExpiry(int $expireDays = 30): ?int
{
    // Only approved quotations can expire
    if ($this->status !== self::STATUS_APPROVED || !$this->approved_at) {
        return null;
    }
    
    // Check if quotation has been converted to project
    if ($this->project_created || $this->hasExistingProject()) {
        return null;
    }
    
    // Adjust expiry days if client approved
    if ($this->client_approved === true) {
        $expireDays = $expireDays + 14;
    }
    
    $daysFromApproval = $this->approved_at->diffInDays(now());
    $daysRemaining = $expireDays - $daysFromApproval;
    
    return max(0, $daysRemaining);
}

/**
 * Get expiry status with detailed information
 * 
 * @param int $expireDays Total days until expiry (default: 30)
 * @param int $warningDays Warning period before expiry (default: 5)
 * @return array
 */
public function getExpiryStatus(int $expireDays = 30, int $warningDays = 5): array
{
    if ($this->status !== self::STATUS_APPROVED || !$this->approved_at) {
        return [
            'status' => 'not_applicable',
            'message' => 'Quotation is not approved or has no approval date',
            'days_remaining' => null,
            'is_expired' => false,
            'is_expiring_soon' => false,
        ];
    }
    
    if ($this->project_created || $this->hasExistingProject()) {
        return [
            'status' => 'converted',
            'message' => 'Quotation has been converted to project',
            'days_remaining' => null,
            'is_expired' => false,
            'is_expiring_soon' => false,
        ];
    }
    
    $adjustedExpireDays = $expireDays;
    if ($this->client_approved === true) {
        $adjustedExpireDays = $expireDays + 14;
    }
    
    $daysFromApproval = $this->approved_at->diffInDays(now());
    $daysRemaining = $adjustedExpireDays - $daysFromApproval;
    
    if ($daysRemaining <= 0) {
        return [
            'status' => 'expired',
            'message' => 'Quotation has expired',
            'days_remaining' => 0,
            'is_expired' => true,
            'is_expiring_soon' => false,
            'expired_days_ago' => abs($daysRemaining),
        ];
    }
    
    if ($daysRemaining <= $warningDays) {
        return [
            'status' => 'expiring_soon',
            'message' => "Quotation expires in {$daysRemaining} day(s)",
            'days_remaining' => $daysRemaining,
            'is_expired' => false,
            'is_expiring_soon' => true,
        ];
    }
    
    return [
        'status' => 'active',
        'message' => "Quotation expires in {$daysRemaining} day(s)",
        'days_remaining' => $daysRemaining,
        'is_expired' => false,
        'is_expiring_soon' => false,
    ];
}

/**
 * Scope for expired quotations
 */
public function scopeExpired($query, int $expireDays = 30)
{
    return $query->where('status', self::STATUS_APPROVED)
                ->whereNotNull('approved_at')
                ->where('project_created', false)
                ->whereDoesntHave('project')
                ->where(function ($q) use ($expireDays) {
                    // Regular quotations (not client approved)
                    $q->where(function ($subQ) use ($expireDays) {
                        $subQ->where(function ($innerQ) {
                            $innerQ->whereNull('client_approved')
                                  ->orWhere('client_approved', false);
                        })
                        ->whereRaw("DATEDIFF(NOW(), approved_at) > ?", [$expireDays]);
                    })
                    // Client approved quotations (extended expiry)
                    ->orWhere(function ($subQ) use ($expireDays) {
                        $subQ->where('client_approved', true)
                            ->whereRaw("DATEDIFF(NOW(), approved_at) > ?", [$expireDays + 14]);
                    });
                });
}

/**
 * Scope for quotations expiring soon
 */
public function scopeExpiringSoon($query, int $warningDays = 5, int $expireDays = 30)
{
    return $query->where('status', self::STATUS_APPROVED)
                ->whereNotNull('approved_at')
                ->where('project_created', false)
                ->whereDoesntHave('project')
                ->where(function ($q) use ($warningDays, $expireDays) {
                    // Regular quotations (not client approved)
                    $q->where(function ($subQ) use ($warningDays, $expireDays) {
                        $subQ->where(function ($innerQ) {
                            $innerQ->whereNull('client_approved')
                                  ->orWhere('client_approved', false);
                        })
                        ->whereRaw("DATEDIFF(NOW(), approved_at) > ?", [$expireDays - $warningDays])
                        ->whereRaw("DATEDIFF(NOW(), approved_at) <= ?", [$expireDays]);
                    })
                    // Client approved quotations (extended expiry)
                    ->orWhere(function ($subQ) use ($warningDays, $expireDays) {
                        $subQ->where('client_approved', true)
                            ->whereRaw("DATEDIFF(NOW(), approved_at) > ?", [($expireDays + 14) - $warningDays])
                            ->whereRaw("DATEDIFF(NOW(), approved_at) <= ?", [$expireDays + 14]);
                    });
                });
}

    /**
     * Check if quotation has been converted to project
     * Using existing project_created field
     */
    public function hasProject(): bool
    {
        return $this->project_created || $this->hasExistingProject();
    }

    /**
     * Check if quotation is ready for project conversion
     */
    public function isReadyForProjectConversion(): bool
    {
        return $this->status === self::STATUS_APPROVED && 
               ($this->client_approved === true || $this->client_approved === null) &&
               !$this->project_created;
    }
    
    /**
     * Get the status badge color.
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
     * Get the priority badge color.
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
     * Get days since creation
     */
    public function getDaysSinceCreationAttribute(): int
    {
        return $this->created_at->diffInDays(Carbon::now());
    }

    /**
     * Get suggested project data from quotation
     * Uses existing quotation fields to suggest project data
     */
    public function getSuggestedProjectData(): array
    {
        $data = [
            'title' => $this->project_type ?? 'Project from Quotation #' . $this->id,
            'description' => $this->requirements ?? 'Project created from quotation request',
            'client_id' => $this->client_id,
            'quotation_id' => $this->id,
            'location' => $this->location,
            'status' => 'planning',
            'start_date' => $this->start_date,
            'year' => $this->start_date ? $this->start_date->year : now()->year,
            'featured' => false,
            'priority' => $this->priority ?? 'normal',
        ];

        // Add service mapping if the project has service_id field
        if ($this->service_id && \Illuminate\Support\Facades\Schema::hasColumn('projects', 'service_id')) {
            $data['service_id'] = $this->service_id;
        }

        // Add client name if no client_id but projects table has client_name field
        if (!$this->client_id && \Illuminate\Support\Facades\Schema::hasColumn('projects', 'client_name')) {
            $data['client_name'] = $this->name;
        }

        // Add additional fields if they exist in projects table
        if (\Illuminate\Support\Facades\Schema::hasColumn('projects', 'short_description')) {
            $data['short_description'] = $this->requirements ? \Illuminate\Support\Str::limit($this->requirements, 200) : null;
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('projects', 'is_active')) {
            $data['is_active'] = true;
        }

        // Try to extract budget if projects table has budget field
        if (\Illuminate\Support\Facades\Schema::hasColumn('projects', 'budget')) {
            $data['budget'] = $this->extractBudgetAmount();
        }

        // Suggest category based on service or project type if projects table has category_id field
        if (\Illuminate\Support\Facades\Schema::hasColumn('projects', 'project_category_id')) {
            $data['project_category_id'] = $this->suggestProjectCategory();
        }

        // Estimate completion date if projects table has estimated_completion_date field
        if (\Illuminate\Support\Facades\Schema::hasColumn('projects', 'estimated_completion_date')) {
            $data['estimated_completion_date'] = $this->estimateCompletionDate();
        }

        return $data;
    }

    /**
     * Extract numeric budget amount from existing text fields
     */
    public function extractBudgetAmount(): ?float
    {
        // Try estimated_cost first, then budget_range
        $budgetText = $this->estimated_cost ?? $this->budget_range;
        
        if (!$budgetText) {
            return null;
        }

        // Remove currency symbols and extract numbers
        $cleanText = preg_replace('/[^\d.,\-]/', '', $budgetText);
        
        // Handle ranges (take the higher value)
        if (strpos($cleanText, '-') !== false) {
            $parts = explode('-', $cleanText);
            $cleanText = trim(end($parts));
        }

        // Clean up and convert to float
        $cleanText = str_replace(',', '', $cleanText);
        
        if (is_numeric($cleanText)) {
            return floatval($cleanText);
        }

        return null;
    }

    /**
     * Suggest project category based on service or project type
     */
    public function suggestProjectCategory(): ?int
    {
        // First try to match by service
        if ($this->service) {
            $category = \App\Models\ProjectCategory::where('name', 'like', '%' . $this->service->title . '%')
                ->where('is_active', true)
                ->first();
            
            if ($category) {
                return $category->id;
            }
        }

        // Then try to match by project type
        if ($this->project_type) {
            $keywords = explode(' ', strtolower($this->project_type));
            
            foreach ($keywords as $keyword) {
                $category = \App\Models\ProjectCategory::where('name', 'like', '%' . $keyword . '%')
                    ->where('is_active', true)
                    ->first();
                
                if ($category) {
                    return $category->id;
                }
            }
        }

        // Try to find a default or "General" category
        $defaultCategory = \App\Models\ProjectCategory::where('name', 'like', '%general%')
            ->orWhere('name', 'like', '%default%')
            ->orWhere('name', 'like', '%misc%')
            ->where('is_active', true)
            ->first();

        return $defaultCategory?->id;
    }

    /**
     * Estimate project completion date
     */
    public function estimateCompletionDate(): ?Carbon
    {
        if ($this->start_date) {
            // Estimate based on project type or default to 3 months
            $estimatedDuration = $this->estimateProjectDuration();
            return $this->start_date->copy()->addMonths($estimatedDuration);
        }

        // Default to 3 months from now
        return now()->addMonths(3);
    }

    /**
     * Estimate project duration in months based on project type
     */
    protected function estimateProjectDuration(): int
    {
        if (!$this->project_type) {
            return 3; // Default 3 months
        }

        $projectType = strtolower($this->project_type);
        
        // Define duration estimates based on common project types
        $durationMap = [
            'website' => 2,
            'web development' => 3,
            'mobile app' => 4,
            'construction' => 6,
            'renovation' => 3,
            'interior design' => 2,
            'marketing campaign' => 2,
            'software development' => 4,
            'system integration' => 5,
            'consulting' => 1,
            'training' => 1,
            'audit' => 1,
        ];

        foreach ($durationMap as $keyword => $months) {
            if (strpos($projectType, $keyword) !== false) {
                return $months;
            }
        }

        // If no match, estimate based on budget
        $budget = $this->extractBudgetAmount();
        if ($budget) {
            if ($budget < 10000) return 1;
            if ($budget < 50000) return 2;
            if ($budget < 100000) return 3;
            if ($budget < 500000) return 6;
            return 12;
        }

        return 3; // Default fallback
    }

    /**
     * Mark quotation as converted to project using existing fields
     */
    public function markAsConvertedToProject(Project $project): void
    {
        $updateData = [
            'project_created' => true,
            'project_created_at' => now(),
        ];

        // Add admin notes about the conversion using existing admin_notes field
        if ($this->admin_notes) {
            $updateData['admin_notes'] = $this->admin_notes . "\n\n" 
                . "Converted to project: {$project->title} (ID: {$project->id}) on " . now()->format('Y-m-d H:i:s');
        } else {
            $updateData['admin_notes'] = "Converted to project: {$project->title} (ID: {$project->id}) on " . now()->format('Y-m-d H:i:s');
        }

        $this->update($updateData);
    }

    /**
     * Get conversion summary for this quotation
     */
    public function getConversionSummary(): array
    {
        $summary = [
            'can_convert' => $this->canConvertToProject(),
            'is_ready' => $this->isReadyForProjectConversion(),
            'has_existing_project' => $this->hasExistingProject(),
            'existing_project' => $this->getExistingProject(),
            'suggested_data' => $this->getSuggestedProjectData(),
            'attachments_count' => $this->attachments->count(),
            'estimated_budget' => $this->extractBudgetAmount(),
            'estimated_duration' => $this->estimateProjectDuration(),
        ];

        // Add validation warnings
        $warnings = [];
        
        if (!$this->client_id) {
            $warnings[] = 'No linked client account - project will use client name only';
        }

        if (!$this->start_date) {
            $warnings[] = 'No start date specified - project timeline may need adjustment';
        }

        if (!$this->extractBudgetAmount()) {
            $warnings[] = 'No budget information available';
        }

        if (!$this->service_id) {
            $warnings[] = 'No service specified - project category may need manual selection';
        }

        $summary['warnings'] = $warnings;

        return $summary;
    }

    /**
     * Validate quotation for conversion
     */
    public function validateForConversion(): array
    {
        $errors = [];
        $warnings = [];

        // Check basic eligibility
        if ($this->status !== self::STATUS_APPROVED) {
            $errors[] = 'Quotation must be approved before conversion';
        }

        if ($this->project_created) {
            $errors[] = 'Quotation has already been converted to a project';
        }

        if ($this->hasExistingProject()) {
            $errors[] = 'A project already exists for this quotation';
        }

        // Check for potential issues
        if (!$this->project_type || strlen($this->project_type) < 3) {
            $warnings[] = 'Project type is very short or missing';
        }

        if (!$this->requirements || strlen($this->requirements) < 10) {
            $warnings[] = 'Project requirements are very brief';
        }

        if ($this->client_approved === false) {
            $warnings[] = 'Client has declined this quotation';
        }

        if (!$this->client_id) {
            $warnings[] = 'No registered client account linked';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
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

            // Generate quotation number if not provided
            if (empty($quotation->quotation_number)) {
                $quotation->quotation_number = 'QUO-' . now()->format('Y') . '-' . str_pad(static::whereYear('created_at', now()->year)->count() + 1, 4, '0', STR_PAD_LEFT);
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