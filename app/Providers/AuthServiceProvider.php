<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Permissions\Permission;
use App\Models\Permissions\Role;
use App\Models\User;
use App\Policies\Permissions\PermissionPolicy;
use App\Policies\Permissions\RolePolicy;
use App\Policies\UserPolicy;
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
        Permission::class => PermissionPolicy::class,
        Role::class => RolePolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Implicitly grant "Superadmin" role all permissions
        Gate::after(function ($user, $ability) {
            return $user->hasRole('Superadministrador') ? true : null;
        });
    }
}
