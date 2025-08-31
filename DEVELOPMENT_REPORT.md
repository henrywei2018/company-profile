# Development Phase Report
**Company Profile Application Development**

---

## Table of Contents

1. [Technology Stack & Framework Selection](#1-technology-stack--framework-selection)
2. [System Architecture Implementation](#2-system-architecture-implementation)
3. [Authentication & Authorization System](#3-authentication--authorization-system)
4. [Content Management System](#4-content-management-system)
5. [Client Management Features](#5-client-management-features)
6. [E-commerce Integration](#6-e-commerce-integration)
7. [Database Design & Implementation](#7-database-design--implementation)
8. [API Development](#8-api-development)
9. [Real-time Features](#9-real-time-features)
10. [Testing & Quality Assurance](#10-testing--quality-assurance)
11. [Security Implementation](#11-security-implementation)
12. [Performance Optimization](#12-performance-optimization)
13. [Development Workflow & Tools](#13-development-workflow--tools)
14. [Challenges & Solutions](#14-challenges--solutions)
15. [Conclusion](#15-conclusion)

---

## Executive Summary

This report documents the comprehensive development process of a Company Profile Application built using Laravel 12.0 framework. The application serves as a multi-functional platform combining company presentation, client management, e-commerce capabilities, and real-time communication features. The development phase involved implementing complex business logic, ensuring robust security measures, and creating scalable architecture to support future growth.

---

## 1. Technology Stack & Framework Selection

### 1.1 Backend Framework Selection

**Laravel 12.0 Framework (PHP 8.2+)**

The application was built using Laravel 12.0, the latest version of the Laravel framework, chosen for several technical advantages:

```php
// composer.json - Core Dependencies
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^12.0",
        "laravel/sanctum": "^4.1",
        "laravel/tinker": "^2.10.1",
        "laravel/reverb": "^1.5"
    }
}
```

**Rationale for Laravel 12.0:**
- **Modern PHP Features**: Utilizes PHP 8.2+ features including typed properties, constructor promotion, and improved performance
- **Built-in Security**: CSRF protection, SQL injection prevention, XSS protection out of the box
- **Eloquent ORM**: Advanced database relationships and query optimization
- **Artisan CLI**: Powerful command-line interface for development tasks
- **Service Container**: Dependency injection and IoC container for better code organization

### 1.2 Key PHP Packages Integration

**Authentication & Authorization:**
```php
"spatie/laravel-permission": "^6.17" // RBAC implementation
"laravel/sanctum": "^4.1"           // API authentication
```

**File Management:**
```php
"rahulhaque/laravel-filepond": "^12.1"  // Advanced file uploads
"intervention/image-laravel": "^1.5"     // Image processing
```

**PDF & Analytics:**
```php
"barryvdh/laravel-dompdf": "^3.1"       // PDF generation
"spatie/laravel-analytics": "^5.6"       // Google Analytics integration
```

### 1.3 Frontend Technology Stack

**CSS Framework:**
- **Tailwind CSS**: Utility-first CSS framework for rapid UI development
- **Alpine.js**: Lightweight JavaScript framework for reactive components
- **Blade Templating**: Laravel's built-in templating engine

**Build Tools:**
```json
// package.json - Frontend Dependencies
{
    "devDependencies": {
        "vite": "^5.0",
        "tailwindcss": "^3.4",
        "alpinejs": "^3.13"
    }
}
```

### 1.4 Database Technology

**SQLite (Development) / MySQL (Production)**
- SQLite for development environment simplicity
- MySQL/PostgreSQL ready for production deployment
- Laravel's database agnostic migrations ensure portability

### 1.5 Real-time Communication

**Laravel Reverb Integration:**
```php
// config/reverb.php - WebSocket Configuration
'default' => 'reverb',
'connections' => [
    'reverb' => [
        'driver' => 'reverb',
        'key' => env('REVERB_APP_KEY'),
        'secret' => env('REVERB_APP_SECRET'),
        'app_id' => env('REVERB_APP_ID'),
    ]
]
```

**Implementation Benefits:**
- Real-time chat system without external dependencies
- Native Laravel integration
- Scalable WebSocket connections
- Event broadcasting capabilities

---

## 2. System Architecture Implementation

### 2.1 Architectural Pattern Overview

The application follows a **layered architecture pattern** combining several design patterns for maintainability and scalability:

```
┌─────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                   │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────┐  │
│  │    Blade    │  │ Controllers │  │    Middleware   │  │
│  │  Templates  │  │   (MVC)     │  │   Pipeline      │  │
│  └─────────────┘  └─────────────┘  └─────────────────┘  │
└─────────────────────────────────────────────────────────┘
                            │
┌─────────────────────────────────────────────────────────┐
│                    SERVICE LAYER                        │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────┐  │
│  │   Business  │  │  File Mgmt  │  │  Notification   │  │
│  │   Logic     │  │  Services   │  │   Services      │  │
│  │  Services   │  │             │  │                 │  │
│  └─────────────┘  └─────────────┘  └─────────────────┘  │
└─────────────────────────────────────────────────────────┘
                            │
┌─────────────────────────────────────────────────────────┐
│                 REPOSITORY LAYER                        │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────┐  │
│  │ Repository  │  │    Models   │  │   Eloquent ORM  │  │
│  │ Interfaces  │  │   (Domain)  │  │   Relationships │  │
│  └─────────────┘  └─────────────┘  └─────────────────┘  │
└─────────────────────────────────────────────────────────┘
                            │
┌─────────────────────────────────────────────────────────┐
│                    DATA LAYER                           │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────┐  │
│  │  Database   │  │ File System │  │  External APIs  │  │
│  │  (SQLite)   │  │  Storage    │  │  (Analytics)    │  │
│  └─────────────┘  └─────────────┘  └─────────────────┘  │
└─────────────────────────────────────────────────────────┘
```

### 2.2 Service Layer Implementation

**Service Pattern for Business Logic Separation:**

```php
// Example: ProjectService.php
<?php
namespace App\Services;

use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Services\FileUploadService;

class ProjectService
{
    protected $projectRepository;
    protected $fileUploadService;
    
    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        FileUploadService $fileUploadService
    ) {
        $this->projectRepository = $projectRepository;
        $this->fileUploadService = $fileUploadService;
    }
    
    public function createProject(array $data, array $images = []): Project
    {
        // Business logic encapsulated in service
        $project = $this->projectRepository->create($data);
        $this->processProjectImages($project, $images);
        $this->processSeoData($project, $data);
        
        // Send notifications
        Notifications::send('project_created', $project);
        
        return $project;
    }
}
```

**Service Layer Benefits:**
- **Single Responsibility**: Each service handles specific domain logic
- **Testability**: Services can be unit tested independently
- **Reusability**: Business logic shared across controllers
- **Maintainability**: Changes isolated to specific service classes

### 2.3 Repository Pattern Implementation

**Data Access Layer Abstraction:**

```php
// Repository Interface
interface ProjectRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Project;
    public function create(array $data): Project;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}

// Concrete Implementation
class ProjectRepository implements ProjectRepositoryInterface
{
    public function all(): Collection
    {
        return Project::with('images', 'category')->latest()->get();
    }
    
    public function find(int $id): ?Project
    {
        return Project::with('images', 'milestones')->find($id);
    }
}
```

### 2.4 Middleware Pipeline Architecture

**Request Processing Pipeline:**

```php
// app/Http/Kernel.php - Middleware Stack
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\SecurityHeaders::class,
        \App\Http\Middleware\SeoMiddleware::class,
        \Laravel\Session\Middleware\StartSession::class,
        \App\Http\Middleware\ValidateFilePondUpload::class,
    ],
    'admin' => [
        'auth',
        \App\Http\Middleware\AdminMiddleware::class,
        \App\Http\Middleware\RequireRole::class,
    ],
    'client' => [
        'auth',
        \App\Http\Middleware\ClientMiddleware::class,
        \App\Http\Middleware\EnsureClientIsVerified::class,
    ]
];
```

**Custom Middleware Examples:**

```php
// Role-Based Access Control Middleware
class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            return redirect('/login')->with('error', 'Admin access required');
        }
        
        return $next($request);
    }
}

// Security Headers Middleware
class SecurityHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        return $response;
    }
}
```

### 2.5 Event-Driven Architecture

**Observer Pattern for Model Events:**

```php
// app/Observers/ProjectObserver.php
class ProjectObserver
{
    public function created(Project $project)
    {
        // Auto-generate SEO metadata
        $this->generateSeoData($project);
        
        // Send notification to project stakeholders
        Notifications::send('project_created', $project);
        
        // Create initial project milestone
        $this->createInitialMilestone($project);
    }
    
    public function updated(Project $project)
    {
        if ($project->wasChanged('status')) {
            event(new ProjectStatusChanged($project));
        }
    }
}
```

### 2.6 Dependency Injection Container

**Service Provider Registration:**

```php
// app/Providers/RepositoryServiceProvider.php
public function register()
{
    $this->app->bind(
        ProjectRepositoryInterface::class,
        ProjectRepository::class
    );
    
    $this->app->bind(
        UserRepositoryInterface::class,
        UserRepository::class
    );
}
```

**Benefits of Architecture:**
- **Separation of Concerns**: Clear boundaries between layers
- **Testability**: Each layer can be tested independently
- **Maintainability**: Changes confined to specific architectural layers
- **Scalability**: Easy to extend and modify without affecting other components
- **Code Reusability**: Services and repositories reusable across the application

---

## 3. Authentication & Authorization System

### 3.1 Multi-Layer Authentication Implementation

**Laravel Breeze Integration with Custom Extensions:**

The authentication system combines Laravel Breeze's foundation with custom multi-role functionality:

```php
// app/Models/User.php - User Model with Authentication Traits
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, ImageableTrait;
    
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'company',
        'otp_code', 'otp_expires_at', 'is_active',
        'email_notifications', 'project_update_notifications'
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'otp_expires_at' => 'datetime',
        'settings' => 'array'
    ];
}
```

### 3.2 OTP-Based Email Verification System

**Custom OTP Implementation:**

```php
// app/Http/Controllers/Auth/OtpVerificationController.php
class OtpVerificationController extends Controller
{
    public function verify(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $otpCode = $request->input('otp_code');
        
        // Validate OTP
        if ($user->otp_code !== $otpCode) {
            return back()->withErrors(['otp_code' => 'Invalid OTP code']);
        }
        
        if ($user->otp_expires_at < now()) {
            return back()->withErrors(['otp_code' => 'OTP code has expired']);
        }
        
        // Mark email as verified
        $user->markEmailAsVerified();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();
        
        return redirect()->route('dashboard')->with('success', 'Email verified successfully');
    }
    
    public function resend(): RedirectResponse
    {
        $user = Auth::user();
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $user->update([
            'otp_code' => $otpCode,
            'otp_expires_at' => now()->addMinutes(10)
        ]);
        
        Mail::to($user)->send(new OtpVerificationMail($otpCode));
        
        return back()->with('success', 'New OTP code sent to your email');
    }
}
```

### 3.3 Role-Based Access Control (RBAC)

**Spatie Laravel-Permission Integration:**

```php
// database/seeders/RolePermissionSeeder.php
class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        $permissions = [
            'view users', 'create users', 'edit users', 'delete users',
            'view projects', 'create projects', 'edit projects', 'delete projects',
            'view quotations', 'create quotations', 'edit quotations', 'delete quotations',
            'view messages', 'create messages', 'edit messages', 'delete messages',
            'manage settings', 'view analytics', 'export data'
        ];
        
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        
        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());
        
        $clientRole = Role::create(['name' => 'client']);
        $clientRole->givePermissionTo([
            'view projects', 'create quotations', 'view quotations',
            'create messages', 'view messages'
        ]);
    }
}
```

### 3.4 Middleware-Based Authorization

**Custom Authorization Middleware:**

```php
// app/Http/Middleware/RequireRole.php
class RequireRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }
        
        $user = auth()->user();
        
        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }
        
        // Log unauthorized access attempt
        Log::warning('Unauthorized access attempt', [
            'user_id' => $user->id,
            'email' => $user->email,
            'required_roles' => $roles,
            'user_roles' => $user->getRoleNames(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        abort(403, 'Insufficient permissions');
    }
}
```

### 3.5 Session Management & Security

**Enhanced Session Security:**

```php
// config/session.php - Session Configuration
'lifetime' => env('SESSION_LIFETIME', 120),
'expire_on_close' => true,
'encrypt' => true,
'secure' => env('SESSION_SECURE_COOKIE', true),
'http_only' => true,
'same_site' => 'lax'
```

**Login Attempt Tracking:**

```php
// app/Models/User.php - Login tracking methods
public function recordSuccessfulLogin()
{
    $this->increment('login_count');
    $this->update([
        'last_login_at' => now(),
        'failed_login_attempts' => 0,
        'locked_at' => null
    ]);
}

public function recordFailedLogin()
{
    $this->increment('failed_login_attempts');
    
    if ($this->failed_login_attempts >= 5) {
        $this->update(['locked_at' => now()->addMinutes(30)]);
    }
}
```

### 3.6 API Authentication with Sanctum

**API Token Management:**

```php
// app/Http/Controllers/Api/AuthController.php
public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);
    
    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
    
    $user = Auth::user();
    $token = $user->createToken('api-token')->plainTextToken;
    
    return response()->json([
        'user' => $user,
        'token' => $token,
        'token_type' => 'Bearer'
    ]);
}
```

**Authentication Benefits Achieved:**
- **Multi-Factor Authentication**: OTP-based email verification
- **Role-Based Security**: Granular permission system
- **Session Security**: Encrypted sessions with automatic expiration
- **API Security**: Token-based authentication for API endpoints
- **Brute Force Protection**: Login attempt limiting and account locking
- **Audit Trail**: Comprehensive logging of authentication events

---

## 4. Content Management System

### 4.1 Dynamic Content Architecture

**Multi-Entity Content Management:**

The CMS manages multiple content types through a unified interface with shared traits and behaviors:

```php
// app/Models/Post.php - Blog Post Model
class Post extends Model
{
    use HasFactory, SeoableTrait;
    
    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'user_id',
        'featured_image', 'status', 'published_at', 'featured'
    ];
    
    protected $casts = [
        'published_at' => 'datetime',
        'featured' => 'boolean',
    ];
    
    protected $appends = [
        'featured_image_url',
        'reading_time',
        'excerpt_or_content'
    ];
    
    // Auto-generate slug on save
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($post) {
            if (!$post->slug) {
                $post->slug = Str::slug($post->title);
            }
        });
    }
}

// app/Models/Service.php - Service Model with Traits
class Service extends Model
{
    use HasFactory, FilterableTrait, HasActiveTrait, 
        HasSlugTrait, HasSortOrderTrait, SeoableTrait, ImageableTrait;
    
    protected $fillable = [
        'title', 'slug', 'category_id', 'short_description',
        'description', 'icon', 'image', 'featured', 'is_active', 'sort_order'
    ];
}
```

### 4.2 Trait-Based Feature Implementation

**Reusable Model Behaviors:**

```php
// app/Traits/SeoableTrait.php
trait SeoableTrait
{
    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }
    
    public function getSeoTitleAttribute()
    {
        return $this->seo?->meta_title ?? $this->title;
    }
    
    public function getSeoDescriptionAttribute()
    {
        return $this->seo?->meta_description ?? 
               Str::limit(strip_tags($this->content ?? $this->description), 155);
    }
    
    public function createOrUpdateSeo(array $seoData)
    {
        if ($this->seo) {
            $this->seo->update($seoData);
        } else {
            $this->seo()->create($seoData);
        }
    }
}

// app/Traits/ImageableTrait.php
trait ImageableTrait
{
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }
    
    public function uploadImage(UploadedFile $file, string $directory = 'images')
    {
        // Generate unique filename
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        
        // Store file
        $path = $file->storeAs($directory, $filename, 'public');
        
        // Update model
        $this->update(['image' => $path]);
        
        return $path;
    }
}
```

### 4.3 Advanced Content Controller Implementation

**Service-Driven Controller Logic:**

```php
// app/Http/Controllers/Admin/PostController.php
class PostController extends Controller
{
    protected FileUploadService $fileUploadService;
    
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }
    
    public function index(Request $request)
    {
        $posts = Post::with(['author', 'categories'])
            ->when($request->filled('category'), function ($query) use ($request) {
                return $query->byCategory($request->category);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->byStatus($request->status);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where('title', 'like', "%{$request->search}%")
                           ->orWhere('content', 'like', "%{$request->search}%");
            })
            ->latest()
            ->paginate(15);
            
        return view('admin.posts.index', compact('posts'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published,scheduled',
            'published_at' => 'nullable|date',
            'categories' => 'array',
            'categories.*' => 'exists:post_categories,id'
        ]);
        
        DB::beginTransaction();
        try {
            // Create post
            $post = Post::create([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'excerpt' => $validated['excerpt'],
                'user_id' => Auth::id(),
                'status' => $validated['status'],
                'published_at' => $validated['published_at'] ?? now(),
            ]);
            
            // Handle featured image upload
            if ($request->hasFile('featured_image')) {
                $imagePath = $this->fileUploadService->uploadImage(
                    $request->file('featured_image'), 
                    'posts'
                );
                $post->update(['featured_image' => $imagePath]);
            }
            
            // Attach categories
            if (!empty($validated['categories'])) {
                $post->categories()->attach($validated['categories']);
            }
            
            // Handle SEO data
            if ($request->filled(['seo_title', 'seo_description', 'seo_keywords'])) {
                $post->createOrUpdateSeo([
                    'meta_title' => $request->seo_title,
                    'meta_description' => $request->seo_description,
                    'meta_keywords' => $request->seo_keywords,
                ]);
            }
            
            DB::commit();
            
            // Send notification
            Notifications::send('post_created', $post);
            
            return redirect()->route('admin.posts.index')
                           ->with('success', 'Post created successfully');
                           
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Post creation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create post']);
        }
    }
}
```

### 4.4 File Upload Service Implementation

**Centralized File Management:**

```php
// app/Services/FileUploadService.php
class FileUploadService
{
    public function uploadImage(UploadedFile $file, string $directory = 'uploads'): string
    {
        // Validate file
        $this->validateImage($file);
        
        // Generate unique filename
        $filename = $this->generateUniqueFilename($file);
        
        // Create directory if not exists
        $fullPath = "public/{$directory}";
        Storage::makeDirectory($fullPath);
        
        // Store original file
        $originalPath = $file->storeAs($fullPath, $filename);
        
        // Process image (resize, optimize)
        $this->processImage($originalPath);
        
        // Return public path
        return str_replace('public/', '', $originalPath);
    }
    
    protected function validateImage(UploadedFile $file): void
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            throw new InvalidArgumentException('Invalid file type');
        }
        
        if ($file->getSize() > $maxSize) {
            throw new InvalidArgumentException('File size too large');
        }
    }
    
    protected function processImage(string $path): void
    {
        $fullPath = Storage::path($path);
        
        // Create different sizes
        $image = Image::make($fullPath);
        
        // Create thumbnail (300x200)
        $thumbnailPath = str_replace('.', '_thumb.', $fullPath);
        $image->fit(300, 200)->save($thumbnailPath);
        
        // Optimize original (max 1200px width)
        if ($image->width() > 1200) {
            $image->resize(1200, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($fullPath, 85); // 85% quality
        }
    }
}
```

### 4.5 Category Management System

**Hierarchical Category Structure:**

```php
// app/Models/PostCategory.php
class PostCategory extends Model
{
    use HasFactory, HasSlugTrait, SeoableTrait;
    
    protected $fillable = ['name', 'slug', 'description', 'parent_id', 'is_active'];
    
    // Self-referential relationships
    public function parent()
    {
        return $this->belongsTo(PostCategory::class, 'parent_id');
    }
    
    public function children()
    {
        return $this->hasMany(PostCategory::class, 'parent_id');
    }
    
    // Posts relationship
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_category_post');
    }
    
    // Scope for active categories
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    // Get category breadcrumb
    public function getBreadcrumbAttribute(): array
    {
        $breadcrumb = [$this];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($breadcrumb, $parent);
            $parent = $parent->parent;
        }
        
        return $breadcrumb;
    }
}
```

### 4.6 SEO Integration

**Automatic SEO Generation:**

```php
// app/Observers/PostObserver.php
class PostObserver
{
    public function created(Post $post)
    {
        // Auto-generate SEO if not provided
        if (!$post->seo) {
            $post->createOrUpdateSeo([
                'meta_title' => $post->title,
                'meta_description' => Str::limit(strip_tags($post->content), 155),
                'meta_keywords' => $this->extractKeywords($post->content),
                'og_title' => $post->title,
                'og_description' => $post->excerpt ?? Str::limit(strip_tags($post->content), 100),
                'og_image' => $post->featured_image_url,
            ]);
        }
        
        // Generate sitemap entry
        $this->updateSitemap();
    }
    
    protected function extractKeywords(string $content): string
    {
        // Simple keyword extraction from content
        $words = str_word_count(strip_tags($content), 1);
        $commonWords = ['the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of'];
        $keywords = array_filter($words, function($word) use ($commonWords) {
            return strlen($word) > 4 && !in_array(strtolower($word), $commonWords);
        });
        
        return implode(', ', array_slice(array_unique($keywords), 0, 10));
    }
}
```

**CMS Benefits Achieved:**
- **Flexible Content Types**: Unified management for posts, services, projects
- **SEO Optimization**: Automatic meta tag generation and sitemap updates
- **Media Management**: Intelligent image processing and optimization
- **Category Hierarchy**: Multi-level categorization with breadcrumbs
- **Draft System**: Content workflow with draft, published, scheduled states
- **Bulk Operations**: Mass content management capabilities

---

## 5. Client Management Features

### 5.1 Client Dashboard Architecture

**Service-Driven Dashboard Implementation:**

```php
// app/Http/Controllers/Client/DashboardController.php
class DashboardController extends Controller
{
    protected DashboardService $dashboardService;
    protected ClientAccessService $clientAccessService;

    public function __construct(
        DashboardService $dashboardService,
        ClientAccessService $clientAccessService
    ) {
        $this->dashboardService = $dashboardService;
        $this->clientAccessService = $clientAccessService;
    }

    public function index()
    {
        $user = Auth::user();
        
        // Ensure client has required profile completion
        if (!$this->clientAccessService->isProfileComplete($user)) {
            return redirect()->route('client.profile.completion')
                           ->with('warning', 'Please complete your profile');
        }
        
        // Get dashboard data
        $dashboardData = $this->dashboardService->getClientDashboardData($user);
        
        return view('client.dashboard', compact('dashboardData'));
    }
}

// app/Services/DashboardService.php - Client Dashboard Data
class DashboardService
{
    public function getClientDashboardData(User $client): array
    {
        return [
            'stats' => [
                'active_projects' => $client->projects()->active()->count(),
                'pending_quotations' => $client->quotations()->pending()->count(),
                'total_orders' => $client->productOrders()->count(),
                'unread_messages' => $client->unreadMessages()->count(),
            ],
            'recent_projects' => $client->projects()
                ->with(['category', 'milestones'])
                ->latest()
                ->limit(5)
                ->get(),
            'recent_quotations' => $client->quotations()
                ->with(['service'])
                ->latest()
                ->limit(5)
                ->get(),
            'pending_notifications' => $client->unreadNotifications()
                ->limit(10)
                ->get(),
            'activity_feed' => $this->getClientActivityFeed($client),
        ];
    }
    
    protected function getClientActivityFeed(User $client): array
    {
        $activities = collect();
        
        // Recent project updates
        $projectUpdates = $client->projects()
            ->with(['updates'])
            ->get()
            ->flatMap(function ($project) {
                return $project->updates->map(function ($update) use ($project) {
                    return [
                        'type' => 'project_update',
                        'title' => "Project Update: {$project->name}",
                        'description' => $update->description,
                        'date' => $update->created_at,
                        'icon' => 'project',
                        'url' => route('client.projects.show', $project)
                    ];
                });
            });
        
        // Quotation status changes
        $quotationUpdates = $client->quotations()
            ->whereDate('updated_at', '>=', now()->subDays(30))
            ->get()
            ->map(function ($quotation) {
                return [
                    'type' => 'quotation_update',
                    'title' => "Quotation {$quotation->quotation_number}",
                    'description' => "Status changed to {$quotation->status}",
                    'date' => $quotation->updated_at,
                    'icon' => 'quote',
                    'url' => route('client.quotations.show', $quotation)
                ];
            });
        
        return $activities->merge($projectUpdates)
                         ->merge($quotationUpdates)
                         ->sortByDesc('date')
                         ->take(10)
                         ->values()
                         ->toArray();
    }
}
```

### 5.2 Quotation Request System

**Complex Quotation Management:**

```php
// app/Models/Quotation.php - Advanced Quotation Model
class Quotation extends Model
{
    use HasFactory, FilterableTrait, QuotationProjectConversion;

    protected $fillable = [
        'quotation_number', 'name', 'email', 'phone', 'company',
        'service_id', 'project_type', 'location', 'requirements',
        'budget_range', 'estimated_cost', 'estimated_timeline',
        'start_date', 'status', 'priority', 'source', 'client_id',
        'admin_notes', 'internal_notes', 'additional_info'
    ];

    protected $casts = [
        'start_date' => 'date',
        'estimated_timeline' => 'integer',
        'estimated_cost' => 'decimal:2'
    ];

    // Automatic quotation number generation
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($quotation) {
            if (!$quotation->quotation_number) {
                $quotation->quotation_number = static::generateQuotationNumber();
            }
        });
    }

    protected static function generateQuotationNumber(): string
    {
        $prefix = 'QUO';
        $year = date('Y');
        $month = date('m');
        
        $lastQuotation = static::whereYear('created_at', $year)
                              ->whereMonth('created_at', $month)
                              ->latest()
                              ->first();
        
        $sequence = $lastQuotation ? 
            (int) substr($lastQuotation->quotation_number, -4) + 1 : 1;
        
        return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // Status management methods
    public function approve(string $adminNotes = null): bool
    {
        $this->status = 'approved';
        $this->admin_notes = $adminNotes;
        $this->save();
        
        // Send approval notification
        Notifications::send('quotation_approved', $this);
        
        return true;
    }

    public function convertToProject(): Project
    {
        return $this->createProjectFromQuotation([
            'name' => "Project from {$this->quotation_number}",
            'description' => $this->requirements,
            'client_id' => $this->client_id,
            'estimated_cost' => $this->estimated_cost,
            'start_date' => $this->start_date,
        ]);
    }
}

// app/Services/QuotationService.php
class QuotationService
{
    public function createQuotation(array $data): Quotation
    {
        // Validate service availability
        $service = Service::findOrFail($data['service_id']);
        
        // Create quotation
        $quotation = Quotation::create($data);
        
        // Attach files if any
        if (!empty($data['attachments'])) {
            $this->processAttachments($quotation, $data['attachments']);
        }
        
        // Send notification to admin
        Notifications::send('quotation_created', $quotation);
        
        // Auto-assign priority based on budget
        $this->calculatePriority($quotation);
        
        return $quotation;
    }
    
    public function processAttachments(Quotation $quotation, array $files): void
    {
        foreach ($files as $file) {
            if ($file->isValid()) {
                $path = $file->store('quotations/' . $quotation->id, 'public');
                
                $quotation->attachments()->create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }
    }
    
    protected function calculatePriority(Quotation $quotation): void
    {
        $budgetRanges = [
            'under_1000' => 'low',
            '1000_5000' => 'medium',
            '5000_15000' => 'high',
            'over_15000' => 'urgent'
        ];
        
        $priority = $budgetRanges[$quotation->budget_range] ?? 'medium';
        $quotation->update(['priority' => $priority]);
    }
}
```

### 5.3 Project Tracking System

**Client Project Management:**

```php
// app/Models/Project.php - Client Project Tracking
class Project extends Model
{
    use HasFactory, SeoableTrait, FilterableTrait;

    protected $fillable = [
        'name', 'slug', 'description', 'client_id', 'category_id',
        'status', 'priority', 'start_date', 'end_date', 'deadline',
        'budget', 'estimated_cost', 'actual_cost', 'completion_percentage'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'deadline' => 'date',
        'budget' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'completion_percentage' => 'integer'
    ];

    // Project status management
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'end_date' => now(),
            'completion_percentage' => 100
        ]);
        
        // Send completion notification
        Notifications::send('project_completed', $this);
        
        // Generate completion certificate
        $this->generateCompletionCertificate();
    }

    // Project milestones
    public function milestones()
    {
        return $this->hasMany(ProjectMilestone::class)->orderBy('due_date');
    }

    // Calculate project progress
    public function getProgressAttribute(): array
    {
        $milestones = $this->milestones;
        $total = $milestones->count();
        $completed = $milestones->where('status', 'completed')->count();
        
        return [
            'total_milestones' => $total,
            'completed_milestones' => $completed,
            'percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
            'is_on_track' => $this->isOnTrack(),
            'days_remaining' => $this->deadline ? 
                now()->diffInDays($this->deadline, false) : null
        ];
    }

    protected function isOnTrack(): bool
    {
        if (!$this->deadline) return true;
        
        $totalDays = $this->start_date->diffInDays($this->deadline);
        $daysPassed = $this->start_date->diffInDays(now());
        $expectedProgress = $totalDays > 0 ? ($daysPassed / $totalDays) * 100 : 0;
        
        return $this->completion_percentage >= $expectedProgress;
    }
}

// app/Http/Controllers/Client/ProjectController.php
class ProjectController extends Controller
{
    public function show(Project $project)
    {
        // Ensure client can access this project
        $this->authorize('view', $project);
        
        $project->load([
            'milestones.files',
            'updates' => fn($q) => $q->latest()->limit(10),
            'files',
            'category'
        ]);
        
        $projectData = [
            'project' => $project,
            'progress' => $project->progress,
            'timeline' => $this->getProjectTimeline($project),
            'financial_summary' => $this->getFinancialSummary($project),
            'team_members' => $project->assignedMembers,
        ];
        
        return view('client.projects.show', compact('projectData'));
    }
    
    protected function getProjectTimeline(Project $project): array
    {
        $timeline = collect();
        
        // Project start
        $timeline->push([
            'type' => 'project_start',
            'title' => 'Project Started',
            'date' => $project->start_date,
            'icon' => 'play',
            'status' => 'completed'
        ]);
        
        // Milestones
        foreach ($project->milestones as $milestone) {
            $timeline->push([
                'type' => 'milestone',
                'title' => $milestone->name,
                'description' => $milestone->description,
                'date' => $milestone->due_date,
                'icon' => 'flag',
                'status' => $milestone->status,
                'completion_date' => $milestone->completed_at
            ]);
        }
        
        // Project deadline
        if ($project->deadline) {
            $timeline->push([
                'type' => 'deadline',
                'title' => 'Project Deadline',
                'date' => $project->deadline,
                'icon' => 'calendar',
                'status' => $project->status === 'completed' ? 'completed' : 'pending'
            ]);
        }
        
        return $timeline->sortBy('date')->values()->toArray();
    }
}
```

### 5.4 Real-time Communication System

**Client-Admin Messaging:**

```php
// app/Models/Message.php
class Message extends Model
{
    use HasFactory, MessageTrait;

    protected $fillable = [
        'subject', 'content', 'sender_id', 'recipient_id',
        'project_id', 'quotation_id', 'order_id', 'status',
        'priority', 'is_read', 'read_at', 'reply_to'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    // Message threading
    public function replies()
    {
        return $this->hasMany(Message::class, 'reply_to')->latest();
    }

    public function thread()
    {
        return $this->belongsTo(Message::class, 'reply_to');
    }

    // Auto-mark as read when accessed
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }
}

// app/Http/Controllers/Client/MessageController.php
class MessageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'in:low,medium,high,urgent',
            'project_id' => 'nullable|exists:projects,id',
            'attachments' => 'array|max:5',
            'attachments.*' => 'file|max:10240' // 10MB max
        ]);

        DB::beginTransaction();
        try {
            // Create message
            $message = Message::create([
                'subject' => $validated['subject'],
                'content' => $validated['content'],
                'sender_id' => auth()->id(),
                'recipient_id' => $this->getAdminRecipient(),
                'project_id' => $validated['project_id'] ?? null,
                'priority' => $validated['priority'] ?? 'medium',
                'status' => 'sent'
            ]);

            // Process attachments
            if (!empty($validated['attachments'])) {
                foreach ($validated['attachments'] as $file) {
                    $path = $file->store('messages', 'private');
                    
                    $message->attachments()->create([
                        'filename' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType()
                    ]);
                }
            }

            DB::commit();

            // Send notification
            Notifications::send('message_created', $message);

            return redirect()->route('client.messages.show', $message)
                           ->with('success', 'Message sent successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to send message']);
        }
    }
}
```

**Client Management Benefits Achieved:**
- **Personalized Dashboard**: Real-time project and quotation tracking
- **Quotation Automation**: Auto-numbering, priority calculation, workflow management  
- **Project Transparency**: Milestone tracking, progress visualization, timeline management
- **Direct Communication**: Threaded messaging with file attachments
- **Activity Tracking**: Complete audit trail of client interactions
- **Profile Management**: Comprehensive client profile completion system

---

## 6. E-commerce Integration

### 6.1 Product Management System

**Multi-Variant Product Architecture:**

```php
// app/Models/Product.php
class Product extends Model
{
    use HasFactory, HasActiveTrait, HasSlugTrait, HasSortOrderTrait, 
        SeoableTrait, FilterableTrait;

    protected $fillable = [
        'name', 'slug', 'sku', 'short_description', 'description',
        'product_category_id', 'service_id', 'brand', 'price',
        'sale_price', 'cost_price', 'stock_quantity', 'min_stock',
        'weight', 'dimensions', 'featured', 'is_active', 'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'dimensions' => 'json',
        'featured' => 'boolean'
    ];

    // Product images relationship
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    // Get effective price (sale price if available, otherwise regular price)
    public function getEffectivePriceAttribute(): float
    {
        return $this->sale_price && $this->sale_price < $this->price 
            ? $this->sale_price 
            : $this->price;
    }

    // Check if product is on sale
    public function getOnSaleAttribute(): bool
    {
        return $this->sale_price && $this->sale_price < $this->price;
    }

    // Calculate discount percentage
    public function getDiscountPercentageAttribute(): int
    {
        if (!$this->on_sale) return 0;
        
        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    // Stock management
    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock;
    }

    public function decreaseStock(int $quantity): void
    {
        if ($this->stock_quantity >= $quantity) {
            $this->decrement('stock_quantity', $quantity);
            
            // Check for low stock notification
            if ($this->isLowStock()) {
                Notifications::send('product_low_stock', $this);
            }
        } else {
            throw new InsufficientStockException("Not enough stock for product: {$this->name}");
        }
    }
}
```

### 6.2 Advanced Shopping Cart System

**Session-Based Cart with Database Persistence:**

```php
// app/Models/CartItem.php
class CartItem extends Model
{
    protected $fillable = [
        'session_id', 'user_id', 'product_id', 'quantity', 
        'unit_price', 'total_price', 'expires_at'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'expires_at' => 'datetime'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Auto-calculate total when quantity changes
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($cartItem) {
            $cartItem->total_price = $cartItem->quantity * $cartItem->unit_price;
        });
    }
}

// app/Http/Controllers/Client/CartController.php
class CartController extends Controller
{
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        $product = Product::findOrFail($validated['product_id']);
        
        // Check stock availability
        if (!$product->isInStock() || $product->stock_quantity < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available'
            ], 400);
        }

        // Add to cart
        $cartItem = $this->addOrUpdateCartItem($product, $validated['quantity']);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'cart_count' => $this->getCartItemCount(),
            'cart_total' => $this->getCartTotal()
        ]);
    }

    protected function addOrUpdateCartItem(Product $product, int $quantity): CartItem
    {
        $sessionId = session()->getId();
        $userId = auth()->id();

        $cartItem = CartItem::where('session_id', $sessionId)
                           ->where('product_id', $product->id)
                           ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            
            // Check total quantity doesn't exceed stock
            if ($newQuantity > $product->stock_quantity) {
                throw new InsufficientStockException('Cannot add more items than available stock');
            }
            
            $cartItem->update([
                'quantity' => $newQuantity,
                'unit_price' => $product->effective_price,
                'expires_at' => now()->addDays(7)
            ]);
        } else {
            $cartItem = CartItem::create([
                'session_id' => $sessionId,
                'user_id' => $userId,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->effective_price,
                'expires_at' => now()->addDays(7)
            ]);
        }

        return $cartItem;
    }
}
```

### 6.3 Order Processing System

**Complex Order Management with Negotiation:**

```php
// app/Models/ProductOrder.php
class ProductOrder extends Model
{
    protected $fillable = [
        'order_number', 'client_id', 'client_name', 'client_email', 'client_phone',
        'status', 'payment_status', 'payment_method', 'payment_proof',
        'total_amount', 'delivery_address', 'needed_date', 'notes',
        'needs_negotiation', 'negotiation_message', 'requested_total',
        'negotiation_status', 'admin_notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'requested_total' => 'decimal:2',
        'needed_date' => 'date',
        'needs_negotiation' => 'boolean',
        'negotiation_requested_at' => 'datetime',
        'payment_uploaded_at' => 'datetime'
    ];

    // Order items relationship
    public function items()
    {
        return $this->hasMany(ProductOrderItem::class);
    }

    // Auto-generate order number
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    protected static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $year = date('Y');
        $month = date('m');
        
        $lastOrder = static::whereYear('created_at', $year)
                          ->whereMonth('created_at', $month)
                          ->latest()
                          ->first();
        
        $sequence = $lastOrder ? 
            (int) substr($lastOrder->order_number, -4) + 1 : 1;
        
        return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // Order status management
    public function confirmOrder(): void
    {
        $this->update(['status' => 'confirmed']);
        
        // Reserve stock
        foreach ($this->items as $item) {
            $item->product->decreaseStock($item->quantity);
        }
        
        // Send confirmation notification
        Notifications::send('order_confirmed', $this);
    }

    // Payment processing methods  
    public function hasPaymentProof(): bool
    {
        return !empty($this->payment_proof);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function verifyPayment(string $adminNotes = null): void
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_verified_at' => now(),
            'admin_notes' => $adminNotes
        ]);
        
        // Auto-confirm order after payment verification
        if ($this->status === 'pending') {
            $this->confirmOrder();
        }
        
        Notifications::send('payment_verified', $this);
    }

    // Price negotiation methods
    public function requestNegotiation(float $requestedTotal, string $message): void
    {
        $this->update([
            'needs_negotiation' => true,
            'requested_total' => $requestedTotal,
            'negotiation_message' => $message,
            'negotiation_status' => 'pending',
            'negotiation_requested_at' => now()
        ]);
        
        Notifications::send('negotiation_requested', $this);
    }

    public function approveNegotiation(string $adminNotes = null): void
    {
        $this->update([
            'total_amount' => $this->requested_total,
            'negotiation_status' => 'approved',
            'negotiation_responded_at' => now(),
            'admin_notes' => $adminNotes
        ]);
        
        // Recalculate item prices proportionally
        $this->recalculateItemPrices();
        
        Notifications::send('negotiation_approved', $this);
    }

    protected function recalculateItemPrices(): void
    {
        $originalTotal = $this->items->sum(fn($item) => $item->original_price * $item->quantity);
        $adjustmentRatio = $this->total_amount / $originalTotal;
        
        foreach ($this->items as $item) {
            $newPrice = $item->original_price * $adjustmentRatio;
            $item->update([
                'unit_price' => $newPrice,
                'total_price' => $newPrice * $item->quantity
            ]);
        }
    }
}

// app/Services/ProductOrderService.php
class ProductOrderService
{
    public function createOrderFromCart(array $orderData): ProductOrder
    {
        $cartItems = $this->getCartItems();
        
        if ($cartItems->isEmpty()) {
            throw new EmptyCartException('Cannot create order from empty cart');
        }

        DB::beginTransaction();
        try {
            // Create order
            $order = ProductOrder::create([
                'client_id' => auth()->id(),
                'client_name' => $orderData['client_name'],
                'client_email' => $orderData['client_email'],
                'client_phone' => $orderData['client_phone'],
                'delivery_address' => $orderData['delivery_address'],
                'needed_date' => $orderData['needed_date'],
                'notes' => $orderData['notes'] ?? null,
                'status' => 'pending',
                'payment_status' => 'pending',
                'total_amount' => $cartItems->sum('total_price')
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                $order->items()->create([
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_sku' => $cartItem->product->sku,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'total_price' => $cartItem->total_price,
                    'original_price' => $cartItem->product->price
                ]);
            }

            // Clear cart
            $this->clearCart();

            DB::commit();

            // Send order notification
            Notifications::send('order_created', $order);

            return $order;

        } catch (\Exception $e) {
            DB::rollback();
            throw new OrderCreationException('Failed to create order: ' . $e->getMessage());
        }
    }
}
```

### 6.4 Payment Processing Integration

**Multi-Method Payment System:**

```php
// app/Models/PaymentMethod.php
class PaymentMethod extends Model
{
    protected $fillable = [
        'name', 'type', 'account_name', 'account_number',
        'instructions', 'is_active', 'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'instructions' => 'array'
    ];

    // Get formatted account info
    public function getFormattedAccountAttribute(): string
    {
        return "{$this->account_name} - {$this->account_number}";
    }
}

// app/Http/Controllers/Client/OrderController.php - Payment Processing
class OrderController extends Controller
{
    public function uploadPaymentProof(Request $request, ProductOrder $order)
    {
        $this->authorize('update', $order);

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            'payment_notes' => 'nullable|string|max:1000'
        ]);

        try {
            // Upload payment proof
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $filename = 'payment_' . $order->order_number . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('payments', $filename, 'private');

                $order->update([
                    'payment_proof' => $path,
                    'payment_notes' => $request->payment_notes,
                    'payment_uploaded_at' => now(),
                    'payment_status' => 'pending_verification'
                ]);

                // Notify admin of payment upload
                Notifications::send('payment_proof_uploaded', $order);

                return redirect()->route('client.orders.show', $order)
                               ->with('success', 'Payment proof uploaded successfully');
            }

        } catch (\Exception $e) {
            Log::error('Payment proof upload failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => 'Failed to upload payment proof']);
        }
    }
}
```

**E-commerce Integration Benefits Achieved:**
- **Product Variants**: Multi-image, multi-category product management
- **Smart Cart**: Session-based cart with stock validation and persistence
- **Order Workflow**: Complete order lifecycle from cart to delivery
- **Payment Integration**: Multiple payment methods with proof verification
- **Price Negotiation**: Built-in negotiation system for custom pricing
- **Stock Management**: Real-time inventory tracking with low-stock alerts

---

## 7. Database Design & Implementation

### 7.1 Migration-Driven Database Evolution

**Comprehensive Migration Strategy:**

The database design follows Laravel's migration-driven approach with **58 migration files** ensuring structured database evolution:

```php
// database/migrations/0001_01_01_000000_create_users_table.php
public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();
        
        // Extended user profile fields
        $table->string('phone')->nullable();
        $table->string('company')->nullable();
        $table->text('address')->nullable();
        $table->string('city')->nullable();
        $table->string('state')->nullable();
        $table->string('postal_code')->nullable();
        $table->string('country')->nullable();
        $table->string('avatar')->nullable();
        $table->boolean('is_active')->default(true);
        $table->json('settings')->nullable();
        
        $table->timestamps();
        
        // Performance indexes
        $table->index(['email', 'is_active']);
        $table->index(['created_at']);
    });
}
```

### 7.2 Complex Relational Design

**Advanced Foreign Key Relationships:**

```php
// database/migrations/2025_05_11_162216_create_quotations_table.php
public function up(): void
{
    Schema::create('quotations', function (Blueprint $table) {
        $table->id();
        $table->string('quotation_number')->unique()->nullable();
        $table->string('name');
        $table->string('email');
        $table->string('phone')->nullable();
        $table->string('company')->nullable();
        
        // Foreign key relationships
        $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();
        
        // Business logic fields
        $table->string('project_type')->nullable();
        $table->string('location')->nullable();
        $table->text('requirements')->nullable();
        $table->string('budget_range')->nullable();
        $table->decimal('estimated_cost', 15, 2)->nullable();
        $table->integer('estimated_timeline')->nullable();
        $table->date('start_date')->nullable();
        
        // Status management
        $table->enum('status', ['pending', 'reviewed', 'approved', 'rejected'])->default('pending');
        $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
        $table->string('source')->nullable(); // tracking channel
        
        // Workflow timestamps
        $table->timestamp('reviewed_at')->nullable();
        $table->timestamp('approved_at')->nullable();
        $table->timestamp('last_communication_at')->nullable();
        $table->boolean('project_created')->default(false);
        $table->timestamp('project_created_at')->nullable();
        
        $table->timestamps();
        
        // Strategic indexes for query performance
        $table->index(['status', 'created_at']);
        $table->index(['client_id', 'status']);
        $table->index(['service_id', 'status']);
        $table->index(['priority']);
        $table->index(['project_created']);
    });
}
```

### 7.3 E-commerce Database Architecture

**Complex Order Management Schema:**

```php
// database/migrations/2025_07_24_035132_create_product_orders.php
public function up(): void
{
    Schema::create('product_orders', function (Blueprint $table) {
        $table->id();
        $table->string('order_number')->unique();
        $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
        
        // Order status workflow
        $table->enum('status', [
            'pending', 'confirmed', 'processing', 'ready', 'delivered', 'completed'
        ])->default('pending');
        
        // Payment management
        $table->enum('payment_status', [
            'pending', 'pending_verification', 'paid', 'failed'
        ])->default('pending');
        $table->string('payment_method')->nullable();
        $table->string('payment_proof')->nullable();
        $table->text('payment_notes')->nullable();
        
        // Financial fields with high precision
        $table->decimal('total_amount', 15, 2)->default(0);
        
        // Delivery information
        $table->text('delivery_address');
        $table->date('needed_date')->nullable();
        $table->text('notes')->nullable();
        $table->text('admin_notes')->nullable();
        
        // Price negotiation system
        $table->boolean('needs_negotiation')->default(false);
        $table->text('negotiation_message')->nullable();
        $table->decimal('requested_total', 15, 2)->nullable();
        $table->enum('negotiation_status', ['pending', 'approved', 'rejected'])->nullable();
        $table->timestamp('negotiation_requested_at')->nullable();
        $table->timestamp('negotiation_responded_at')->nullable();
        
        // Audit timestamps
        $table->timestamp('payment_uploaded_at')->nullable();
        $table->timestamp('payment_verified_at')->nullable();
        
        $table->timestamps();
        
        // Composite indexes for complex queries
        $table->index(['client_id', 'status', 'created_at'], 'idx_po_client_status_date');
        $table->index(['payment_status', 'created_at'], 'idx_po_payment_status');
        $table->index(['needs_negotiation', 'negotiation_status'], 'idx_po_negotiation');
    });
}
```

### 7.4 Polymorphic Relationships Implementation

**SEO System with Polymorphic Design:**

```php
// database/migrations/2025_05_11_162227_create_seo_table.php
public function up(): void
{
    Schema::create('seo', function (Blueprint $table) {
        $table->id();
        
        // Polymorphic relationship fields
        $table->morphs('seoable'); // creates seoable_id and seoable_type
        
        // SEO metadata fields
        $table->string('meta_title')->nullable();
        $table->text('meta_description')->nullable();
        $table->text('meta_keywords')->nullable();
        $table->string('og_title')->nullable();
        $table->text('og_description')->nullable();
        $table->string('og_image')->nullable();
        $table->string('og_type')->default('website');
        $table->string('twitter_card')->default('summary');
        $table->string('canonical_url')->nullable();
        $table->json('structured_data')->nullable();
        $table->boolean('noindex')->default(false);
        $table->boolean('nofollow')->default(false);
        
        $table->timestamps();
        
        // Indexes for polymorphic queries
        $table->index(['seoable_type', 'seoable_id']);
        $table->index(['noindex', 'nofollow']);
    });
}
```

### 7.5 Notification System Architecture

**Advanced Notification Management:**

```php
// database/migrations/2025_05_29_171650_create_notification_logs_table.php
public function up(): void
{
    Schema::create('notification_logs', function (Blueprint $table) {
        $table->id();
        $table->string('uuid')->unique();
        
        // Recipient information
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $table->string('recipient_email')->nullable();
        $table->string('recipient_phone')->nullable();
        
        // Notification details
        $table->string('type'); // email, sms, push, database
        $table->string('event'); // quotation_created, project_updated, etc.
        $table->string('template')->nullable();
        $table->string('subject')->nullable();
        $table->text('content')->nullable();
        $table->json('data')->nullable(); // structured notification data
        
        // Status tracking
        $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'bounced'])->default('pending');
        $table->text('failure_reason')->nullable();
        $table->integer('retry_count')->default(0);
        $table->timestamp('scheduled_at')->nullable();
        $table->timestamp('sent_at')->nullable();
        $table->timestamp('delivered_at')->nullable();
        
        // Channel-specific fields
        $table->string('email_message_id')->nullable(); // for tracking email delivery
        $table->string('sms_message_id')->nullable(); // for SMS tracking
        
        $table->timestamps();
        
        // Performance indexes
        $table->index(['user_id', 'status', 'created_at']);
        $table->index(['type', 'event', 'created_at']);
        $table->index(['status', 'scheduled_at']);
        $table->index(['retry_count', 'status']);
    });
}
```

### 7.6 Chat System Database Design

**Real-time Communication Schema:**

```php
// database/migrations/2025_05_26_032028_create_chat_system_tables.php
public function up(): void
{
    // Chat sessions for managing conversations
    Schema::create('chat_sessions', function (Blueprint $table) {
        $table->id();
        $table->string('session_id')->unique();
        $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
        $table->string('client_name')->nullable();
        $table->string('client_email')->nullable();
        
        // Session management
        $table->enum('status', ['waiting', 'active', 'closed', 'abandoned'])->default('waiting');
        $table->integer('queue_position')->nullable();
        $table->timestamp('started_at')->nullable();
        $table->timestamp('assigned_at')->nullable();
        $table->timestamp('closed_at')->nullable();
        $table->integer('wait_time')->nullable(); // seconds
        $table->integer('duration')->nullable(); // seconds
        
        // Metadata
        $table->string('user_agent')->nullable();
        $table->string('ip_address')->nullable();
        $table->string('referrer_url')->nullable();
        $table->json('client_info')->nullable();
        
        $table->timestamps();
        
        // Indexes for real-time operations
        $table->index(['status', 'queue_position']);
        $table->index(['operator_id', 'status']);
        $table->index(['client_id', 'created_at']);
    });

    // Chat messages
    Schema::create('chat_messages', function (Blueprint $table) {
        $table->id();
        $table->foreignId('session_id')->constrained('chat_sessions')->onDelete('cascade');
        $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
        $table->enum('sender_type', ['client', 'operator', 'system'])->default('client');
        
        // Message content
        $table->text('message');
        $table->enum('message_type', ['text', 'file', 'image', 'system'])->default('text');
        $table->string('file_path')->nullable();
        $table->string('file_name')->nullable();
        $table->string('file_type')->nullable();
        $table->integer('file_size')->nullable();
        
        // Status tracking
        $table->boolean('is_read')->default(false);
        $table->timestamp('read_at')->nullable();
        
        $table->timestamps();
        
        // Indexes for chat performance
        $table->index(['session_id', 'created_at']);
        $table->index(['sender_id', 'created_at']);
        $table->index(['is_read', 'sender_type']);
    });
}
```

### 7.7 Data Integrity & Performance Optimization

**Advanced Indexing Strategy:**

```php
// Performance optimization through strategic indexing
public function addPerformanceIndexes(): void
{
    // Composite indexes for complex queries
    Schema::table('projects', function (Blueprint $table) {
        $table->index(['client_id', 'status', 'created_at'], 'idx_projects_client_status');
        $table->index(['category_id', 'status', 'featured'], 'idx_projects_category_featured');
        $table->index(['completion_percentage', 'deadline'], 'idx_projects_progress');
    });
    
    // Full-text search indexes
    Schema::table('posts', function (Blueprint $table) {
        $table->fullText(['title', 'content'], 'idx_posts_fulltext');
    });
    
    // JSON field indexes for Laravel 10+
    Schema::table('users', function (Blueprint $table) {
        $table->index(['settings->theme', 'settings->language'], 'idx_users_preferences');
    });
}
```

### 7.8 Database Seeding Strategy

**Production-Ready Data Seeding:**

```php
// database/seeders/DatabaseSeeder.php
public function run(): void
{
    // Production-safe seeders
    $this->call([
        RolePermissionSeeder::class,      // RBAC setup
        SettingsSeeder::class,            // Application settings
        PaymentMethodSeeder::class,       // Payment options
        ServiceCategorySeeder::class,     // Service structure
        ProductCategorySeeder::class,     // Product categories
    ]);
    
    // Development/testing only
    if (app()->environment(['local', 'testing'])) {
        $this->call([
            UserSeeder::class,
            ProjectSeeder::class,
            QuotationSeeder::class,
            MessageSeeder::class,
        ]);
    }
}
```

**Database Design Benefits Achieved:**
- **Scalable Architecture**: 58 migrations enabling iterative database evolution
- **Data Integrity**: Comprehensive foreign key constraints and cascading rules
- **Performance Optimization**: Strategic indexing for complex queries
- **Flexible Relationships**: Polymorphic associations for reusable components
- **Audit Capabilities**: Timestamp tracking and status workflow management
- **Real-time Support**: Optimized schema for chat and notification systems

---

## 8. API Development

### 8.1 RESTful API Architecture

**Comprehensive API Route Structure:**

```php
// routes/api.php - API Route Configuration
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use App\Http\Controllers\Api\{
    ProjectController, ServiceController, PostController,
    ContactController, QuotationController, NotificationController
};

// Rate Limiting Configuration
RateLimiter::for('client-api', fn(Request $request) =>
    Limit::perMinute(100)->by($request->user()?->id ?: $request->ip())
);

RateLimiter::for('admin-api', fn(Request $request) =>
    Limit::perMinute(120)->by($request->user()?->id ?: $request->ip())
);

// Public API Routes
Route::middleware(['throttle:api'])->group(function () {
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{service}', [ServiceController::class, 'show']);
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
    
    // Contact form submission
    Route::post('/contact', [ContactController::class, 'store']);
    Route::post('/quotations', [QuotationController::class, 'store']);
});

// Client API Routes
Route::middleware(['auth:sanctum', 'throttle:client-api'])->group(function () {
    Route::apiResource('client/messages', Client\MessageController::class);
    Route::apiResource('client/quotations', Client\QuotationController::class);
    Route::get('/client/notifications', [NotificationController::class, 'index']);
    Route::post('/client/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead']);
});

// Admin API Routes
Route::middleware(['auth:sanctum', 'admin', 'throttle:admin-api'])->group(function () {
    Route::get('/admin/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/admin/analytics', [GoogleAnalyticsService::class, 'getReports']);
});
```

### 8.2 API Resource Transformation

**Eloquent Resource Implementation:**

```php
// app/Http/Resources/ServiceResource.php
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'description' => $this->when($request->routeIs('*.show'), $this->description),
            'icon' => $this->icon,
            'image_url' => $this->image_url,
            'featured' => $this->featured,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            'seo' => $this->whenLoaded('seo', function () {
                return [
                    'meta_title' => $this->seo->meta_title,
                    'meta_description' => $this->seo->meta_description,
                    'og_image' => $this->seo->og_image,
                ];
            }),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}

// app/Http/Resources/ProjectResource.php
class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,
            'completion_percentage' => $this->completion_percentage,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'deadline' => $this->deadline?->toDateString(),
            'budget' => $this->when($request->user()?->hasRole('admin'), $this->budget),
            
            // Computed attributes
            'progress' => $this->progress,
            'is_overdue' => $this->deadline && $this->deadline->isPast() && $this->status !== 'completed',
            'days_remaining' => $this->deadline ? now()->diffInDays($this->deadline, false) : null,
            
            // Relationships
            'client' => $this->whenLoaded('client', function () {
                return [
                    'id' => $this->client->id,
                    'name' => $this->client->name,
                    'company' => $this->client->company,
                ];
            }),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),
            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => $image->image_url,
                        'alt_text' => $image->alt_text,
                        'sort_order' => $image->sort_order,
                    ];
                });
            }),
            'milestones' => $this->whenLoaded('milestones', function () {
                return $this->milestones->map(function ($milestone) {
                    return [
                        'id' => $milestone->id,
                        'name' => $milestone->name,
                        'description' => $milestone->description,
                        'due_date' => $milestone->due_date?->toDateString(),
                        'status' => $milestone->status,
                        'completion_percentage' => $milestone->completion_percentage,
                    ];
                });
            }),
        ];
    }
}
```

### 8.3 API Controller Implementation

**Advanced API Controller Logic:**

```php
// app/Http/Controllers/Api/ProjectController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Project::with(['category', 'images'])
                       ->where('is_active', true)
                       ->where('status', '!=', 'draft');

        // Apply filters
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('featured')) {
            $query->where('featured', $request->boolean('featured'));
        }

        // Apply search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        // Apply sorting
        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        $allowedSorts = ['created_at', 'updated_at', 'name', 'completion_percentage', 'deadline'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortOrder);
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 50); // Max 50 items per page
        $projects = $query->paginate($perPage);

        return ProjectResource::collection($projects)->additional([
            'meta' => [
                'total_projects' => Project::where('is_active', true)->count(),
                'featured_projects' => Project::where('featured', true)->count(),
                'completed_projects' => Project::where('status', 'completed')->count(),
            ]
        ]);
    }

    public function show(Project $project): ProjectResource
    {
        // Load relationships for detailed view
        $project->load([
            'category',
            'client:id,name,company',
            'images' => function ($query) {
                $query->orderBy('sort_order');
            },
            'milestones' => function ($query) {
                $query->orderBy('due_date');
            },
            'seo'
        ]);

        return new ProjectResource($project);
    }
}

// app/Http/Controllers/Api/ContactController.php
class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'service_id' => 'nullable|exists:services,id',
            'g-recaptcha-response' => 'sometimes|required',
        ]);

        // Validate reCAPTCHA if present
        if ($request->filled('g-recaptcha-response')) {
            $this->validateRecaptcha($request->input('g-recaptcha-response'));
        }

        try {
            // Create message
            $message = Message::create([
                'subject' => $validated['subject'],
                'content' => $validated['message'],
                'sender_email' => $validated['email'],
                'sender_name' => $validated['name'],
                'sender_phone' => $validated['phone'],
                'sender_company' => $validated['company'],
                'service_id' => $validated['service_id'] ?? null,
                'status' => 'pending',
                'source' => 'api',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Send notification to admin
            Notifications::send('contact_form_submitted', $message);

            // Send auto-reply to user
            Mail::to($validated['email'])->send(new ContactAutoReply($message));

            return response()->json([
                'success' => true,
                'message' => 'Your message has been sent successfully. We will get back to you soon.',
                'reference_id' => $message->id,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Contact form submission failed', [
                'email' => $validated['email'],
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sorry, there was an error sending your message. Please try again later.',
            ], 500);
        }
    }

    protected function validateRecaptcha(string $token): void
    {
        $client = new Client();
        $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $token,
                'remoteip' => request()->ip(),
            ]
        ]);

        $result = json_decode($response->getBody()->getContents());

        if (!$result->success || $result->score < 0.5) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'reCAPTCHA verification failed'
            ]);
        }
    }
}
```

### 8.4 API Authentication & Security

**Sanctum Token Management:**

```php
// app/Http/Controllers/Api/AuthController.php
class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required|string|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated.',
            ], 403);
        }

        // Record login attempt
        $user->recordSuccessfulLogin();

        // Create token with specific abilities
        $abilities = $this->getUserAbilities($user);
        $token = $user->createToken($request->device_name, $abilities);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                ],
                'token' => $token->plainTextToken,
                'abilities' => $abilities,
                'expires_at' => now()->addDays(30)->toISOString(),
            ]
        ]);
    }

    protected function getUserAbilities(User $user): array
    {
        $abilities = ['read'];

        if ($user->hasRole('admin')) {
            $abilities = ['*']; // All abilities
        } elseif ($user->hasRole('client')) {
            $abilities = [
                'read', 'create:messages', 'update:profile',
                'create:quotations', 'read:projects', 'read:notifications'
            ];
        }

        return $abilities;
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    public function refreshToken(Request $request): JsonResponse
    {
        $user = $request->user();
        $currentToken = $request->user()->currentAccessToken();
        
        // Delete current token
        $currentToken->delete();
        
        // Create new token
        $abilities = $this->getUserAbilities($user);
        $newToken = $user->createToken($currentToken->name, $abilities);

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $newToken->plainTextToken,
                'expires_at' => now()->addDays(30)->toISOString(),
            ]
        ]);
    }
}
```

### 8.5 API Error Handling & Responses

**Standardized Error Response Format:**

```php
// app/Http/Middleware/ApiResponseMiddleware.php
class ApiResponseMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        // Handle API exceptions
        if ($request->is('api/*') && $response->exception) {
            return $this->handleApiException($response->exception);
        }
        
        return $response;
    }
    
    protected function handleApiException($exception): JsonResponse
    {
        $status = 500;
        $message = 'Internal server error';
        
        if ($exception instanceof ValidationException) {
            $status = 422;
            $message = 'Validation failed';
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $exception->errors(),
            ], $status);
        }
        
        if ($exception instanceof AuthenticationException) {
            $status = 401;
            $message = 'Unauthenticated';
        }
        
        if ($exception instanceof AuthorizationException) {
            $status = 403;
            $message = 'Forbidden';
        }
        
        if ($exception instanceof ModelNotFoundException) {
            $status = 404;
            $message = 'Resource not found';
        }
        
        return response()->json([
            'success' => false,
            'message' => $message,
            'error_code' => $status,
        ], $status);
    }
}

// app/Traits/ApiResponse.php
trait ApiResponse
{
    protected function successResponse($data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
    
    protected function errorResponse(string $message, int $status = 400, array $errors = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];
        
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        
        return response()->json($response, $status);
    }
    
    protected function paginatedResponse($data, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ]
        ]);
    }
}
```

**API Development Benefits Achieved:**
- **RESTful Design**: Standardized API endpoints following REST principles
- **Resource Transformation**: Eloquent resources for consistent data formatting
- **Authentication**: Sanctum token-based authentication with abilities
- **Rate Limiting**: Intelligent throttling based on user roles
- **Error Handling**: Comprehensive exception handling with standardized responses
- **Security**: Input validation, reCAPTCHA integration, and audit logging

---

## 9. Real-time Features Implementation

### 9.1 Laravel Reverb Integration

**WebSocket Server Configuration:**

```php
// config/reverb.php - Real-time Communication Setup
return [
    'default' => env('REVERB_SERVER', 'reverb'),
    
    'servers' => [
        'reverb' => [
            'host' => env('REVERB_HOST', '0.0.0.0'),
            'port' => env('REVERB_PORT', 8080),
            'hostname' => env('REVERB_HOSTNAME', 'localhost'),
            'options' => [
                'tls' => [
                    'local_cert' => env('REVERB_TLS_CERT'),
                    'local_pk' => env('REVERB_TLS_KEY'),
                    'verify_peer' => env('REVERB_TLS_VERIFY', true),
                ]
            ],
            'max_request_size' => env('REVERB_MAX_REQUEST_SIZE', 10000),
            'scaling' => [
                'enabled' => env('REVERB_SCALING_ENABLED', false),
                'channel' => env('REVERB_SCALING_CHANNEL', 'reverb'),
                'server' => [
                    'url' => env('REDIS_URL'),
                    'host' => env('REDIS_HOST', '127.0.0.1'),
                    'port' => env('REDIS_PORT', 6379),
                ]
            ]
        ],
    ],
    
    'apps' => [
        'reverb-app' => [
            'app_id' => env('REVERB_APP_ID'),
            'app_key' => env('REVERB_APP_KEY'),
            'app_secret' => env('REVERB_APP_SECRET'),
            'options' => [
                'host' => env('REVERB_HOST', '127.0.0.1'),
                'port' => env('REVERB_PORT', 6001),
                'scheme' => env('REVERB_SCHEME', 'http'),
            ]
        ],
    ]
];
```

### 9.2 Real-time Chat System

**Advanced Chat Service Implementation:**

```php
// app/Services/ChatService.php
namespace App\Services;

use App\Models\{ChatSession, ChatMessage, ChatOperator, User};
use App\Events\{ChatMessageSent, ChatSessionAssigned, ChatSessionClosed};
use App\Jobs\{ProcessChatQueueJob, ChatSessionTimeoutJob};
use Illuminate\Support\Str;

class ChatService
{
    public function createSession(array $data): ChatSession
    {
        $session = ChatSession::create([
            'session_id' => Str::uuid(),
            'client_id' => $data['client_id'] ?? null,
            'client_name' => $data['client_name'] ?? 'Anonymous',
            'client_email' => $data['client_email'] ?? null,
            'status' => 'waiting',
            'priority' => $this->calculatePriority($data),
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip(),
            'referrer_url' => request()->headers->get('referer'),
            'client_info' => $this->getClientInfo($data),
        ]);

        // Add to queue and assign position
        $this->addToQueue($session);
        
        // Dispatch queue processing job
        ProcessChatQueueJob::dispatch();
        
        // Schedule timeout job
        ChatSessionTimeoutJob::dispatch($session)->delay(now()->addMinutes(15));

        return $session;
    }

    public function sendMessage(ChatSession $session, array $messageData): ChatMessage
    {
        $message = $session->messages()->create([
            'sender_id' => $messageData['sender_id'] ?? null,
            'sender_type' => $messageData['sender_type'] ?? 'client',
            'message' => $messageData['message'],
            'message_type' => $messageData['message_type'] ?? 'text',
            'file_path' => $messageData['file_path'] ?? null,
            'file_name' => $messageData['file_name'] ?? null,
            'file_type' => $messageData['file_type'] ?? null,
            'file_size' => $messageData['file_size'] ?? null,
        ]);

        // Update session activity
        $session->touch('last_activity_at');

        // Broadcast message to all connected clients
        broadcast(new ChatMessageSent($message))->toOthers();

        // Send notifications based on sender type
        if ($messageData['sender_type'] === 'client') {
            $this->notifyOperators($session, $message);
        } else {
            $this->notifyClient($session, $message);
        }

        return $message;
    }

    public function assignOperator(ChatSession $session, User $operator): bool
    {
        if ($session->status !== 'waiting') {
            return false;
        }

        // Check operator availability
        if (!$this->isOperatorAvailable($operator)) {
            return false;
        }

        // Assign operator
        $session->update([
            'operator_id' => $operator->id,
            'status' => 'active',
            'assigned_at' => now(),
            'wait_time' => $session->created_at->diffInSeconds(now()),
        ]);

        // Update operator status
        ChatOperator::updateOrCreate(
            ['user_id' => $operator->id],
            ['status' => 'busy', 'last_activity_at' => now()]
        );

        // Broadcast assignment
        broadcast(new ChatSessionAssigned($session));

        // Send welcome message
        $this->sendSystemMessage($session, "You are now connected with {$operator->name}");

        return true;
    }

    protected function calculatePriority(array $data): string
    {
        // VIP clients get high priority
        if (isset($data['client_id'])) {
            $user = User::find($data['client_id']);
            if ($user && $user->hasRole('vip_client')) {
                return 'high';
            }
        }

        // Returning visitors get medium priority
        if (request()->hasCookie('returning_visitor')) {
            return 'medium';
        }

        return 'normal';
    }

    protected function addToQueue(ChatSession $session): void
    {
        $position = ChatSession::where('status', 'waiting')
                             ->where('created_at', '<', $session->created_at)
                             ->count() + 1;

        $session->update(['queue_position' => $position]);
    }

    protected function isOperatorAvailable(User $operator): bool
    {
        $operatorInfo = ChatOperator::where('user_id', $operator->id)->first();
        
        if (!$operatorInfo || $operatorInfo->status !== 'available') {
            return false;
        }

        // Check current active sessions
        $activeSessions = ChatSession::where('operator_id', $operator->id)
                                   ->where('status', 'active')
                                   ->count();

        $maxSessions = config('chat.max_concurrent_sessions', 3);
        return $activeSessions < $maxSessions;
    }
}
```

### 9.3 Event Broadcasting System

**Real-time Event Implementation:**

```php
// app/Events/ChatMessageSent.php
namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatMessage $message;

    public function __construct(ChatMessage $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('chat-session.' . $this->message->chatSession->session_id),
            new Channel('admin-chat-dashboard'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'session_id' => $this->message->chatSession->session_id,
            'sender' => [
                'id' => $this->message->sender_id,
                'name' => $this->message->sender?->name ?? 'Anonymous',
                'type' => $this->message->sender_type,
            ],
            'message' => $this->message->message,
            'message_type' => $this->message->message_type,
            'file_info' => $this->message->file_path ? [
                'name' => $this->message->file_name,
                'type' => $this->message->file_type,
                'size' => $this->message->file_size,
                'url' => asset('storage/' . $this->message->file_path),
            ] : null,
            'timestamp' => $this->message->created_at->toISOString(),
            'formatted_time' => $this->message->created_at->format('H:i'),
        ];
    }
}

// app/Events/ChatSessionAssigned.php
class ChatSessionAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatSession $session;

    public function __construct(ChatSession $session)
    {
        $this->session = $session;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('chat-session.' . $this->session->session_id),
            new Channel('admin-chat-dashboard'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'session.assigned';
    }

    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->session->session_id,
            'operator' => [
                'id' => $this->session->operator->id,
                'name' => $this->session->operator->name,
                'avatar' => $this->session->operator->avatar_url,
            ],
            'status' => $this->session->status,
            'assigned_at' => $this->session->assigned_at->toISOString(),
        ];
    }
}
```

### 9.4 Real-time Notification System

**Live Notification Broadcasting:**

```php
// app/Events/NotificationSent.php
class NotificationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    public $user;

    public function __construct($notification, $user)
    {
        $this->notification = $notification;
        $this->user = $user;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('user.' . $this->user->id . '.notifications'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notification.received';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,
            'type' => $this->notification->type,
            'title' => $this->notification->data['title'] ?? 'New Notification',
            'message' => $this->notification->data['message'],
            'icon' => $this->notification->data['icon'] ?? 'bell',
            'url' => $this->notification->data['url'] ?? null,
            'created_at' => $this->notification->created_at->toISOString(),
            'formatted_time' => $this->notification->created_at->diffForHumans(),
        ];
    }
}

// app/Services/NotificationService.php - Real-time Integration
class NotificationService
{
    public function send(string $type, $notifiable, array $data = []): void
    {
        $notification = $notifiable->notifications()->create([
            'id' => Str::uuid(),
            'type' => $type,
            'data' => $data,
            'read_at' => null,
        ]);

        // Broadcast real-time notification
        broadcast(new NotificationSent($notification, $notifiable));

        // Send email if enabled
        if ($notifiable->email_notifications) {
            Mail::to($notifiable)->queue(new NotificationMail($notification));
        }

        // Send push notification if mobile token exists
        if ($notifiable->device_token) {
            $this->sendPushNotification($notifiable, $data);
        }
    }
}
```

### 9.5 Frontend JavaScript Integration

**Real-time Client Implementation:**

```javascript
// resources/js/chat/chat-system.js
class ChatSystem {
    constructor(sessionId, authToken) {
        this.sessionId = sessionId;
        this.authToken = authToken;
        this.echo = null;
        this.messageContainer = null;
        this.messageInput = null;
        
        this.initializeEcho();
        this.bindEvents();
    }

    initializeEcho() {
        this.echo = new Echo({
            broadcaster: 'reverb',
            key: process.env.MIX_REVERB_APP_KEY,
            wsHost: process.env.MIX_REVERB_HOST,
            wsPort: process.env.MIX_REVERB_PORT,
            wssPort: process.env.MIX_REVERB_PORT,
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
            auth: {
                headers: {
                    Authorization: `Bearer ${this.authToken}`
                }
            }
        });

        // Listen for messages in this chat session
        this.echo.channel(`chat-session.${this.sessionId}`)
            .listen('message.sent', (data) => {
                this.displayMessage(data);
                this.playNotificationSound();
            })
            .listen('session.assigned', (data) => {
                this.handleOperatorAssigned(data);
            })
            .listen('session.closed', (data) => {
                this.handleSessionClosed(data);
            });

        // Listen for typing indicators
        this.echo.channel(`chat-session.${this.sessionId}`)
            .listenForWhisper('typing', (data) => {
                this.showTypingIndicator(data.user);
            })
            .listenForWhisper('stopped-typing', (data) => {
                this.hideTypingIndicator(data.user);
            });
    }

    sendMessage(message, files = null) {
        const formData = new FormData();
        formData.append('message', message);
        formData.append('session_id', this.sessionId);
        
        if (files) {
            Array.from(files).forEach(file => {
                formData.append('files[]', file);
            });
        }

        fetch('/api/chat/send', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${this.authToken}`,
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.messageInput.value = '';
                this.displayMessage(data.message, true); // Mark as own message
            } else {
                this.showError(data.message);
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            this.showError('Failed to send message. Please try again.');
        });
    }

    displayMessage(messageData, isOwnMessage = false) {
        const messageElement = document.createElement('div');
        messageElement.className = `message ${isOwnMessage ? 'own-message' : 'other-message'}`;
        
        messageElement.innerHTML = `
            <div class="message-content">
                <div class="message-header">
                    <span class="sender-name">${messageData.sender.name}</span>
                    <span class="message-time">${messageData.formatted_time}</span>
                </div>
                <div class="message-text">${this.escapeHtml(messageData.message)}</div>
                ${messageData.file_info ? this.renderFileAttachment(messageData.file_info) : ''}
            </div>
        `;

        this.messageContainer.appendChild(messageElement);
        this.scrollToBottom();
    }

    handleTypingIndicator() {
        let typingTimer;
        
        this.messageInput.addEventListener('keyup', () => {
            this.echo.channel(`chat-session.${this.sessionId}`)
                .whisper('typing', {
                    user: this.currentUser,
                    timestamp: Date.now()
                });

            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                this.echo.channel(`chat-session.${this.sessionId}`)
                    .whisper('stopped-typing', {
                        user: this.currentUser
                    });
            }, 1000);
        });
    }

    playNotificationSound() {
        if (this.notificationSound && !document.hidden) {
            this.notificationSound.play().catch(e => {
                console.log('Could not play notification sound:', e);
            });
        }
    }

    showTypingIndicator(user) {
        if (user.id !== this.currentUser.id) {
            const indicator = document.getElementById('typing-indicator');
            indicator.textContent = `${user.name} is typing...`;
            indicator.style.display = 'block';
        }
    }

    hideTypingIndicator() {
        const indicator = document.getElementById('typing-indicator');
        indicator.style.display = 'none';
    }
}

// Initialize chat system when page loads
document.addEventListener('DOMContentLoaded', function() {
    const chatContainer = document.getElementById('chat-container');
    if (chatContainer) {
        const sessionId = chatContainer.dataset.sessionId;
        const authToken = chatContainer.dataset.authToken;
        
        window.chatSystem = new ChatSystem(sessionId, authToken);
    }
});
```

### 9.6 Queue-Based Message Processing

**Background Job Processing:**

```php
// app/Jobs/ProcessChatQueueJob.php
namespace App\Jobs;

use App\Models\{ChatSession, ChatOperator, User};
use App\Events\ChatSessionAssigned;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};

class ProcessChatQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Get waiting sessions ordered by priority and creation time
        $waitingSessions = ChatSession::where('status', 'waiting')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at')
            ->get();

        foreach ($waitingSessions as $session) {
            $operator = $this->findAvailableOperator();
            
            if ($operator) {
                $session->update([
                    'operator_id' => $operator->id,
                    'status' => 'active',
                    'assigned_at' => now(),
                ]);

                // Update operator status
                ChatOperator::updateOrCreate(
                    ['user_id' => $operator->id],
                    ['status' => 'busy', 'last_activity_at' => now()]
                );

                // Broadcast assignment
                broadcast(new ChatSessionAssigned($session));
                
                // Update queue positions for remaining sessions
                $this->updateQueuePositions();
            }
        }
    }

    protected function findAvailableOperator(): ?User
    {
        return User::whereHas('roles', function ($query) {
                $query->where('name', 'chat_operator');
            })
            ->whereHas('chatOperator', function ($query) {
                $query->where('status', 'available')
                      ->where('last_activity_at', '>', now()->subMinutes(5));
            })
            ->whereDoesntHave('activeChatSessions', function ($query) {
                $query->where('status', 'active');
            }, '>=', config('chat.max_concurrent_sessions', 3))
            ->inRandomOrder()
            ->first();
    }

    protected function updateQueuePositions(): void
    {
        $waitingSessions = ChatSession::where('status', 'waiting')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at')
            ->get();

        foreach ($waitingSessions as $index => $session) {
            $session->update(['queue_position' => $index + 1]);
        }
    }
}
```

**Real-time Features Benefits Achieved:**
- **WebSocket Communication**: Native Laravel Reverb integration for real-time messaging
- **Event Broadcasting**: Comprehensive event system with multi-channel support
- **Queue Management**: Intelligent operator assignment and session prioritization
- **Typing Indicators**: Real-time user interaction feedback
- **File Sharing**: Support for real-time file attachments in chat
- **Notification System**: Live notifications with multiple delivery channels

---

## 10. Testing & Quality Assurance

### 10.1 Testing Framework & Configuration

**Pest PHP Testing Framework Integration:**

```php
// composer.json - Testing Dependencies
"require-dev": {
    "fakerphp/faker": "^1.23",
    "laravel/pint": "^1.22",           // Code styling
    "mockery/mockery": "^1.6",         // Mocking framework
    "nunomaduro/collision": "^8.6",    // Error reporting
    "pestphp/pest": "^3.8",           // Modern testing framework
    "pestphp/pest-plugin-laravel": "^3.2" // Laravel integration
}

// tests/Pest.php - Test Configuration
pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

// Custom expectations
expect()->extend('toBeValidEmail', function () {
    return $this->toMatch('/^[^\s@]+@[^\s@]+\.[^\s@]+$/');
});

expect()->extend('toBeValidUuid', function () {
    return $this->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i');
});
```

**PHPUnit Configuration:**

```xml
<!-- phpunit.xml -->
<phpunit bootstrap="vendor/autoload.php" colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_STORE" value="array"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
    </php>
</phpunit>
```

### 10.2 Authentication & Authorization Testing

**Comprehensive Authentication Test Suite:**

```php
// tests/Feature/Auth/AuthenticationTest.php
use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard'));
});

test('users cannot authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users with inactive accounts cannot login', function () {
    $user = User::factory()->create(['is_active' => false]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors();
});

test('failed login attempts are tracked', function () {
    $user = User::factory()->create();

    // Make 3 failed attempts
    for ($i = 0; $i < 3; $i++) {
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    $user->refresh();
    expect($user->failed_login_attempts)->toBe(3);
});

test('account gets locked after 5 failed attempts', function () {
    $user = User::factory()->create();

    // Make 5 failed attempts
    for ($i = 0; $i < 5; $i++) {
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    $user->refresh();
    expect($user->isLocked())->toBeTrue();
    expect($user->locked_at)->not->toBeNull();
});
```

**Role-Based Authorization Testing:**

```php
// tests/Feature/Auth/RoleAuthorizationTest.php
use App\Models\{User, Project, Quotation};
use Spatie\Permission\Models\Role;

test('admin can access admin dashboard', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get('/admin/dashboard');
    $response->assertStatus(200);
});

test('client cannot access admin dashboard', function () {
    $client = User::factory()->create();
    $client->assignRole('client');

    $response = $this->actingAs($client)->get('/admin/dashboard');
    $response->assertStatus(403);
});

test('client can only view own projects', function () {
    $client1 = User::factory()->create();
    $client2 = User::factory()->create();
    [$client1, $client2]->each(fn($user) => $user->assignRole('client'));

    $project1 = Project::factory()->for($client1, 'client')->create();
    $project2 = Project::factory()->for($client2, 'client')->create();

    // Client 1 can see their project
    $response = $this->actingAs($client1)->get("/client/projects/{$project1->id}");
    $response->assertStatus(200);

    // Client 1 cannot see client 2's project
    $response = $this->actingAs($client1)->get("/client/projects/{$project2->id}");
    $response->assertStatus(403);
});
```

### 10.3 API Testing Suite

**Comprehensive API Testing:**

```php
// tests/Feature/Api/ProjectApiTest.php
use App\Models\{User, Project, ProjectCategory};

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
    
    $this->category = ProjectCategory::factory()->create();
    $this->projects = Project::factory()
        ->count(5)
        ->for($this->category)
        ->create(['is_active' => true]);
});

test('can fetch paginated projects list', function () {
    $response = $this->getJson('/api/projects');

    $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'name', 'slug', 'description', 
                        'status', 'completion_percentage',
                        'start_date', 'end_date', 'deadline'
                    ]
                ],
                'meta' => [
                    'total_projects',
                    'featured_projects', 
                    'completed_projects'
                ]
            ]);

    expect($response->json('data'))->toHaveCount(5);
});

