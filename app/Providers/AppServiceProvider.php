<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Morph map for polymorphic relations.
        Relation::morphMap([
            'permissions' => 'App\Models\Permissions\Permission',
            'roles' => 'App\Models\Permissions\Role',            
            'users' => 'App\Models\User',
            'addresses' => 'App\Models\Address',            
            'cms_posts' => 'App\Models\Cms\Post',
            'cms_pages' => 'App\Models\Cms\Page',
            'cms_blog_posts' => 'App\Models\Cms\BlogPost',
            // 'cms_services' => 'App\Models\Cms\Services',
            // 'cms_products' => 'App\Models\Cms\Products',            
            // 'cms_lead_magnets' => 'App\Models\Cms\LeadMagnet',
            // 'cms_courses' => 'App\Models\Cms\Course',
            // 'cms_calendar_events' => 'App\Models\Cms\CalendarEvent',
            // 'cms_userful_external_links' => 'App\Models\Cms\UsefulExternalLink',
            // 'cms_portfolio_posts' => 'App\Models\Cms\PortfolioPost',
            // 'cms_testimonial' => 'App\Models\Cms\Testimonial',
            // 'cms_partner' => 'App\Models\Cms\Partner',
            // 'cms_team_members' => 'App\Models\Cms\TeamMember',
            'cms_post_categories' => 'App\Models\Cms\PostCategory',
            'cms_post_sliders' => 'App\Models\Cms\PostSlider',
            'cms_post_subcontents' => 'App\Models\Cms\PostSlider',
        ]);
    }
}
