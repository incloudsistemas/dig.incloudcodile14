<?php

namespace App\Providers\Filament;

use App\Filament\AvatarProviders\BoringAvatarsProvider;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Categories\CmsPostCategoryResource;
use App\Models\Category;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('i2c-admin')
            ->path('i2c-admin')
            ->login()
            ->passwordReset()
            ->profile(EditProfile::class)
            ->userMenuItems([
                'profile' => MenuItem::make()->label('Meu Perfil'),
                MenuItem::make()
                ->label('Website')
                ->url('/')
                ->icon('heroicon-o-globe-alt'),
                'logout' => MenuItem::make()->label('Sair'),
            ])
            // ->defaultAvatarProvider(BoringAvatarsProvider::class)
            ->colors([
                // 'primary' => Color::Amber,
                'primary' => Color::Purple,
            ])
            ->favicon(url: asset('images/filament/favicon.ico'))
            ->navigationGroups([
                NavigationGroup::make()
                     ->label('Shop'),
                NavigationGroup::make()
                     ->label('CMS & Marketing'),
                NavigationGroup::make()
                     ->label('Sistema'),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
