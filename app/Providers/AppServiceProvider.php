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
        // // Create storage folder
        // // utilizar apenas no ambiente de produção em host compartilhado.
        // if (!file_exists('storage')) {

        //     \App::make('files')->link(storage_path('app/public'), 'storage');
        // }

        // // Public Path
        // // utilizar apenas no ambiente de produção em host compartilhado.
        // app()->usePublicPath(realpath(base_path() . '/..'));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Morph map for polymorphic relations.
        Relation::morphMap([
            'permissions' => 'App\Models\Permissions\Permission',
            'roles'       => 'App\Models\Permissions\Role',
            'users'       => 'App\Models\User',
            'addresses'   => 'App\Models\Address',

            'cms_posts'                 => 'App\Models\Cms\Post',
            'cms_pages'                 => 'App\Models\Cms\Page',
            'cms_blog_posts'            => 'App\Models\Cms\BlogPost',
            'cms_products'              => 'App\Models\Cms\Product',
            'cms_services'              => 'App\Models\Cms\Service',
            'cms_portfolio_posts'       => 'App\Models\Cms\PortfolioPost',
            'cms_testimonial'           => 'App\Models\Cms\Testimonial',
            'cms_partner'               => 'App\Models\Cms\Partner',
            'cms_team_members'          => 'App\Models\Cms\TeamMember',
            'cms_external_useful_links' => 'App\Models\Cms\ExternalUsefulLink',
            // 'cms_calendar_events'       => 'App\Models\Cms\CalendarEvent',
            // 'cms_lead_magnets'          => 'App\Models\Cms\LeadMagnet',
            'cms_post_categories'       => 'App\Models\Cms\PostCategory',
            'cms_post_sliders'          => 'App\Models\Cms\PostSlider',
            'cms_post_subcontents'      => 'App\Models\Cms\PostSubcontent',

            'shop_product_categories'      => 'App\Models\Shop\ProductCategory',
            'shop_product_brands'          => 'App\Models\Shop\ProductBrand',
            'shop_products'                => 'App\Models\Shop\Product',
            'shop_product_variant_options' => 'App\Models\Shop\ProductVariantOption',
            'shop_product_variant_items'   => 'App\Models\Shop\ProductVariantItem',
            'shop_product_inventory_items' => 'App\Models\Shop\ProductInventoryItem',
            'shop_inventory_activities'    => 'App\Models\Shop\InventoryActivity',

            'crm_funnels'                 => 'App\Models\Crm\Funnels\Funnel',
            'crm_business_funnels'        => 'App\Models\Crm\Funnels\BusinessFunnel',
            'crm_contact_funnels'         => 'App\Models\Crm\Funnels\ContactFunnel',
            'crm_funnel_stages'           => 'App\Models\Crm\Funnels\FunnelStage',
            'crm_model_has_funnel_stages' => 'App\Models\Crm\Funnels\ModelHasFunnelStage',
            'crm_contact_sources'         => 'App\Models\Crm\Contacts\Source',
            'crm_contact_roles'           => 'App\Models\Crm\Contacts\Role',
            'crm_contacts'                => 'App\Models\Crm\Contacts\Contact',
            'crm_contact_individuals'     => 'App\Models\Crm\Contacts\Individual',
            'crm_contact_legal_entities'  => 'App\Models\Crm\Contacts\LegalEntity',

            'business'                 => 'App\Models\Business\Business',
            'shop_business'            => 'App\Models\Business\ShopBusiness',
            'business_traded_items'    => 'App\Models\Business\TradedItem',
            'business_payment_methods' => 'App\Models\Business\PaymentMethod',
        ]);
    }
}
