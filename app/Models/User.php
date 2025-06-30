<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\ImageableTrait;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpVerificationMail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, ImageableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'company',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'avatar',
        'is_active',
        'settings',
        'email_verified_at',
        'last_login_at',
        'login_count',
        'failed_login_attempts',
        'locked_at',
        'otp_code',
        'otp_expires_at',
        
        // Notification preferences fields from migration
        'email_notifications',
        'project_update_notifications',
        'quotation_update_notifications',
        'message_reply_notifications',
        'deadline_alert_notifications',
        'system_notifications',
        'marketing_emails',
        'profile_reminder_sent_at',
        'last_notification_sent_at',
        'notification_frequency',
        'quiet_hours',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'settings' => 'array',
        'last_login_at' => 'datetime',
        'locked_at' => 'datetime',
        'login_count' => 'integer',
        'failed_login_attempts' => 'integer',
        'otp_expires_at' => 'datetime',
        
        // Notification preferences casts
        'email_notifications' => 'boolean',
        'project_update_notifications' => 'boolean',
        'quotation_update_notifications' => 'boolean',
        'message_reply_notifications' => 'boolean',
        'deadline_alert_notifications' => 'boolean',
        'system_notifications' => 'boolean',
        'marketing_emails' => 'boolean',
        'profile_reminder_sent_at' => 'datetime',
        'last_notification_sent_at' => 'datetime',
        'quiet_hours' => 'array', // JSON field for storing quiet hours
    ];
    public function hasVerifiedEmail()
    {
        return ! is_null($this->email_verified_at);
    }
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }
    public function getEmailForVerification()
    {
        return $this->email;
    }
    public function sendEmailVerificationNotification()
    {
     
    }
    public function generateOtp()
    {
        $this->otp_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->otp_expires_at = Carbon::now()->addMinutes(10); // 10 minutes expiry
        $this->save();
    }
    public function hasValidOtp(): bool
    {
        $isValid = $this->otp_code && 
                $this->otp_expires_at && 
                !$this->otp_expires_at->isPast();               
                
        return $isValid;
    }
    public function verifyOtp($code)
    {
        if (!$this->otp_code || !$this->otp_expires_at) {
            return false;
        }

        if (Carbon::now()->isAfter($this->otp_expires_at)) {
            return false; // OTP expired
        }

        return $this->otp_code === $code;
    }
    public function clearOtp()
    {
        $this->otp_code = null;
        $this->otp_expires_at = null;
        $this->save();
    }
    public function getOtpRemainingMinutes(): int
    {
        if (!$this->otp_expires_at) {
            return 0;
        }
        
        return max(0, Carbon::now()->diffInMinutes($this->otp_expires_at, false));
    }
    /**
     * Get the projects associated with the user.
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'client_id');
    }
    
    /**
     * Get the quotations associated with the user.
     */
    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'client_id');
    }
    
    /**
     * Get the messages associated with the user.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    
    /**
     * Get the blog posts created by the user.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    
    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->hasAnyRole(['super-admin', 'admin']);
    }
    
    /**
     * Check if user is super admin.
     */
    public function isSuperAdmin()
    {
        return $this->hasRole('super-admin');
    }
    
    /**
     * Check if user is client.
     */
    public function isClient()
    {
        return $this->hasRole('client');
    }
    
    /**
     * Check if user is manager.
     */
    public function isManager()
    {
        return $this->hasRole('manager');
    }
    
    /**
     * Check if user is editor.
     */
    public function isEditor()
    {
        return $this->hasRole('editor');
    }
    
    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope a query to only include inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
    
    /**
     * Scope a query to filter by role.
     */
    public function scopeWithRole($query, $role)
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role);
        });
    }
    
    /**
     * Scope a query to filter by multiple roles.
     */
    public function scopeWithAnyRole($query, array $roles)
    {
        return $query->whereHas('roles', function ($q) use ($roles) {
            $q->whereIn('name', $roles);
        });
    }
    
    /**
     * Scope a query to filter by permission.
     */
    public function scopeWithPermission($query, $permission)
    {
        return $query->whereHas('permissions', function ($q) use ($permission) {
            $q->where('name', $permission);
        })->orWhereHas('roles.permissions', function ($q) use ($permission) {
            $q->where('name', $permission);
        });
    }
    
    /**
     * Scope a query to only include verified users.
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }
    
    /**
     * Scope a query to only include unverified users.
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }
    
    /**
     * Get avatar URL or default avatar.
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        return asset('images/default-avatar.jpg');
    }
    
    /**
     * Get user's full name with role.
     */
    public function getFullNameWithRoleAttribute()
    {
        $roles = $this->roles->pluck('name')->join(', ');
        return $this->name . ($roles ? " ({$roles})" : '');
    }
    
    /**
     * Get user's primary role (highest priority).
     */
    public function getPrimaryRoleAttribute()
    {
        $roleHierarchy = [
            'super-admin' => 1,
            'admin' => 2,
            'manager' => 3,
            'editor' => 4,
            'client' => 5,
        ];
        
        $userRoles = $this->roles->pluck('name')->toArray();
        
        foreach ($roleHierarchy as $role => $priority) {
            if (in_array($role, $userRoles)) {
                return $role;
            }
        }
        
        return $userRoles[0] ?? null;
    }
    
    /**
     * Get formatted primary role name.
     */
    public function getPrimaryRoleNameAttribute()
    {
        $roleNames = [
            'super-admin' => 'Super Administrator',
            'admin' => 'Administrator',
            'manager' => 'Manager',
            'editor' => 'Editor',
            'client' => 'Client',
        ];
        
        return $roleNames[$this->primary_role] ?? ucfirst(str_replace('-', ' ', $this->primary_role ?? 'User'));
    }
    
    /**
     * Get user's role badge color.
     */
    public function getRoleBadgeColorAttribute()
    {
        $colors = [
            'super-admin' => 'red',
            'admin' => 'blue',
            'manager' => 'purple',
            'editor' => 'green',
            'client' => 'yellow',
        ];
        
        return $colors[$this->primary_role] ?? 'gray';
    }
    
    /**
     * Check if user has any admin-level role.
     */
    public function hasAdminAccess()
    {
        return $this->hasAnyRole(['super-admin', 'admin', 'manager', 'editor']);
    }
    
    /**
     * Check if user can manage other users.
     */
    public function canManageUsers()
    {
        return $this->can('manage users') || $this->hasAnyRole(['super-admin', 'admin']);
    }
    
    /**
     * Check if user can assign roles.
     */
    public function canAssignRoles()
    {
        return $this->can('assign roles') || $this->hasAnyRole(['super-admin', 'admin']);
    }
    
    /**
     * Check if user can assign specific role to another user.
     */
    public function canAssignRole($roleName, User $targetUser = null)
    {
        // Super admin can assign any role
        if ($this->hasRole('super-admin')) {
            return true;
        }
        
        // Admin can assign any role except super-admin
        if ($this->hasRole('admin') && $roleName !== 'super-admin') {
            return true;
        }
        
        // Manager can assign editor and client roles
        if ($this->hasRole('manager') && in_array($roleName, ['editor', 'client'])) {
            return true;
        }
        
        // Editor can only assign client role
        if ($this->hasRole('editor') && $roleName === 'client') {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get permissions tree for display.
     */
    public function getPermissionsTreeAttribute()
    {
        return $this->getAllPermissions()
            ->groupBy(function($permission) {
                return explode(' ', $permission->name)[1] ?? 'general';
            })
            ->map(function($permissions) {
                return $permissions->pluck('name', 'id');
            });
    }
    
    /**
     * Get user's permission count.
     */
    public function getPermissionsCountAttribute()
    {
        return $this->getAllPermissions()->count();
    }
    public function testimonials()
    {
        return $this->hasMany(Testimonial::class, 'client_id');
    }
    /**
     * Check if user account is locked.
     */
    public function isLocked()
    {
        return !is_null($this->locked_at);
    }
    
    /**
     * Lock user account.
     */
    public function lock($reason = null)
    {
        $this->update([
            'locked_at' => now(),
            'is_active' => false,
        ]);
        
        // Log the lock action if you have audit logging
        // ActivityLog::create([...]);
    }
    
    /**
     * Unlock user account.
     */
    public function unlock()
    {
        $this->update([
            'locked_at' => null,
            'failed_login_attempts' => 0,
            'is_active' => true,
        ]);
    }
    
    /**
     * Increment failed login attempts.
     */
    public function incrementFailedLoginAttempts()
    {
        $this->increment('failed_login_attempts');
        
        // Lock account after 5 failed attempts
        if ($this->failed_login_attempts >= 5) {
            $this->lock('Too many failed login attempts');
        }
    }
    
    /**
     * Reset failed login attempts and update last login.
     */
    public function recordSuccessfulLogin()
    {
        $this->update([
            'failed_login_attempts' => 0,
            'last_login_at' => now(),
            'login_count' => $this->login_count + 1,
        ]);
    }
    
    /**
     * Get user activity summary.
     */
    public function getActivitySummaryAttribute()
    {
        return [
            'total_projects' => $this->projects()->count(),
            'total_quotations' => $this->quotations()->count(),
            'total_messages' => $this->messages()->count(),
            'total_posts' => $this->posts()->count(),
            'last_login' => $this->last_login_at?->diffForHumans(),
            'login_count' => $this->login_count,
            'account_age' => $this->created_at->diffForHumans(),
        ];
    }

    /**
     * Get notification preferences with defaults.
     */
    public function getNotificationPreferences(): array
    {
        return [
            'email_notifications' => $this->email_notifications ?? true,
            'project_update_notifications' => $this->project_update_notifications ?? true,
            'quotation_update_notifications' => $this->quotation_update_notifications ?? true,
            'message_reply_notifications' => $this->message_reply_notifications ?? true,
            'deadline_alert_notifications' => $this->deadline_alert_notifications ?? true,
            'system_notifications' => $this->system_notifications ?? false,
            'marketing_emails' => $this->marketing_emails ?? false,
            'notification_frequency' => $this->notification_frequency ?? 'immediate',
            'quiet_hours' => $this->quiet_hours ?? null,
        ];
    }

    /**
     * Check if user should receive specific notification type.
     */
    public function shouldReceiveNotification(string $type): bool
    {
        // Check if email notifications are globally disabled
        if (!($this->email_notifications ?? true)) {
            return false;
        }

        // Check if user is active
        if (!$this->is_active) {
            return false;
        }

        // Check specific notification type preferences
        return match($type) {
            'project.created', 'project.updated', 'project.completed', 'project.status_changed' => 
                $this->project_update_notifications ?? true,
            
            'quotation.created', 'quotation.approved', 'quotation.rejected', 'quotation.status_updated' => 
                $this->quotation_update_notifications ?? true,
            
            'message.reply', 'message.created', 'message.auto_reply' => 
                $this->message_reply_notifications ?? true,
            
            'project.deadline_approaching', 'project.overdue' => 
                $this->deadline_alert_notifications ?? true,
            
            'system.maintenance', 'system.alert', 'system.backup_completed' => 
                $this->system_notifications ?? false,
            
            'marketing.newsletter', 'marketing.promotion' => 
                $this->marketing_emails ?? false,
            
            default => $this->email_notifications ?? true
        };
    }

    /**
     * Check if user is in quiet hours.
     */
    public function isInQuietHours(): bool
    {
        if (!$this->quiet_hours) {
            return false;
        }

        $quietHours = is_array($this->quiet_hours) ? $this->quiet_hours : json_decode($this->quiet_hours, true);
        
        if (!isset($quietHours['start']) || !isset($quietHours['end'])) {
            return false;
        }

        $currentHour = now()->format('H:i');
        $start = $quietHours['start'];
        $end = $quietHours['end'];

        // Handle overnight quiet hours (e.g., 22:00 to 06:00)
        if ($start > $end) {
            return $currentHour >= $start || $currentHour <= $end;
        }

        // Handle same-day quiet hours (e.g., 12:00 to 14:00)
        return $currentHour >= $start && $currentHour <= $end;
    }

    /**
     * Get unread notifications count.
     */
    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->unreadNotifications()->count();
    }

    /**
     * Get recent notifications.
     */
    public function getRecentNotifications(int $limit = 10)
    {
        return $this->notifications()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsAsRead(): int
    {
        $count = $this->unreadNotifications()->count();
        $this->unreadNotifications()->update(['read_at' => now()]);
        return $count;
    }

    /**
     * Update notification timestamp for tracking.
     */
    public function updateNotificationTimestamp(): void
    {
        $this->update(['last_notification_sent_at' => now()]);
    }

    /**
     * Check if profile reminder should be sent.
     */
    public function shouldSendProfileReminder(): bool
    {
        // Don't send if already sent within last 7 days
        if ($this->profile_reminder_sent_at && 
            $this->profile_reminder_sent_at->diffInDays(now()) < 7) {
            return false;
        }

        // Check if profile is incomplete
        return !$this->isProfileComplete();
    }

    /**
     * Check if user profile is complete.
     */
    public function isProfileComplete(): bool
    {
        $requiredFields = ['name', 'email', 'phone', 'company', 'address'];
        
        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Mark profile reminder as sent.
     */
    public function markProfileReminderSent(): void
    {
        $this->update(['profile_reminder_sent_at' => now()]);
    }

    /**
     * Get notification route for specific channel.
     */
    public function routeNotificationForMail($notification = null)
    {
        return $this->email;
    }

    /**
     * Get notification route for database.
     */
    public function routeNotificationForDatabase($notification = null)
    {
        return $this;
    }

    /**
     * Get chat operator relationship.
     */
    public function chatOperator()
    {
        return $this->hasOne(ChatOperator::class);
    }

    /**
     * Get chat sessions where user is the client.
     */
    public function chatSessions()
    {
        return $this->hasMany(ChatSession::class);
    }

    /**
     * Get chat sessions where user is the operator.
     */
    public function operatingChatSessions()
    {
        return $this->hasMany(ChatSession::class, 'assigned_operator_id');
    }

    /**
     * Get active chat sessions for this user.
     */
    public function activeChatSessions()
    {
        return $this->chatSessions()
            ->whereIn('status', ['active', 'waiting']);
    }

    /**
     * Get active chat sessions where user is operating.
     */
    public function activeOperatingChatSessions()
    {
        return $this->operatingChatSessions()
            ->whereIn('status', ['active', 'waiting']);
    }

    /**
     * Check if user is currently online as chat operator.
     */
    public function isChatOperatorOnline(): bool
    {
        return $this->chatOperator && $this->chatOperator->is_online;
    }

    /**
     * Check if user is available for chat.
     */
    public function isChatOperatorAvailable(): bool
    {
        return $this->chatOperator && 
            $this->chatOperator->is_online && 
            $this->chatOperator->is_available;
    }

    /**
     * Get user's current chat operator status.
     */
    public function getChatOperatorStatus(): array
    {
        $operator = $this->chatOperator;
        
        return [
            'is_online' => $operator ? $operator->is_online : false,
            'is_available' => $operator ? $operator->is_available : false,
            'current_chats_count' => $operator ? $operator->current_chats_count : 0,
            'max_concurrent_chats' => $operator ? $operator->max_concurrent_chats : 3,
            'last_seen_at' => $operator ? $operator->last_seen_at : null,
        ];
    }

    /**
     * Get user's unread chat messages count (for operators).
     */
    public function getUnreadChatMessagesCount(): int
    {
        if (!$this->hasAdminAccess()) {
            return 0;
        }

        return ChatMessage::whereHas('chatSession', function ($query) {
            $query->where('assigned_operator_id', $this->id)
                ->whereIn('status', ['active', 'waiting']);
        })
        ->where('sender_type', 'visitor')
        ->where('is_read', false)
        ->count();
    }

    /**
     * Get user's chat statistics (for operators).
     */
    public function getChatStatistics(): array
    {
        if (!$this->hasAdminAccess()) {
            return [];
        }

        $sessions = $this->operatingChatSessions();
        
        return [
            'total_sessions' => $sessions->count(),
            'active_sessions' => $sessions->where('status', 'active')->count(),
            'closed_sessions' => $sessions->where('status', 'closed')->count(),
            'avg_session_duration' => $sessions->where('status', 'closed')
                ->get()
                ->avg(function ($session) {
                    return $session->getDuration();
                }) ?? 0,
            'total_messages_sent' => ChatMessage::where('sender_id', $this->id)
                ->where('sender_type', 'operator')
                ->count(),
        ];
    }

    /**
     * Check if user can handle more chat sessions.
     */
    public function canTakeMoreChatSessions(): bool
    {
        if (!$this->hasAdminAccess() || !$this->isChatOperatorAvailable()) {
            return false;
        }

        $operator = $this->chatOperator;
        return $operator->current_chats_count < $operator->max_concurrent_chats;
    }

    /**
     * Get user's avatar URL for chat.
     */
    public function getChatAvatarUrl(): string
    {
        if ($this->avatar) {
            return Storage::disk('public')->url($this->avatar);
        }

        // Generate avatar initials
        $initials = substr($this->name, 0, 2);
        return "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background=3B82F6&color=ffffff&size=128";
    }
    
    /**
     * Bootstrap the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-assign default role to new users
        static::created(function ($user) {
            if (!$user->roles()->count()) {
                // Assign default client role to new users
                $defaultRole = \Spatie\Permission\Models\Role::where('name', 'client')->first();
                if ($defaultRole) {
                    $user->assignRole($defaultRole);
                }
            }
        });
        
        // Clean up roles when user is deleted
        static::deleting(function ($user) {
            $user->syncRoles([]);
            $user->syncPermissions([]);
        });
    }
}