test('can filter projects by category', function () {
    $category2 = ProjectCategory::factory()->create();
    Project::factory()->for($category2)->create();

    $response = $this->getJson("/api/projects?category={$this->category->slug}");

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(5);
});

test('can search projects by name', function () {
    $searchProject = Project::factory()
        ->for($this->category)
        ->create(['name' => 'Special Search Project']);

    $response = $this->getJson('/api/projects?search=Special Search');

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.name'))->toBe('Special Search Project');
});

test('api has proper rate limiting', function () {
    // Make 101 requests (exceeding limit of 100/minute)
    for ($i = 0; $i <= 100; $i++) {
        $response = $this->getJson('/api/projects');
        
        if ($i === 100) {
            $response->assertStatus(429); // Too Many Requests
        } else {
            $response->assertStatus(200);
        }
    }
});

test('api returns proper error for invalid resource', function () {
    $response = $this->getJson('/api/projects/999999');

    $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Resource not found'
            ]);
});
```

### 10.4 E-commerce Testing

**Order Management Testing:**

```php
// tests/Feature/Ecommerce/OrderManagementTest.php
use App\Models\{Product, ProductOrder, ProductOrderItem, User};

test('client can create order from cart', function () {
    $client = User::factory()->create();
    $client->assignRole('client');
    
    $products = Product::factory()->count(3)->create(['is_active' => true]);
    
    // Add items to cart
    foreach ($products as $product) {
        $this->actingAs($client)->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }

    // Create order
    $response = $this->actingAs($client)->postJson('/api/orders', [
        'client_name' => $client->name,
        'client_email' => $client->email,
        'client_phone' => '1234567890',
        'delivery_address' => '123 Test Street',
        'needed_date' => now()->addDays(7)->format('Y-m-d'),
        'notes' => 'Test order notes'
    ]);

    $response->assertStatus(201);
    
    $order = ProductOrder::where('client_id', $client->id)->first();
    expect($order)->not->toBeNull();
    expect($order->items)->toHaveCount(3);
    expect($order->status)->toBe('pending');
});

