<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterableTrait;

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
        'client_id',
        'admin_notes',
        'additional_info',
        'client_approved',
        'client_decline_reason',
        'client_approved_at',
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
    ];
    
    /**
     * The filterable attributes for the model.
     *
     * @var array
     */
    protected $filterable = [
        'status',
        'service_id',
        'search',
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
     * Get quotation attachments.
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
    
    /**
     * Scope a query to only include pending quotations.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    /**
     * Scope a query to only include approved quotations.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}