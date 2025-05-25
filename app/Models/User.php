<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\ImageableTrait;

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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
    ];
    
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