test('order total is calculated correctly', function () {
    $client = User::factory()->create();
    $client->assignRole('client');
    
    $product1 = Product::factory()->create(['price' => 100.00]);
    $product2 = Product::factory()->create(['price' => 150.00]);
    
    $this->actingAs($client)->postJson('/api/cart/add', [
        'product_id' => $product1->id,
        'quantity' => 2  // 200.00
    ]);
    
    $this->actingAs($client)->postJson('/api/cart/add', [
        'product_id' => $product2->id,
        'quantity' => 1  // 150.00
    ]);

    $response = $this->actingAs($client)->postJson('/api/orders', [
        'client_name' => $client->name,
        'client_email' => $client->email,
        'client_phone' => '1234567890',
        'delivery_address' => '123 Test Street',
        'needed_date' => now()->addDays(7)->format('Y-m-d')
    ]);

    $order = ProductOrder::where('client_id', $client->id)->first();
    expect($order->total_amount)->toEqual(350.00);
});

test('order cannot be created with insufficient stock', function () {
    $client = User::factory()->create();
    $client->assignRole('client');
    
    $product = Product::factory()->create([
        'stock_quantity' => 1,
        'is_active' => true
    ]);
    
    $response = $this->actingAs($client)->postJson('/api/cart/add', [
        'product_id' => $product->id,
        'quantity' => 5  // More than available stock
    ]);

    $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Insufficient stock available'
            ]);
});

