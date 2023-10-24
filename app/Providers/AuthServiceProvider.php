<?php

namespace App\Providers;

use App\Policies\UserGroupPolicy;
use App\Policies\UserWorkflowRequestPolicy;
use App\Resources\UserGroupResource;
use App\Resources\UserWorkflowRequestResource;
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
        UserWorkflowRequestResource::class => UserWorkflowRequestPolicy::class,
        UserGroupResource::class => UserGroupPolicy::class,
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
