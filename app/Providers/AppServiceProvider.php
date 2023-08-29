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
            // 'cms-services' => 'App\Models\Cms\Services',
            // 'cms-products' => 'App\Models\Cms\Products',            
            // 'cms-rich-contents' => 'App\Models\Cms\RichContent',
            // 'cms-courses' => 'App\Models\Cms\Course',
            // 'cms-calendar-events' => 'App\Models\Cms\CalendarEvent',
            // 'cms-userful-external-links' => 'App\Models\Cms\UsefulExternalLink',
            // 'cms-portfolio-posts' => 'App\Models\Cms\PortfolioPost',
            // 'cms-testimonial' => 'App\Models\Cms\Testimonial',
            // 'cms-partner' => 'App\Models\Cms\Partner',
            // 'cms-team-members' => 'App\Models\Cms\TeamMember',
            'cms-post-categories' => 'App\Models\Cms\PostCategory',
        ]);
    }
}