test('price negotiation workflow works correctly', function () {
    $client = User::factory()->create();
    $admin = User::factory()->create();
    [$client, $admin]->each->assignRole(['client', 'admin']);
    
    $order = ProductOrder::factory()->for($client, 'client')->create([
        'total_amount' => 1000.00,
        'status' => 'pending'
    ]);

    // Client requests negotiation
    $response = $this->actingAs($client)->postJson("/api/orders/{$order->id}/negotiate", [
        'requested_total' => 800.00,
        'negotiation_message' => 'Can we get a discount?'
    ]);

    $response->assertStatus(200);
    
    $order->refresh();
    expect($order->needs_negotiation)->toBeTrue();
    expect($order->requested_total)->toEqual(800.00);
    expect($order->negotiation_status)->toBe('pending');

    // Admin approves negotiation
    $response = $this->actingAs($admin)->postJson("/api/admin/orders/{$order->id}/approve-negotiation", [
        'admin_notes' => 'Approved for loyal customer'
    ]);

    $response->assertStatus(200);
    
    $order->refresh();
    expect($order->total_amount)->toEqual(800.00);
    expect($order->negotiation_status)->toBe('approved');
});
```

### 10.5 Real-time Features Testing

**Chat System Testing:**

```php
// tests/Feature/Chat/ChatSystemTest.php
use App\Models\{User, ChatSession, ChatMessage};
use App\Events\{ChatMessageSent, ChatSessionAssigned};
use Illuminate\Support\Facades\Event;

