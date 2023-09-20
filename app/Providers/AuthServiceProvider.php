<?php

namespace App\Providers;

use App\Models;
use App\Policies;
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
        Models\Permissions\Permission::class => Policies\Permissions\PermissionPolicy::class,
        Models\Permissions\Role::class       => Policies\Permissions\RolePolicy::class,
        Models\User::class                   => Policies\UserPolicy::class,

        Models\Cms\Page::class          => Policies\Cms\PagePolicy::class,
        Models\Cms\BlogPost::class      => Policies\Cms\BlogPostPolicy::class,
        Models\Cms\Product::class       => Policies\Cms\ProductPolicy::class,
        Models\Cms\Service::class       => Policies\Cms\ServicePolicy::class,
        Models\Cms\PortfolioPost::class => Policies\Cms\PortfolioPostPolicy::class,
        Models\Cms\Testimonial::class   => Policies\Cms\TestimonialPolicy::class,
        Models\Cms\Partner::class       => Policies\Cms\PartnerPolicy::class,
        Models\Cms\TeamMember::class    => Policies\Cms\TeamMemberPolicy::class,
        Models\Cms\BlogPost::class      => Policies\Cms\BlogPostPolicy::class,

        Models\Cms\PostSlider::class    => Policies\Cms\MainPostSliderPolicy::class,
        Models\Cms\PostCategory::class  => Policies\Cms\PostCategoryPolicy::class,

        Models\Shop\ProductCategory::class => Policies\Shop\ProductCategoryPolicy::class,
        Models\Shop\ProductBrand::class    => Policies\Shop\ProductBrandPolicy::class,
        Models\Shop\Product::class         => Policies\Shop\ProductPolicy::class,

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
