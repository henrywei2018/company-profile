<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'client_id',           // Added in migration
        'client_name',
        'client_position',
        'client_company',
        'content',
        'image',
        'rating',
        'is_active',
        'featured',
        'status',              // Added in migration
        'approved_at',         // Added in migration
        'admin_notes',         // Added in migration
        'approval_notification_sent_at',  // Added in migration
        'featured_notification_sent_at',  // Added in migration
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'featured' => 'boolean',
        'rating' => 'integer',
        'approved_at' => 'datetime',
        'approval_notification_sent_at' => 'datetime',
        'featured_notification_sent_at' => 'datetime',
    ];

    protected $dates = [
        'approved_at',
        'approval_notification_sent_at',
        'featured_notification_sent_at',
    ];

    // ================================
    // RELATIONSHIPS
    // ================================

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    // ================================
    // SCOPES
    // ================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeOrderedByRating($query)
    {
        return $query->orderBy('rating', 'desc');
    }

    // ================================
    // ACCESSORS & MUTATORS
    // ================================

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'featured' => 'purple',
            default => 'gray'
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'featured' => 'Featured',
            default => 'Unknown'
        };
    }

    // ================================
    // METHODS
    // ================================

    public function approve()
    {
        $this->update([
            'status' => 'approved',
            'is_active' => true,
            'approved_at' => now(),
        ]);
    }

    public function reject($notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'is_active' => false,
            'admin_notes' => $notes,
        ]);
    }

    public function setAsFeatured()
    {
        $this->update([
            'featured' => true,
            'status' => 'featured',
            'is_active' => true,
        ]);
    }

    public function removeFeatured()
    {
        $this->update([
            'featured' => false,
            'status' => 'approved',
        ]);
    }
}