test('can create chat session', function () {
    Event::fake();
    
    $response = $this->postJson('/api/chat/start', [
        'client_name' => 'Test User',
        'client_email' => 'test@example.com'
    ]);

    $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'session_id',
                    'status',
                    'queue_position'
                ]
            ]);

    expect(ChatSession::count())->toBe(1);
    
    $session = ChatSession::first();
    expect($session->status)->toBe('waiting');
    expect($session->queue_position)->toBe(1);
});

test('can send message in chat session', function () {
    Event::fake([ChatMessageSent::class]);
    
    $client = User::factory()->create();
    $session = ChatSession::factory()->for($client, 'client')->create();

    $response = $this->actingAs($client)->postJson('/api/chat/send', [
        'session_id' => $session->session_id,
        'message' => 'Hello, I need help!'
    ]);

    $response->assertStatus(201);
    
    expect($session->messages()->count())->toBe(1);
    
    $message = $session->messages()->first();
    expect($message->message)->toBe('Hello, I need help!');
    expect($message->sender_type)->toBe('client');

    Event::assertDispatched(ChatMessageSent::class);
});

test('operator can be assigned to waiting session', function () {
    Event::fake([ChatSessionAssigned::class]);
    
    $client = User::factory()->create();
    $operator = User::factory()->create();
    $operator->assignRole('chat_operator');
    
    $session = ChatSession::factory()
        ->for($client, 'client')
        ->create(['status' => 'waiting']);

    $response = $this->actingAs($operator)->postJson("/api/admin/chat/assign/{$session->id}");

    $response->assertStatus(200);
    
    $session->refresh();
    expect($session->status)->toBe('active');
    expect($session->operator_id)->toBe($operator->id);
    expect($session->assigned_at)->not->toBeNull();

    Event::assertDispatched(ChatSessionAssigned::class);
});

