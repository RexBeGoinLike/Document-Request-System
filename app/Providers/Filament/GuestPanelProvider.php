<?php

namespace App\Providers\Filament;

use App\Filament\Guest\Resources\Tickets\Pages\CreateTicket;
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
use Illuminate\Contracts\View\View;
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
            //Inactivity Guard (AI to tol, good luck sa nagbabasa neto)
            // Add niyo ko sa IG @sirekks.
            // Shoutout kay Andrei Joshua Seguirre, tangina mo dre
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render('
        <!-- Inactivity Modal Overlay -->
        <div id="idle-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 99999; align-items: center; justify-content: center; font-family: sans-serif;">
            <div style="background: white; padding: 30px; border-radius: 12px; text-align: center; max-width: 400px; width: 90%;">
                <h2 style="font-size: 24px; font-weight: bold; color: #1f2937; margin-bottom: 10px;">Are you still there?</h2>
                <p style="color: #4b5563; margin-bottom: 20px;">To protect your privacy, this form will reset in <span id="idle-countdown" style="font-weight: bold; color: #dc2626;">30</span> seconds.</p>
                <button onclick="keepAlive()" style="background: #3b82f6; color: white; padding: 12px 24px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; width: 100%;">Continue Request</button>
            </div>
        </div>

        <script>
            const IDLE_TIMEOUT = 180; // 3 minutes of silence before warning
            const WARNING_TIMEOUT = 30; // 30 seconds to respond to warning
            const REDIRECT_URL = "/portal"; // Safe landing page

            let idleSecondsCounter = 0;
            let warningSecondsCounter = 0;
            let isWarningActive = false;
            let checkInterval;

            function isWizardOnFirstStep() {
                const panels = document.querySelectorAll(".fi-sc-wizard-step");
                if (panels.length === 0) return false;

                for (let i = 0; i < panels.length; i++) {
                    if (panels[i].classList.contains("fi-active")) {
                        return i === 0;
                    }
                }
                return false;
            }

            function resetIdleTimer() {
                if (!isWarningActive) {
                    idleSecondsCounter = 0;
                }
            }

            // Listen for physical kiosk interactions
            window.onload = resetIdleTimer;
            document.onmousemove = resetIdleTimer;
            document.onkeypress = resetIdleTimer;
            document.ontouchstart = resetIdleTimer;

            function startKioskMonitor() {
                checkInterval = setInterval(() => {
                    if (isWizardOnFirstStep()) {
                        return;
                    }

                    if (!isWarningActive) {
                        idleSecondsCounter++;
                        if (idleSecondsCounter >= IDLE_TIMEOUT) {
                            showWarning();
                        }
                    } else {
                        warningSecondsCounter--;
                        document.getElementById("idle-countdown").innerText = warningSecondsCounter;
                        if (warningSecondsCounter <= 0) {
                            triggerReset();
                        }
                    }
                }, 1000);
            }

            function showWarning() {
                isWarningActive = true;
                warningSecondsCounter = WARNING_TIMEOUT;
                document.getElementById("idle-countdown").innerText = WARNING_TIMEOUT;
                document.getElementById("idle-modal").style.display = "flex";
            }

            function keepAlive() {
                isWarningActive = false;
                idleSecondsCounter = 0;
                document.getElementById("idle-modal").style.display = "none";
            }

            function triggerReset() {
                clearInterval(checkInterval);
                window.location.href = REDIRECT_URL;
            }

            startKioskMonitor();
        </script>
    '),
            ) //TIMEOUT
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
            ) //BACKGROUND
            ->renderHook(PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn (): string => Blade::render('@livewire(\'request-tracker\')')) //TRACK REQUEST
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
