<?php

namespace App\Providers\Filament;

use App\Filament\AvatarProviders\BoringAvatarsProvider;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Resources\Cms;
use App\Filament\Resources\Crm;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation;
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
                'profile' => Navigation\MenuItem::make()->label('Meu Perfil'),
                Navigation\MenuItem::make()
                    ->label('Website')
                    ->url('/')
                    ->icon('heroicon-o-globe-alt'),
                'logout' => Navigation\MenuItem::make()->label('Sair'),
            ])
            // ->defaultAvatarProvider(BoringAvatarsProvider::class)
            ->colors([
                // 'primary' => Color::Amber,
                'primary' => Color::Purple,
            ])
            ->favicon(url: asset('images/filament/favicon.ico'))
            ->navigationGroups([
                Navigation\NavigationGroup::make()
                    ->label('CRM'),
                Navigation\NavigationGroup::make()
                    ->label('Loja'),
                Navigation\NavigationGroup::make()
                    ->label('CMS & Marketing'),
                Navigation\NavigationGroup::make()
                    ->label('Sistema'),
            ])
            // ->navigationItems([
            //     NavigationItem::make('cms.products')
            //         ->label(fn (): string => Cms\ProductResource::getNavigationLabel())
            //         ->group(fn (): string => Cms\ProductResource::getNavigationGroup())
            //         ->sort(fn (): string => Cms\ProductResource::getNavigationSort())
            //         ->icon(fn (): string => Cms\ProductResource::getNavigationIcon())
            //         ->url(fn (): string => Cms\ProductResource::getUrl())
            //         ->isActiveWhen(fn () => request()->routeIs('filament.i2c-admin.resources.cms.products.index'))
            //         ->visible(fn(): bool => auth()->user()->can('Visualizar [Cms] Produtos')),
            //     NavigationItem::make('cms.services')
            //         ->label(fn (): string => Cms\ServiceResource::getNavigationLabel())
            //         ->group(fn (): string => Cms\ServiceResource::getNavigationGroup())
            //         ->sort(fn (): string => Cms\ServiceResource::getNavigationSort())
            //         ->icon(fn (): string => Cms\ServiceResource::getNavigationIcon())
            //         ->url(fn (): string => Cms\ServiceResource::getUrl())
            //         ->isActiveWhen(fn () => request()->routeIs('filament.i2c-admin.resources.cms.services.index'))
            //         ->visible(fn(): bool => auth()->user()->can('Visualizar [Cms] Serviços')),
            // ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->renderHook(
                'panels::body.end',
                fn () => view('vendor.filament-panels.components.footer'),
            )
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