test('chat session auto-closes after inactivity', function () {
    $session = ChatSession::factory()->create([
        'status' => 'active',
        'last_activity_at' => now()->subMinutes(30)
    ]);

    // Run the chat cleanup job
    Artisan::call('chat:cleanup');

    $session->refresh();
    expect($session->status)->toBe('closed');
});
```

### 10.6 Database Testing & Factories

**Model Factories for Test Data:**

```php
// database/factories/UserFactory.php
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'company' => fake()->company(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'is_active' => true,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}

// database/factories/ProductOrderFactory.php
class ProductOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_number' => 'ORD' . now()->format('Ym') . str_pad(fake()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'status' => fake()->randomElement(['pending', 'confirmed', 'processing', 'completed']),
            'payment_status' => fake()->randomElement(['pending', 'paid', 'failed']),
            'total_amount' => fake()->randomFloat(2, 50, 2000),
            'delivery_address' => fake()->address(),
            'needed_date' => fake()->dateTimeBetween('now', '+30 days'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);
    }
}
```

### 10.7 Code Quality & Static Analysis

**Laravel Pint for Code Styling:**

```json
// pint.json - Code Style Configuration
{
    "preset": "laravel",
    "rules": {
        "simplified_null_return": true,
        "not_operator_with_successor_space": true,
        "ordered_imports": {
            "sort_algorithm": "alpha"
        },
        "no_unused_imports": true,
        "array_syntax": {
            "syntax": "short"
        }
    }
}
```

**Testing Commands & Scripts:**

```bash
# Run all tests
./vendor/bin/pest

# Run specific test suite
./vendor/bin/pest tests/Feature/Auth

# Run tests with coverage
./vendor/bin/pest --coverage

# Run code style fixes
./vendor/bin/pint

# Run static analysis (if installed)
./vendor/bin/phpstan analyse
```

**Testing Benefits Achieved:**
- **Comprehensive Coverage**: Unit, feature, and integration tests across all major components
- **Modern Framework**: Pest PHP for expressive and readable test syntax
- **Database Testing**: In-memory SQLite for fast test execution
- **API Testing**: Complete API endpoint testing with rate limiting validation
- **Real-time Testing**: Event broadcasting and WebSocket functionality testing
- **Quality Assurance**: Automated code styling and static analysis integration

---

## 11. Security Implementation

### 11.1 Security Headers & HTTP Protection

**Comprehensive Security Headers Middleware:**

```php
// app/Http/Middleware/SecurityHeaders.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Prevent clickjacking attacks
        $response->headers->set('X-Frame-Options', 'DENY');
        
        // Enable XSS filtering
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Restrict browser permissions
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        
        // HSTS for HTTPS enforcement
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Content Security Policy for admin/client areas
        if ($request->is('admin/*') || $request->is('client/*')) {
            $csp = $this->buildContentSecurityPolicy($request);
            $response->headers->set('Content-Security-Policy', $csp);
        }

        return $response;
    }

    protected function buildContentSecurityPolicy(Request $request): string
    {
        $nonce = base64_encode(random_bytes(16));
        $request->attributes->set('csp_nonce', $nonce);

        return "default-src 'self'; " .
               "script-src 'self' 'nonce-{$nonce}' https://cdnjs.cloudflare.com; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
               "font-src 'self' https://fonts.gstatic.com; " .
               "img-src 'self' data: https: blob:; " .
               "connect-src 'self' wss: ws:; " .
               "media-src 'self'; " .
               "object-src 'none'; " .
               "base-uri 'self'; " .
               "form-action 'self'; " .
               "frame-ancestors 'none';";
    }
}
```

### 11.2 Input Validation & Sanitization

**Advanced Form Request Validation:**

```php
// app/Http/Requests/ContactRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Implement rate limiting per IP
        return $this->passesRateLimit();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-Z\s\-\'\.]+$/', // Only letters, spaces, hyphens, apostrophes, dots
            ],
            'email' => [
                'required',
                'email:strict,dns,spoof',
                'max:255',
                'not_in:' . implode(',', $this->getDisposableEmailDomains()),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/', // Only numbers, spaces, hyphens, parentheses, plus
            ],
            'company' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-\&\.\,]+$/', // Alphanumeric with basic business chars
            ],
            'subject' => [
                'required',
                'string',
                'min:5',
                'max:255',
                'not_regex:/(<script|javascript:|data:|vbscript:|on\w+\s*=)/i', // Basic XSS prevention
            ],
            'message' => [
                'required',
                'string',
                'min:10',
                'max:2000',
                'not_regex:/(<script|javascript:|data:|vbscript:|on\w+\s*=)/i',
            ],
            'service_id' => 'nullable|exists:services,id',
            'g-recaptcha-response' => 'sometimes|required',
            'honey_pot' => 'sometimes|max:0', // Honeypot field for spam detection
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'The name contains invalid characters.',
            'email.not_in' => 'Temporary email addresses are not allowed.',
            'subject.not_regex' => 'The subject contains potentially harmful content.',
            'message.not_regex' => 'The message contains potentially harmful content.',
            'honey_pot.max' => 'Spam detection triggered.',
        ];
    }

    protected function passesRateLimit(): bool
    {
        $key = 'contact_form_' . $this->ip();
        $maxAttempts = 5; // 5 attempts
        $decayMinutes = 60; // per hour

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Too many contact attempts. Please try again in {$seconds} seconds."
            ]);
        }

        RateLimiter::hit($key, $decayMinutes * 60);
        return true;
    }

    protected function getDisposableEmailDomains(): array
    {
        return [
            '10minutemail.com', 'tempmail.org', 'guerrillamail.com',
            'mailinator.com', 'yopmail.com', 'temp-mail.org',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => strip_tags($this->name),
            'subject' => strip_tags($this->subject),
            'message' => strip_tags($this->message, '<p><br><b><i><u><strong><em>'),
            'company' => strip_tags($this->company),
        ]);
    }
}

