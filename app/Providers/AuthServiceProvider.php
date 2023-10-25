<?php

namespace App\Providers;

use App\Policies\UserArchivePolicy;
use App\Policies\UserGroupPolicy;
use App\Policies\UserWorkflowPolicy;
use App\Resources\UserArchiveResource;
use App\Resources\UserGroupResource;
use App\Resources\UserWorkflowResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        UserArchiveResource::class => UserArchivePolicy::class,
        UserGroupResource::class => UserGroupPolicy::class,
        UserWorkflowResource::class => UserWorkflowPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(
            function ($user, $ability) {
                return $user->hasRole('Super admin') ? true : null;
            }
        );
    }
}
