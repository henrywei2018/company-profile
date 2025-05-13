<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;
use App\Policies\ProjectPolicy;
use App\Policies\QuotationPolicy;
use App\Policies\MessagePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Project::class => ProjectPolicy::class,
        Quotation::class => QuotationPolicy::class,
        Message::class => MessagePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for admin access
        Gate::define('access-admin', function ($user) {
            return $user->hasRole('admin');
        });

        // Define gates for client access
        Gate::define('access-client', function ($user) {
            return $user->hasRole('client') && $user->is_active;
        });
    }
}