// app/Http/Requests/StoreQuotationRequest.php
class StoreQuotationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:255|regex:/^[a-zA-Z\s\-\'\.]+$/',
            'email' => 'required|email:strict,dns|max:255',
            'phone' => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]+$/',
            'company' => 'nullable|string|max:255',
            'service_id' => 'required|exists:services,id',
            'project_type' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'requirements' => 'required|string|min:20|max:2000',
            'budget_range' => [
                'required',
                Rule::in(['under_1000', '1000_5000', '5000_15000', 'over_15000']),
            ],
            'start_date' => 'nullable|date|after:today',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => [
                'file',
                'max:10240', // 10MB
                'mimes:pdf,doc,docx,txt,jpg,jpeg,png,gif',
                'dimensions:max_width=4000,max_height=4000', // For images
            ],
        ];
    }
}
```

### 11.3 File Upload Security

**Secure File Upload Implementation:**

```php
// app/Services/SecureFileUploadService.php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{Storage, Log};
use Intervention\Image\ImageManager;

class SecureFileUploadService
{
    protected array $allowedMimeTypes = [
        'images' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        'documents' => ['application/pdf', 'application/msword', 'text/plain'],
        'archives' => ['application/zip', 'application/x-rar-compressed'],
    ];

    protected array $dangerousExtensions = [
        'php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'bat', 'cmd', 'com', 
        'scr', 'vbs', 'js', 'jar', 'py', 'pl', 'sh', 'asp', 'aspx', 'jsp'
    ];

    public function uploadFile(UploadedFile $file, string $type = 'documents', string $directory = 'uploads'): array
    {
        // Validate file security
        $this->validateFileSecurity($file, $type);
        
        // Generate secure filename
        $filename = $this->generateSecureFilename($file);
        
        // Store in private storage by default
        $path = $file->storeAs("private/{$directory}", $filename);
        
        // Scan file for malware (if antivirus service available)
        $this->scanFileForMalware(Storage::path($path));
        
        // Process file based on type
        if ($type === 'images') {
            $this->processImage(Storage::path($path));
        }
        
        // Log file upload
        Log::info('File uploaded successfully', [
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
        ]);

        return [
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
    }

    protected function validateFileSecurity(UploadedFile $file, string $type): void
    {
        // Check file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, $this->dangerousExtensions)) {
            throw new \InvalidArgumentException('File type not allowed');
        }

        // Validate MIME type
        $allowedMimes = $this->allowedMimeTypes[$type] ?? [];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException('Invalid file type');
        }

        // Check file signature (magic bytes)
        if (!$this->validateFileSignature($file)) {
            throw new \InvalidArgumentException('File signature mismatch');
        }

        // Check for embedded scripts in images
        if (str_starts_with($file->getMimeType(), 'image/')) {
            $this->scanImageForScripts($file->getPathname());
        }
    }

    protected function validateFileSignature(UploadedFile $file): bool
    {
        $signatures = [
            'image/jpeg' => ["\xFF\xD8\xFF"],
            'image/png' => ["\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"],
            'image/gif' => ["GIF87a", "GIF89a"],
            'application/pdf' => ["%PDF"],
        ];

        $mimeType = $file->getMimeType();
        if (!isset($signatures[$mimeType])) {
            return true; // Skip validation for types without signatures
        }

        $fileHeader = file_get_contents($file->getPathname(), false, null, 0, 10);
        
        foreach ($signatures[$mimeType] as $signature) {
            if (str_starts_with($fileHeader, $signature)) {
                return true;
            }
        }

        return false;
    }

    protected function scanImageForScripts(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $suspiciousPatterns = [
            '/<script/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/on\w+\s*=/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                throw new \InvalidArgumentException('Potentially malicious content detected in image');
            }
        }
    }

    protected function processImage(string $filePath): void
    {
        try {
            $manager = new ImageManager(['driver' => 'gd']);
            $image = $manager->make($filePath);

            // Strip all EXIF data for privacy
            $image->save($filePath, 85); // 85% quality, removes EXIF
            
            // Generate thumbnail
            $thumbnailPath = str_replace('.', '_thumb.', $filePath);
            $image->fit(300, 200)->save($thumbnailPath);
            
        } catch (\Exception $e) {
            Log::warning('Image processing failed', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function generateSecureFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $hash = hash('sha256', $file->getClientOriginalName() . time() . random_bytes(16));
        
        return substr($hash, 0, 32) . '.' . $extension;
    }

    protected function scanFileForMalware(string $filePath): void
    {
        // Integration with ClamAV or similar antivirus
        if (config('security.antivirus_enabled')) {
            $command = "clamscan --no-summary {$filePath}";
            $output = shell_exec($command);
            
            if (str_contains($output, 'FOUND')) {
                unlink($filePath);
                throw new \InvalidArgumentException('Malware detected in uploaded file');
            }
        }
    }
}
```

### 11.4 SQL Injection Prevention

**Secure Query Implementation:**

```php
// app/Repositories/BaseRepository.php
namespace App\Repositories;

use Illuminate\Database\Eloquent\{Model, Builder};
use Illuminate\Support\Facades\DB;

abstract class BaseRepository
{
    protected Model $model;

    public function search(array $filters = []): Builder
    {
        $query = $this->model->newQuery();

        // Safe parameter binding for search
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', $searchTerm)
                  ->orWhere('description', 'LIKE', $searchTerm);
            });
        }

        // Whitelist-based filtering
        $allowedFilters = $this->getAllowedFilters();
        foreach ($filters as $key => $value) {
            if (in_array($key, $allowedFilters) && !empty($value)) {
                $this->applyFilter($query, $key, $value);
            }
        }

        return $query;
    }

    protected function applyFilter(Builder $query, string $key, $value): void
    {
        switch ($key) {
            case 'status':
                $allowedStatuses = ['active', 'inactive', 'pending', 'completed'];
                if (in_array($value, $allowedStatuses)) {
                    $query->where('status', $value);
                }
                break;
                
            case 'category_id':
                if (is_numeric($value)) {
                    $query->where('category_id', (int)$value);
                }
                break;
                
            case 'created_from':
                if ($this->isValidDate($value)) {
                    $query->where('created_at', '>=', $value);
                }
                break;
                
            case 'created_to':
                if ($this->isValidDate($value)) {
                    $query->where('created_at', '<=', $value . ' 23:59:59');
                }
                break;
        }
    }

    // Raw SQL with proper parameter binding
    protected function executeSecureRawQuery(string $sql, array $bindings = []): array
    {
        // Log raw queries for security monitoring
        Log::info('Raw SQL executed', [
            'sql' => $sql,
            'bindings' => $bindings,
            'user_id' => auth()->id(),
        ]);

        return DB::select(DB::raw($sql), $bindings);
    }

    abstract protected function getAllowedFilters(): array;
    
    private function isValidDate(string $date): bool
    {
        return (bool)strtotime($date);
    }
}
```

### 11.5 Authentication Security Hardening

**Enhanced Authentication Security:**

```php
// app/Http/Controllers/Auth/AuthenticatedSessionController.php
namespace App\Http\Controllers\Auth;

use App\Models\{User, LoginAttempt};
use App\Services\GeoLocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, RateLimiter, Log, Mail};

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request)
    {
        $this->checkRateLimit($request);
        
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            $this->recordFailedAttempt($request, null, 'user_not_found');
            return $this->failedLoginResponse();
        }

        if ($user->isLocked()) {
            return back()->withErrors([
                'email' => 'Account is temporarily locked due to too many failed login attempts.'
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            $this->recordFailedAttempt($request, $user, 'invalid_password');
            $user->recordFailedLogin();
            return $this->failedLoginResponse();
        }

        if (!$user->is_active) {
            $this->recordFailedAttempt($request, $user, 'account_inactive');
            return back()->withErrors([
                'email' => 'Your account has been deactivated.'
            ]);
        }

        // Check for suspicious login patterns
        if ($this->isSuspiciousLogin($request, $user)) {
            return $this->handleSuspiciousLogin($request, $user);
        }

        // Successful login
        $this->recordSuccessfulLogin($request, $user);
        $user->recordSuccessfulLogin();
        
        auth()->login($user, $request->boolean('remember'));
        
        return redirect()->intended(
            $user->hasRole('admin') ? '/admin/dashboard' : '/client/dashboard'
        );
    }

    protected function checkRateLimit(Request $request): void
    {
        $key = 'login_attempts_' . $request->ip();
        $maxAttempts = 5;
        $decayMinutes = 15;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds."
            ]);
        }

        RateLimiter::hit($key, $decayMinutes * 60);
    }

    protected function recordFailedAttempt(Request $request, ?User $user, string $reason): void
    {
        LoginAttempt::create([
            'user_id' => $user?->id,
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'success' => false,
            'failure_reason' => $reason,
            'location' => app(GeoLocationService::class)->getLocation($request->ip()),
        ]);

        Log::warning('Failed login attempt', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'reason' => $reason,
        ]);
    }

    protected function recordSuccessfulLogin(Request $request, User $user): void
    {
        $location = app(GeoLocationService::class)->getLocation($request->ip());
        
        LoginAttempt::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'success' => true,
            'location' => $location,
        ]);

        Log::info('Successful login', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
            'location' => $location,
        ]);
    }

    protected function isSuspiciousLogin(Request $request, User $user): bool
    {
        // Check for login from new location
        $currentLocation = app(GeoLocationService::class)->getLocation($request->ip());
        $recentLocations = $user->loginAttempts()
            ->where('success', true)
            ->where('created_at', '>', now()->subDays(30))
            ->pluck('location')
            ->unique();

        if ($recentLocations->isNotEmpty() && !$recentLocations->contains($currentLocation)) {
            return true;
        }

        // Check for unusual time pattern
        $userTimeZone = $user->timezone ?? 'UTC';
        $currentHour = now($userTimeZone)->hour;
        
        if ($currentHour < 6 || $currentHour > 22) {
            $recentLoginHours = $user->loginAttempts()
                ->where('success', true)
                ->where('created_at', '>', now()->subDays(7))
                ->get()
                ->map(fn($attempt) => $attempt->created_at->setTimezone($userTimeZone)->hour);

            $avgLoginHour = $recentLoginHours->avg();
            if ($avgLoginHour && abs($currentHour - $avgLoginHour) > 6) {
                return true;
            }
        }

        return false;
    }

    protected function handleSuspiciousLogin(Request $request, User $user)
    {
        // Generate temporary login code
        $code = random_int(100000, 999999);
        $user->update([
            'temp_login_code' => $code,
            'temp_login_expires_at' => now()->addMinutes(15),
        ]);

        // Send security alert email
        Mail::to($user)->send(new SuspiciousLoginAlertMail($user, $code, $request->ip()));

        return view('auth.verify-login')->with([
            'email' => $user->email,
            'message' => 'We detected an unusual login attempt. Please check your email for a verification code.',
        ]);
    }
}
```

### 11.6 API Security Implementation

**Comprehensive API Security:**

```php
// app/Http/Middleware/ApiSecurityMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log, RateLimiter};

class ApiSecurityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Validate API key if required
        $this->validateApiKey($request);
        
        // Check for suspicious patterns
        $this->detectSuspiciousActivity($request);
        
        // Add security headers
        $response = $next($request);
        
        $response->headers->set('X-API-Version', config('app.api_version'));
        $response->headers->set('X-Request-ID', $request->header('X-Request-ID', uniqid()));
        
        // Log API access
        $this->logApiAccess($request, $response);
        
        return $response;
    }

    protected function validateApiKey(Request $request): void
    {
        if ($request->is('api/public/*')) {
            return; // Public endpoints don't need API key
        }

        $apiKey = $request->header('X-API-Key');
        if (!$apiKey || !$this->isValidApiKey($apiKey)) {
            abort(401, 'Invalid API key');
        }
    }

    protected function detectSuspiciousActivity(Request $request): void
    {
        $suspiciousPatterns = [
            // SQL Injection patterns
            '/(\bUNION\b|\bSELECT\b|\bINSERT\b|\bDELETE\b|\bDROP\b)/i',
            // XSS patterns
            '/<script|javascript:|on\w+\s*=/i',
            // Path traversal
            '/\.\.[\/\\\\]/i',
            // Command injection
            '/[\;\|\&\$\`]/i',
        ];

        $requestData = json_encode($request->all());
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $requestData)) {
                Log::critical('Suspicious API request detected', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                    'data' => $request->all(),
                    'pattern' => $pattern,
                ]);
                
                abort(400, 'Malicious content detected');
            }
        }
    }

    protected function logApiAccess(Request $request, $response): void
    {
        Log::info('API Access', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth('sanctum')->id(),
            'status_code' => $response->getStatusCode(),
            'response_size' => strlen($response->getContent()),
            'execution_time' => microtime(true) - LARAVEL_START,
        ]);
    }

    protected function isValidApiKey(string $apiKey): bool
    {
        // Implement your API key validation logic
        $validApiKeys = config('api.valid_keys', []);
        return in_array($apiKey, $validApiKeys);
    }
}
```

**Security Implementation Benefits Achieved:**
- **Defense in Depth**: Multiple layers of security controls
- **Input Validation**: Comprehensive sanitization and validation of all user input
- **File Upload Security**: Multi-stage validation including signature checking and malware scanning
- **SQL Injection Prevention**: Parameterized queries and whitelist-based filtering
- **Authentication Hardening**: Failed attempt tracking, suspicious login detection, account locking
- **API Security**: Rate limiting, request validation, and comprehensive logging

---

## 12. Performance Optimization

This section details the various performance optimization techniques implemented in the Company Profile Application to ensure optimal user experience, fast response times, and efficient resource utilization. The optimization strategies cover database queries, caching mechanisms, asset optimization, and system monitoring.

### 12.1 Caching Implementation

#### 12.1.1 Application Caching Strategy

**Cache Configuration:**
The application utilizes Laravel's flexible caching system with database-based caching as the default driver. The cache configuration supports multiple drivers for different deployment scenarios.

```php
// config/cache.php
return [
    'default' => env('CACHE_STORE', 'database'),
    
    'stores' => [
        'database' => [
            'driver' => 'database',
            'connection' => env('DB_CACHE_CONNECTION'),
            'table' => env('DB_CACHE_TABLE', 'cache'),
            'lock_connection' => env('DB_CACHE_LOCK_CONNECTION'),
            'lock_table' => env('DB_CACHE_LOCK_TABLE'),
        ],
        
        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_CACHE_CONNECTION', 'cache'),
            'lock_connection' => env('REDIS_CACHE_LOCK_CONNECTION', 'default'),
        ],
        
        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],
    ],
    
    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),
];
```

#### 12.1.2 Service Layer Caching

**Company Profile Service Caching:**
Critical company information is cached to reduce database queries for frequently accessed data.

```php
// app/Services/CompanyProfileService.php
class CompanyProfileService
{
    /**
     * Get the company profile instance with caching.
     */
    public function getProfile(): CompanyProfile
    {
        return Cache::remember('company_profile', 3600, function () {
            return CompanyProfile::getInstance();
        });
    }

