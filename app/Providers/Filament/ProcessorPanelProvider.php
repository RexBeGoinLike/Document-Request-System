<?php

namespace App\Providers\Filament;

use App\Filament\Processor\Pages\ActivityLogs;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ProcessorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('processor')
            ->path('processor')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Processor/Resources'), for: 'App\Filament\Processor\Resources')
            ->discoverPages(in: app_path('Filament/Processor/Pages'), for: 'App\Filament\Processor\Pages')
            ->pages([
                ActivityLogs::class
            ])
            ->discoverWidgets(in: app_path('Filament/Processor/Widgets'), for: 'App\Filament\Processor\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
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
                Authenticate::class,
                AuthenticateSession::class,
            ])
            ->topNavigation()
            ->navigation(fn () => auth()->user()->role === 'admin')
            ->globalSearch(false)
            ->favicon(asset('css/images/pma_logo.svg'))
            ->brandName('OFFICE OF THE REGISTRAR')
            ->brandLogo(asset('css/images/pma_logo.svg'))
            ->brandLogoHeight('3rem')
            ->databaseNotifications()
            ->login();
    }

}
