<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\Models\User;
use App\Policies\UserPolicy;

use App\Models\Organisation;
use App\Policies\OrganisationPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Organisation::class => OrganisationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Explicitly define authorization rules via Gate (Optional but Recommended)
        // Gate::define('create-user', [UserPolicy::class, 'create']);
        // Gate::define('update-user', [UserPolicy::class, 'update']);
        // Gate::define('delete-user', [UserPolicy::class, 'delete']);

        // Can optionally define Organisation gates here if needed
        // Gate::define('create-organisation', [OrganisationPolicy::class, 'create']);
        // Gate::define('update-organisation', [OrganisationPolicy::class, 'update']);
        // Gate::define('delete-organisation', [OrganisationPolicy::class, 'delete']);

        // Note:
        // Don’t need to define gates for Organisation if I am using authorize() or can() methods directly in controllers. 
        // But here included if we decide to hook them into components like Blade directives or non-resource-based checks.
    }
}