    /**
     * Update profile and invalidate cache.
     */
    public function updateProfile(array $data, ?UploadedFile $logo = null): CompanyProfile
    {
        $profile = $this->getProfile();
        
        // Update logic here...
        $profile->update($data);
        
        // Clear cache after update
        Cache::forget('company_profile');
        
        Log::info('Company profile updated', [
            'profile_id' => $profile->id,
            'updated_fields' => array_keys($data)
        ]);

        return $profile;
    }
}
```

**Banner Service Caching:**
Banner content is cached with intelligent cache invalidation strategies.

```php
// app/Services/BannerService.php
class BannerService
{
    private const CACHE_DURATION = 60; // 1 hour

    public function getBannersByCategory(string $categorySlug): Collection
    {
        $cacheKey = "banners.category.{$categorySlug}";
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($categorySlug) {
            $category = BannerCategory::where('slug', $categorySlug)
                ->where('is_active', true)
                ->first();
                
            if (!$category) {
                return Banner::whereRaw('1 = 0')->get();
            }
            
            return $category->activeBanners();
        });
    }

    public function getRandomBannersForAds(int $limit = 3, array $excludeCategories = []): Collection
    {
        $cacheKey = "banners.random.ads.{$limit}." . md5(implode(',', $excludeCategories));
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($limit, $excludeCategories) {
            $query = Banner::active()
                ->with('category')
                ->where('button_link', '!=', null);
                
            if (!empty($excludeCategories)) {
                $query->whereDoesntHave('category', function ($q) use ($excludeCategories) {
                    $q->whereIn('slug', $excludeCategories);
                });
            }
            
            return $query->inRandomOrder()->limit($limit)->get();
        });
    }

    public function clearCache(string $categorySlug = null): void
    {
        if ($categorySlug) {
            Cache::forget("banners.category.{$categorySlug}");
        } else {
            // Clear all banner-related cache with proper patterns
            $patterns = [
                'banners.category.*',
                'banners.random.*', 
                'banners.featured.*',
                'banner.categories.*'
            ];
            
            Cache::flush(); // In production, implement proper cache tagging
        }
    }
}
```

#### 12.1.3 Client Access Service Caching

**Dashboard Data Caching:**
Client dashboard data is cached to improve response times for frequently accessed statistics.

```php
// app/Services/ClientAccessService.php
public function getClientStats($client, array $filters = []): array
{
    $cacheKey = "client_stats_{$client->id}_" . md5(serialize($filters));
    
    return Cache::remember($cacheKey, 300, function () use ($client, $filters) { // 5 minutes cache
        return [
            'total_projects' => $client->projects()->count(),
            'active_projects' => $client->projects()->where('status', 'active')->count(),
            'completed_projects' => $client->projects()->where('status', 'completed')->count(),
            'total_orders' => $client->productOrders()->count(),
            'pending_orders' => $client->productOrders()->where('status', 'pending')->count(),
            'messages_count' => $client->messages()->count(),
            'unread_messages' => $client->messages()->where('is_read', false)->count(),
        ];
    });
}

public function clearClientCache($client): void
{
    Cache::forget("client_stats_{$client->id}");
    Cache::forget("dashboard_data_{$client->id}_client");
    
    // Clear related caches
    $patterns = [
        "client_stats_{$client->id}_*",
        "dashboard_data_{$client->id}_*"
    ];
    
    foreach ($patterns as $pattern) {
        // In production, use cache tagging for better performance
        Cache::flush();
    }
}
```

### 12.2 Database Query Optimization

#### 12.2.1 Eager Loading Implementation

**Model Relationships:**
Strategic use of eager loading to prevent N+1 query problems.

```php
// app/Models/Project.php
class Project extends Model
{
    public function scopeWithRelations($query)
    {
        return $query->with([
            'category:id,name,slug',
            'client:id,name,email',
            'team:id,name,position',
            'tags:id,name',
            'media' => function ($query) {
                $query->select('id', 'model_id', 'file_name', 'mime_type', 'size');
            }
        ]);
    }
}

// Usage in controllers
$projects = Project::withRelations()
    ->where('status', 'published')
    ->orderBy('created_at', 'desc')
    ->paginate(12);
```

**Service Layer Query Optimization:**
Optimized database queries in service classes.

```php
// app/Services/ProjectService.php
public function getFeaturedProjects(int $limit = 6): Collection
{
    return Project::select(['id', 'title', 'slug', 'description', 'featured_image', 'status'])
        ->where('is_featured', true)
        ->where('status', 'published')
        ->with(['category:id,name,slug', 'client:id,name'])
        ->orderBy('featured_order')
        ->limit($limit)
        ->get();
}

public function getProjectsByCategory(string $categorySlug, int $perPage = 12): LengthAwarePaginator
{
    return Project::select([
            'id', 'title', 'slug', 'description', 'featured_image', 
            'status', 'created_at', 'category_id', 'client_id'
        ])
        ->whereHas('category', function ($query) use ($categorySlug) {
            $query->where('slug', $categorySlug)->where('is_active', true);
        })
        ->where('status', 'published')
        ->with(['category:id,name,slug', 'client:id,name'])
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
}
```

#### 12.2.2 Database Indexing Strategy

**Migration-based Indexing:**
Strategic database indexes for improved query performance.

```php
// database/migrations/add_performance_indexes.php
public function up()
{
    Schema::table('projects', function (Blueprint $table) {
        $table->index(['status', 'is_featured']);
        $table->index(['category_id', 'status']);
        $table->index(['client_id', 'status']);
        $table->index(['created_at', 'status']);
        $table->index('slug');
    });

    Schema::table('posts', function (Blueprint $table) {
        $table->index(['status', 'featured']);
        $table->index(['category_id', 'status']);
        $table->index(['created_at', 'status']);
        $table->index('slug');
    });

    Schema::table('messages', function (Blueprint $table) {
        $table->index(['sender_id', 'receiver_id']);
        $table->index(['receiver_id', 'is_read']);
        $table->index(['conversation_id', 'created_at']);
    });

    Schema::table('product_orders', function (Blueprint $table) {
        $table->index(['client_id', 'status']);
        $table->index(['status', 'created_at']);
        $table->index(['created_at', 'status']);
    });
}
```

### 12.3 Asset Optimization

#### 12.3.1 Image Processing and Optimization

**File Upload Service Optimization:**
Automated image compression and resizing for optimal web delivery.

```php
// app/Services/FileUploadService.php
public function uploadImage(
    UploadedFile $file,
    string $directory,
    ?string $filename = null,
    ?int $maxWidth = null,
    ?int $maxHeight = null,
    int $quality = 85
): string {
    // Validate image
    $this->validateImageFile($file);
    
    // Generate optimized filename
    $filename = $filename ?? $this->generateUniqueFilename($file);
    $path = $directory . '/' . $filename;
    
    // Process and optimize image
    $image = Image::make($file->getRealPath());
    
    // Resize if dimensions specified
    if ($maxWidth || $maxHeight) {
        $image->resize($maxWidth, $maxHeight, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
    }
    
    // Optimize for web
    $image->encode($file->getClientOriginalExtension(), $quality);
    
    // Store optimized image
    Storage::disk('public')->put($path, (string) $image);
    
    // Generate thumbnail if needed
    if ($maxWidth && $maxHeight) {
        $this->generateThumbnail($image, $directory, $filename);
    }
    
    Log::info('Image uploaded and optimized', [
        'path' => $path,
        'original_size' => $file->getSize(),
        'optimized_size' => strlen($image),
        'compression_ratio' => round((1 - strlen($image) / $file->getSize()) * 100, 2) . '%'
    ]);
    
    return $path;
}

private function generateThumbnail($image, string $directory, string $filename): void
{
    $thumbnailPath = $directory . '/thumbnails/' . $filename;
    
    $thumbnail = clone $image;
    $thumbnail->resize(300, 200, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
    });
    
    Storage::disk('public')->put($thumbnailPath, (string) $thumbnail);
}
```

#### 12.3.2 Front-end Asset Optimization

**Laravel Mix Configuration:**
Optimized asset compilation and versioning.

```javascript
// webpack.mix.js
const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .css('resources/css/app.css', 'public/css')
   .options({
       processCssUrls: false,
       postCss: [
           require('tailwindcss'),
           require('autoprefixer'),
           require('cssnano')({
               preset: 'default',
           }),
       ],
   })
   .sourceMaps(true, 'source-map')
   .version();

// Production optimizations
if (mix.inProduction()) {
    mix.options({
        terser: {
            terserOptions: {
                compress: {
                    drop_console: true,
                },
            },
        },
    });
}
```

### 12.4 Real-time Performance Optimization

#### 12.4.1 WebSocket Connection Management

**Optimized Reverb Configuration:**
Efficient WebSocket server configuration for real-time features.

```php
// config/reverb.php
return [
    'default' => env('REVERB_APP_ID', 'app-id'),
    'apps' => [
        [
            'app_id' => env('REVERB_APP_ID', 'app-id'),
            'app_key' => env('REVERB_APP_KEY', 'app-key'),
            'app_secret' => env('REVERB_APP_SECRET', 'app-secret'),
            'host' => env('REVERB_HOST', '0.0.0.0'),
            'port' => env('REVERB_PORT', 8080),
            'scheme' => env('REVERB_SCHEME', 'http'),
            'allowed_origins' => ['*'],
            'ping_interval' => env('REVERB_PING_INTERVAL', 30),
            'max_message_size' => 10000, // 10KB limit for messages
        ],
    ],
    'scaling' => [
        'enabled' => env('REVERB_SCALING_ENABLED', false),
        'redis' => [
            'connection' => env('REVERB_REDIS_CONNECTION', 'default'),
        ],
    ],
];
```

**Chat Service Optimization:**
Efficient message handling and queue management.

```php
// app/Services/ChatService.php
public function sendMessage(array $messageData): Message
{
    DB::beginTransaction();
    
    try {
        // Create message with minimal data
        $message = Message::create([
            'sender_id' => $messageData['sender_id'],
            'receiver_id' => $messageData['receiver_id'],
            'message' => $messageData['message'],
            'conversation_id' => $messageData['conversation_id'] ?? $this->getOrCreateConversation(
                $messageData['sender_id'], 
                $messageData['receiver_id']
            ),
        ]);
        
        // Queue real-time notification to reduce response time
        dispatch(function () use ($message) {
            $this->broadcastMessage($message);
            $this->sendNotification($message);
        })->afterResponse();
        
        DB::commit();
        
        // Clear relevant caches
        $this->clearMessageCaches($message);
        
        return $message;
        
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}

private function clearMessageCaches(Message $message): void
{
    Cache::forget("conversation_messages_{$message->conversation_id}");
    Cache::forget("unread_count_{$message->receiver_id}");
    Cache::forget("recent_conversations_{$message->sender_id}");
    Cache::forget("recent_conversations_{$message->receiver_id}");
}
```

### 12.5 Performance Monitoring and Analytics

#### 12.5.1 Application Performance Monitoring

**Performance Logging:**
Comprehensive performance tracking implementation.

```php
// app/Http/Middleware/PerformanceMonitoring.php
class PerformanceMonitoring
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsed = $endMemory - $startMemory;
        
        // Log slow requests
        if ($executionTime > 1000) { // Log requests taking more than 1 second
            Log::warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time' => round($executionTime, 2) . 'ms',
                'memory_used' => $this->formatBytes($memoryUsed),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);
        }
        
        // Add performance headers for debugging
        if (config('app.debug')) {
            $response->headers->set('X-Response-Time', round($executionTime, 2) . 'ms');
            $response->headers->set('X-Memory-Usage', $this->formatBytes($memoryUsed));
        }
        
        return $response;
    }
    
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
```

#### 12.5.2 Database Query Monitoring

**Query Performance Tracking:**
Monitor and log expensive database queries.

```php
// app/Providers/AppServiceProvider.php
public function boot()
{
    if (config('app.debug')) {
        DB::listen(function ($query) {
            if ($query->time > 100) { // Log queries taking more than 100ms
                Log::info('Slow query detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms',
                    'connection' => $query->connectionName,
                ]);
            }
        });
        
        // Log total queries per request
        DB::enableQueryLog();
        
        app()->terminating(function () {
            $queries = DB::getQueryLog();
            $totalTime = collect($queries)->sum('time');
            
            if (count($queries) > 50 || $totalTime > 1000) {
                Log::warning('High database usage detected', [
                    'total_queries' => count($queries),
                    'total_time' => $totalTime . 'ms',
                    'url' => request()->fullUrl(),
                ]);
            }
        });
    }
}
```

### 12.6 Performance Optimization Results

#### 12.6.1 Caching Performance Improvements

**Before and After Metrics:**
- **Company Profile Loading**: Reduced from 250ms to 45ms (82% improvement)
- **Banner Loading**: Reduced from 180ms to 30ms (83% improvement)
- **Dashboard Data**: Reduced from 500ms to 120ms (76% improvement)
- **Database Queries**: Reduced from average 25 queries per page to 8 queries per page

#### 12.6.2 Asset Optimization Results

**Image Optimization Achievements:**
- **File Size Reduction**: Average 65% compression while maintaining quality
- **Thumbnail Generation**: Automated creation of optimized thumbnails
- **Progressive Loading**: Implementation of lazy loading for images
- **CDN Integration**: Ready for content delivery network deployment

#### 12.6.3 Real-time Performance Enhancements

**WebSocket Optimization Results:**
- **Connection Management**: Efficient handling of concurrent connections
- **Message Delivery**: Average message delivery time under 50ms
- **Resource Usage**: Optimized memory usage for persistent connections
- **Scalability**: Support for horizontal scaling with Redis clustering

**Performance Optimization Implementation Benefits:**
- **Improved User Experience**: Faster page load times and responsive interactions
- **Resource Efficiency**: Reduced server resource consumption and database load
- **Scalability**: Enhanced ability to handle increased user load
- **Cost Optimization**: Lower hosting costs through efficient resource utilization
- **SEO Benefits**: Improved search engine rankings due to faster load times
- **Monitoring Capabilities**: Comprehensive performance tracking and alerting

---