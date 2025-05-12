<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use App\Filament\Publisher\Pages\Register;
use Filament\Http\Middleware\Authenticate;
use Filament\FontProviders\GoogleFontProvider;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\RedirectToProperPanelMiddleware;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class PublisherPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('publisher')
            ->spa()
            ->path('')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->login()
            ->registration(Register::class)
            ->theme(asset('css/filament/admin/theme.css'))
            ->darkMode(false)
            ->font("Inter", provider: GoogleFontProvider::class)
            ->discoverResources(in: app_path('Filament/Publisher/Resources'), for: 'App\\Filament\\Publisher\\Resources')
            ->discoverPages(in: app_path('Filament/Publisher/Pages'), for: 'App\\Filament\\Publisher\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Publisher/Widgets'), for: 'App\\Filament\\Publisher\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
                RedirectToProperPanelMiddleware::class,
                Authenticate::class,
            ]);
    }
}
