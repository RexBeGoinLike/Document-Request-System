<?php

namespace App\Providers\Filament;

use EightCedars\FilamentInactivityGuard\FilamentInactivityGuardPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;

class GuestPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->plugins([

                ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                function (): string {
                    $imageUrl = asset('css/images/guest_bg.jpg');

                    return Blade::render(<<<HTML
                        <style>
                            .fi-body{
                                background-image:
                                linear-gradient(rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0.6)),
                                url('{$imageUrl}');
                                background-repeat: no-repeat;
                                background-position: center;
                                background-attachment: fixed;
                                opacity: 1;
                            }
                        </style>
                    HTML);
                },
            )
            ->renderHook(PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn (): string => Blade::render('@livewire(\'request-tracker\')'))
            ->id('guest')
            ->path('portal')
            ->viteTheme('resources/css/filament/guest/theme.css')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Guest/Resources'), for: 'App\Filament\Guest\Resources')
            ->discoverPages(in: app_path('Filament/Guest/Pages'), for: 'App\Filament\Guest\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Guest/Widgets'), for: 'App\Filament\Guest\Widgets')
            ->widgets([])
            ->favicon(asset('css/images/pma_logo.svg'))
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
            ])
            ->globalSearch(false)
            ->brandName('OFFICE OF THE REGISTRAR')
            ->brandLogo(asset('css/images/pma_logo.svg'))
            ->brandLogoHeight('3rem')
            ->navigation(false)
            ->darkMode(false)
            ;
    }
}
